<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    private string $apiKey;

    private string $model = 'claude-sonnet-4-6';

    private string $baseUrl = 'https://api.anthropic.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.api_key');
    }

    public function analyzeContentForReferences(string $content, array $existingCharacters, array $existingLocations, array $existingReferences): array
    {
        $systemPrompt = $this->buildSystemPrompt($existingCharacters, $existingLocations, $existingReferences);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl.'/messages', [
                'model' => $this->model,
                'max_tokens' => 1024,
                'system' => [
                    [
                        'type' => 'text',
                        'text' => $systemPrompt,
                        'cache_control' => ['type' => 'ephemeral'],
                    ],
                ],
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Analyze this paragraph:\n\n{$content}",
                    ],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();

                return $this->parseResponse($result['content'][0]['text'] ?? '');
            }

            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->emptyResult();
        } catch (\Exception $e) {
            Log::error('Anthropic API exception', [
                'message' => $e->getMessage(),
            ]);

            return $this->emptyResult();
        }
    }

    private function buildSystemPrompt(array $characters, array $locations, array $references): string
    {
        $charactersList = collect($characters)->map(fn ($c) => "- {$c['name']}")->join("\n");
        $locationsList = collect($locations)->map(fn ($l) => "- {$l['name']}")->join("\n");
        $referencesList = collect($references)->map(fn ($r) => "- {$r['title']}")->join("\n");

        return <<<PROMPT
You are a writing assistant analyzing a paragraph from a story. Your job is to identify:
1. Which existing characters, locations, or references are mentioned in the paragraph
2. Any NEW characters or locations that appear but aren't in the existing lists

EXISTING CHARACTERS:
{$charactersList}

EXISTING LOCATIONS:
{$locationsList}

EXISTING REFERENCES:
{$referencesList}

Respond ONLY with valid JSON in this exact format (no markdown, no code blocks):
{
  "mentioned_characters": ["character name", "another character"],
  "mentioned_locations": ["location name"],
  "mentioned_references": ["reference title"],
  "new_characters": [{"name": "New Character Name", "description": "Brief description from context"}],
  "new_locations": [{"name": "New Location Name", "description": "Brief description from context"}]
}

Rules:
- Only include items that are clearly present in the paragraph
- Use exact names from the existing lists when matching
- For new items, provide a brief description based on context in the paragraph
- If nothing is found in a category, use an empty array []
- Character names should be proper nouns (e.g., "John", "Sarah")
- Location names should be specific places (e.g., "Dark Forest", "Castle")
PROMPT;
    }

    private function parseResponse(string $responseText): array
    {
        // Remove markdown code blocks if present
        $cleaned = preg_replace('/```json\s*|\s*```/', '', $responseText);
        $cleaned = trim($cleaned);

        try {
            $data = json_decode($cleaned, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return [
                    'mentioned_characters' => $data['mentioned_characters'] ?? [],
                    'mentioned_locations' => $data['mentioned_locations'] ?? [],
                    'mentioned_references' => $data['mentioned_references'] ?? [],
                    'new_characters' => $data['new_characters'] ?? [],
                    'new_locations' => $data['new_locations'] ?? [],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse Anthropic response', [
                'response' => $responseText,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->emptyResult();
    }

    private function emptyResult(): array
    {
        return [
            'mentioned_characters' => [],
            'mentioned_locations' => [],
            'mentioned_references' => [],
            'new_characters' => [],
            'new_locations' => [],
        ];
    }

    public function getGrammarSuggestions(string $content, string $grammarPrompt): array
    {
        // Use custom prompt from database if provided, otherwise use default line editor prompt
        if (! empty($grammarPrompt)) {
            $systemPrompt = $grammarPrompt;
        } else {
            $systemPrompt = <<<'PROMPT'
You are a line editor for the author's novel. Your job is to help with grammar, syntax, punctuation, spelling, word choice, and clarity, and to make light, voice-preserving edits that sharpen a paragraph's impact. You are not a co-writer.

Hard rules:
1. Never add new content: no new images, descriptions, dialogue, actions, emotions, worldbuilding or plot. Every idea in the rewrite must already exist in the author's text.
2. Never cut a sentence or an idea. You may trim a redundant word or a weak hedge as an "enhancement" when doing so makes the paragraph land harder, but the meaning must stay the same.
3. The rewrite applies the fixes marked "error", "clarity" and "enhancement". Everything else stays exactly as the author wrote it. "Optional" suggestions are never applied.
4. Keep the author's voice. An edit is only acceptable if a reader would believe the author wrote it. If an edit makes a sentence sound more "polished" but less like the author, mark it "optional" or leave it out.
5. If a paragraph has no real problems, say so. Do not invent suggestions to look useful.

Enhancements (what's allowed):
An "enhancement" is a small edit that increases a paragraph's impact or makes a concept come across more clearly, using the author's own material.

Allowed:
• Swapping a weak or vague word for a stronger, more precise one in the same register ("moved quickly" → "rushed").
• Trimming a filler word or hedge that blunts a key moment ("Essentially," "somehow," "just," "very") when it weakens the beat.
• Reordering words within a sentence so the most important word lands at the end, or so the cause comes before the effect.
• Clarifying how a piece of magic or worldbuilding works when the current wording is ambiguous, using only what the text already says.
• Adjusting punctuation for rhythm (a dash or a period for a dramatic pause).

Limits:
• Each enhancement changes a phrase, not a whole sentence. If an improvement would require rewriting more than half a sentence, mark it "optional" instead and don't apply it.
• Max 4 enhancements per paragraph. Prefer fewer: only make the ones that clearly matter.
• Never enhance away a protected voice trait (below) unless it is actively confusing the reader.

What to look for:
• Typos and misspellings ("oh him" → "of him", "wold" → "would").
• Wrong word forms ("causing a released" → "causing a release").
• Countable/uncountable errors ("how much pieces" → "how many pieces").
• Prepositions and idioms that don't sound natural in English ("With the corner of his eyes" → "Out of the corner of his eye"; "replaced for" → "replaced with"; "lighted" → "lit").
• Continuity and clarity errors: wrong character named, unclear pronouns, unfinished clauses. These are high priority.
• Inconsistent capitalization of world terms (Wood Mage / wood mage, Lance / lance). Don't pick a form yourself. Ask in "questions" and leave the text unchanged in the rewrite.
• Comma splices that hurt clarity, odd mid-sentence capitals ("Kill" → "kill"), and dialogue punctuation ("wait! you know me" → "wait! You know me").
• Moments where the paragraph's key beat is muffled by filler, weak verbs, or word order (candidates for "enhancement").

Output format:
Respond with ONLY a single valid JSON object. No markdown, no code fences, no text before or after it. The object has exactly two top-level keys, "feedback" and "rewrite", with this exact structure:

{
  "feedback": {
    "summary": string,
    "has_changes": boolean,
    "changes": [
      {
        "id": integer,
        "type": "error" | "clarity" | "enhancement" | "optional",
        "category": "spelling" | "grammar" | "punctuation" | "word_choice" | "idiom" | "capitalization" | "continuity" | "dialogue" | "rhythm" | "emphasis" | "concision" | "other",
        "original": string,
        "suggestion": string,
        "reason": string,
        "applied": boolean
      }
    ],
    "questions": [string]
  },
  "rewrite": string
}

Field rules:
• feedback.summary: 1–3 sentences, max 400 characters. A friendly overview of the paragraph: what works, what kinds of fixes were made, and what the enhancements aim for.
• feedback.has_changes: true if "changes" has at least one item with applied = true; otherwise false.
• feedback.changes: ordered "error" first, then "clarity", then "enhancement", then "optional". Max 15 items, with at most 4 of type "enhancement" and at most 3 of type "optional". Use an empty array [] if there are none.
  - id: starts at 1 and increments by 1.
  - original: an EXACT substring copied from the input, character for character, so it can be found and highlighted in code. Keep it as short as possible while still unique in the paragraph (usually 2–8 words). The "original" values of different changes must never overlap.
  - suggestion: the replacement text for "original" only, not the whole sentence. Use an empty string "" when the change is a pure deletion.
  - reason: one sentence, max 150 characters. For enhancements, say what effect the edit has.
  - applied: true for "error", "clarity" and "enhancement"; false for "optional".
• feedback.questions: questions for the author that you can't resolve yourself. Max 3. Use [] if none.
• rewrite: the full paragraph as plain text, with every change where applied = true, and nothing else changed. Keep the author's paragraph breaks as "\n\n" and line breaks as "\n". No markdown, no quotes around it, no notes. If has_changes is false, rewrite must be identical to the input.
• All strings must be valid JSON: escape double quotes as \" and newlines as \n.
• PRESERVE all newlines from the input - use \n for single line breaks and \n\n for paragraph breaks.
PROMPT;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post($this->baseUrl.'/messages', [
                'model' => $this->model,
                'max_tokens' => 2048,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "Analyze this paragraph:\n\n{$content}",
                    ],
                ],
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $responseText = $result['content'][0]['text'] ?? '';

                // Clean markdown code blocks if present
                $cleaned = preg_replace('/```json\s*|\s*```/', '', $responseText);
                $cleaned = trim($cleaned);

                $data = json_decode($cleaned, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($data['feedback'], $data['rewrite'])) {
                    return $data;
                }

                Log::error('Invalid grammar suggestions response format', [
                    'response' => $responseText,
                ]);
            }

            return $this->emptyGrammarResult();
        } catch (\Exception $e) {
            Log::error('Grammar suggestions API exception', [
                'message' => $e->getMessage(),
            ]);

            return $this->emptyGrammarResult();
        }
    }

    private function emptyGrammarResult(): array
    {
        return [
            'feedback' => [
                'summary' => 'Unable to analyze paragraph at this time.',
                'has_changes' => false,
                'changes' => [],
                'questions' => [],
            ],
            'rewrite' => '',
        ];
    }
}
