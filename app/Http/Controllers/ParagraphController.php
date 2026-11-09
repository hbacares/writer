<?php

namespace App\Http\Controllers;

use App\Models\Paragraph;
use App\Models\Section;
use App\Services\AnthropicService;
use Illuminate\Http\Request;

class ParagraphController extends Controller
{
    public function index(Section $section)
    {
        $this->authorize('view', $section->chapter->story);

        return redirect()->route('sections.show', $section);
    }

    public function create(Section $section)
    {
        $this->authorize('view', $section->chapter->story);

        $story = $section->chapter->story;
        $characters = $story->characters;
        $locations = $story->locations;
        $references = $story->references;

        return view('paragraphs.create', compact('section', 'characters', 'locations', 'references'));
    }

    public function store(Request $request, Section $section)
    {
        $this->authorize('update', $section->chapter->story);

        $validated = $request->validate([
            'content' => 'required|string',
            'characters' => 'nullable|array',
            'characters.*' => 'exists:characters,id',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
            'references' => 'nullable|array',
            'references.*' => 'exists:references,id',
        ]);

        $maxOrder = $section->paragraphs()->max('order') ?? -1;

        // Get manual selections
        $manualCharacters = $validated['characters'] ?? [];
        $manualLocations = $validated['locations'] ?? [];
        $manualReferences = $validated['references'] ?? [];

        // Split content by paragraph breaks (Trix uses <div> for paragraphs)
        $paragraphs = $this->splitIntoParagraphs($validated['content']);

        $firstParagraph = null;
        foreach ($paragraphs as $index => $content) {
            if (trim(strip_tags($content))) {
                $paragraph = $section->paragraphs()->create([
                    'content' => $content,
                    'order' => $maxOrder + 1 + $index,
                ]);

                // Run AI analysis with manual selections
                $this->analyzeWithAI($paragraph, $manualCharacters, $manualLocations, $manualReferences);

                if ($firstParagraph === null) {
                    $firstParagraph = $paragraph;
                }
            }
        }

        // Normalize paragraph order to ensure sequential values
        $section->normalizeParagraphOrder();

        $story = $section->chapter->story;

        return redirect(route('stories.show', $story).'#paragraph-'.$firstParagraph->id)
            ->with('success', count($paragraphs) > 1
                ? count($paragraphs).' paragraphs created successfully.'
                : 'Paragraph created successfully.');
    }

    public function show(Paragraph $paragraph)
    {
        $this->authorize('view', $paragraph->section->chapter->story);

        return view('paragraphs.show', compact('paragraph'));
    }

    public function edit(Paragraph $paragraph)
    {
        $this->authorize('update', $paragraph->section->chapter->story);

        $story = $paragraph->section->chapter->story;
        $characters = $story->characters;
        $locations = $story->locations;
        $references = $story->references;

        return view('paragraphs.edit', compact('paragraph', 'characters', 'locations', 'references'));
    }

    public function update(Request $request, Paragraph $paragraph)
    {
        $this->authorize('update', $paragraph->section->chapter->story);

        $validated = $request->validate([
            'content' => 'required|string',
            'characters' => 'nullable|array',
            'characters.*' => 'exists:characters,id',
            'locations' => 'nullable|array',
            'locations.*' => 'exists:locations,id',
            'references' => 'nullable|array',
            'references.*' => 'exists:references,id',
        ]);

        $section = $paragraph->section;
        $originalOrder = $paragraph->order;

        // Split content by paragraph breaks
        $paragraphs = $this->splitIntoParagraphs($validated['content']);

        $manualCharacters = $validated['characters'] ?? [];
        $manualLocations = $validated['locations'] ?? [];
        $manualReferences = $validated['references'] ?? [];

        if (count($paragraphs) === 1) {
            // Single paragraph - just update
            $paragraph->update(['content' => $paragraphs[0]]);

            // AI analysis (combines manual + AI detections, removes unmentioned)
            $this->analyzeWithAI($paragraph, $manualCharacters, $manualLocations, $manualReferences);
        } else {
            // Multiple paragraphs - update first, create rest
            // Update existing paragraph with first chunk
            $paragraph->update(['content' => $paragraphs[0]]);

            // Shift order of subsequent paragraphs
            $section->paragraphs()
                ->where('order', '>', $originalOrder)
                ->increment('order', count($paragraphs) - 1);

            // Create new paragraphs for remaining chunks
            foreach (array_slice($paragraphs, 1) as $index => $content) {
                if (trim(strip_tags($content))) {
                    $newParagraph = $section->paragraphs()->create([
                        'content' => $content,
                        'order' => $originalOrder + 1 + $index,
                    ]);

                    // AI analysis for new paragraphs (with manual selections)
                    $this->analyzeWithAI($newParagraph, $manualCharacters, $manualLocations, $manualReferences);
                }
            }

            // AI analysis for first paragraph (with manual selections)
            $this->analyzeWithAI($paragraph, $manualCharacters, $manualLocations, $manualReferences);
        }

        $story = $section->chapter->story;

        return redirect(route('stories.show', $story).'#paragraph-'.$paragraph->id)
            ->with('success', count($paragraphs) > 1
                ? 'Split into '.count($paragraphs).' paragraphs successfully.'
                : 'Paragraph updated successfully.');
    }

    public function destroy(Paragraph $paragraph)
    {
        $this->authorize('delete', $paragraph->section->chapter->story);

        $section = $paragraph->section;
        $paragraph->delete();

        // Normalize paragraph order to ensure sequential values
        $section->normalizeParagraphOrder();

        return redirect()->route('sections.show', $section)
            ->with('success', 'Paragraph deleted successfully.');
    }

    public function mergeWithPrevious(Paragraph $paragraph)
    {
        $this->authorize('update', $paragraph->section->chapter->story);

        $section = $paragraph->section;

        // Find previous paragraph in the same section
        // Use reorder() to clear the relationship's default orderBy('order') and apply descending order
        $previousParagraph = $section->paragraphs()
            ->where('order', '<', $paragraph->order)
            ->reorder('order', 'desc')
            ->first();

        if (! $previousParagraph) {
            return back()->with('error', 'No previous paragraph to merge with.');
        }

        // Store IDs and orders for logging
        $currentId = $paragraph->id;
        $currentOrder = $paragraph->order;
        $previousId = $previousParagraph->id;
        $previousOrder = $previousParagraph->order;

        // Combine content - previous content + current content
        $mergedContent = $previousParagraph->content.'<br><br>'.$paragraph->content;

        // Update previous paragraph with merged content
        $previousParagraph->update(['content' => $mergedContent]);

        // Delete current paragraph
        $paragraph->delete();

        // Normalize paragraph order to ensure sequential values
        $section->normalizeParagraphOrder();

        // Re-analyze the merged paragraph with AI
        $this->analyzeWithAI($previousParagraph);

        $story = $section->chapter->story;

        return redirect(route('stories.show', $story).'#paragraph-'.$previousParagraph->id)
            ->with('success', "Paragraph #{$currentId} (order {$currentOrder}) merged into paragraph #{$previousId} (order {$previousOrder}) successfully.");
    }

    private function analyzeWithAI(Paragraph $paragraph, array $manualCharacterIds = [], array $manualLocationIds = [], array $manualReferenceIds = []): void
    {
        if (! config('services.anthropic.api_key')) {
            // No API key - just sync manual selections
            $paragraph->characters()->sync($manualCharacterIds);
            $paragraph->locations()->sync($manualLocationIds);
            $paragraph->references()->sync($manualReferenceIds);

            return;
        }

        $story = $paragraph->section->chapter->story;

        // Get existing items (not rejected)
        $characters = $story->characters()->notRejected()->get(['id', 'name'])->toArray();
        $locations = $story->locations()->notRejected()->get(['id', 'name'])->toArray();
        $references = $story->references()->notRejected()->get(['id', 'title'])->toArray();

        $anthropic = new AnthropicService;
        $analysis = $anthropic->analyzeContentForReferences(
            strip_tags($paragraph->content),
            $characters,
            $locations,
            $references
        );

        // Get AI-detected existing items
        $aiCharacterIds = collect($characters)
            ->whereIn('name', $analysis['mentioned_characters'])
            ->pluck('id')
            ->toArray();

        $aiLocationIds = collect($locations)
            ->whereIn('name', $analysis['mentioned_locations'])
            ->pluck('id')
            ->toArray();

        $aiReferenceIds = collect($references)
            ->whereIn('title', $analysis['mentioned_references'])
            ->pluck('id')
            ->toArray();

        // Create new AI-detected items and get their IDs
        $newCharacterIds = [];
        foreach ($analysis['new_characters'] as $newChar) {
            $character = $story->characters()->create([
                'name' => $newChar['name'],
                'description' => $newChar['description'] ?? '',
                'status' => 'ai_created',
            ]);
            $newCharacterIds[] = $character->id;
        }

        $newLocationIds = [];
        foreach ($analysis['new_locations'] as $newLoc) {
            $location = $story->locations()->create([
                'name' => $newLoc['name'],
                'description' => $newLoc['description'] ?? '',
                'status' => 'ai_created',
            ]);
            $newLocationIds[] = $location->id;
        }

        // AI takes precedence - only use AI-detected items
        // This ensures cleanup happens automatically
        $finalCharacterIds = array_unique(array_merge($aiCharacterIds, $newCharacterIds));
        $finalLocationIds = array_unique(array_merge($aiLocationIds, $newLocationIds));
        $finalReferenceIds = $aiReferenceIds;

        // Sync (this will remove any not in the list)
        $paragraph->characters()->sync($finalCharacterIds);
        $paragraph->locations()->sync($finalLocationIds);
        $paragraph->references()->sync($finalReferenceIds);
    }

    public function aiSuggestions(Paragraph $paragraph)
    {
        $this->authorize('update', $paragraph->section->chapter->story);

        $story = $paragraph->section->chapter->story;

        if (! $story->grammar_prompt) {
            return response()->json([
                'error' => 'No grammar prompt set for this story. Please set one in Grammar AI settings.',
            ], 400);
        }

        if (! config('services.anthropic.api_key')) {
            return response()->json([
                'error' => 'Anthropic API key not configured.',
            ], 400);
        }

        // Convert HTML to plain text while preserving line breaks
        $plainTextContent = $this->htmlToPlainText($paragraph->content);

        $anthropic = new AnthropicService;
        $suggestions = $anthropic->getGrammarSuggestions(
            $plainTextContent,
            $story->grammar_prompt
        );

        return response()->json($suggestions);
    }

    private function htmlToPlainText(string $html): string
    {
        // First, normalize spacing in HTML
        $html = preg_replace('/>\s+</', '><', $html);

        // Convert various HTML elements to newlines
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n\n", $text);
        $text = preg_replace('/<\/div>/i', "\n\n", $text);
        $text = preg_replace('/<\/h[1-6]>/i', "\n\n", $text);

        // Remove remaining HTML tags
        $text = strip_tags($text);

        // Normalize whitespace within lines (multiple spaces to single space)
        $text = preg_replace('/[^\S\n]+/', ' ', $text);

        // Clean up excessive newlines (more than 2 in a row becomes 2)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        // Trim each line
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $text = implode("\n", $lines);

        return trim($text);
    }

    private function splitIntoParagraphs(string $content): array
    {
        // Look for triple <br> tags (or more) as paragraph separators
        // This is what Trix creates when pressing Enter multiple times
        // Pattern: <br><br><br> or more consecutive breaks
        $parts = preg_split('/(?:<br\s*\/?>){3,}/i', $content);

        // Clean and wrap each part
        $paragraphs = [];
        foreach ($parts as $part) {
            $trimmed = trim($part);

            // Skip empty paragraphs
            if (! $trimmed || strip_tags($trimmed) === '') {
                continue;
            }

            // If content is wrapped in a div, keep it as is
            // Otherwise wrap it in a div
            if (preg_match('/^<div[^>]*>.*<\/div>$/is', $trimmed)) {
                $paragraphs[] = $trimmed;
            } else {
                // Remove opening/closing div if partial
                $cleaned = preg_replace('/^<div[^>]*>/', '', $trimmed);
                $cleaned = preg_replace('/<\/div>$/', '', $cleaned);
                $paragraphs[] = '<div>'.trim($cleaned).'</div>';
            }
        }

        // If no split occurred, return original content as single paragraph
        return count($paragraphs) <= 1 ? [$content] : $paragraphs;
    }
}
