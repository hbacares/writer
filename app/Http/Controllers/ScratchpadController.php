<?php

namespace App\Http\Controllers;

use App\Models\Scratchpad;
use App\Models\Section;
use App\Models\Story;
use Illuminate\Http\Request;

class ScratchpadController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);

        $scratchpads = $story->scratchpads;

        return view('scratchpads.index', compact('story', 'scratchpads'));
    }

    public function create(Story $story)
    {
        $this->authorize('view', $story);

        return view('scratchpads.create', compact('story'));
    }

    public function store(Request $request, Story $story)
    {
        $this->authorize('update', $story);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $maxOrder = $story->scratchpads()->max('order') ?? -1;

        $story->scratchpads()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('stories.scratchpads.index', $story)
            ->with('success', 'Scratchpad created successfully.');
    }

    public function edit(Scratchpad $scratchpad)
    {
        $this->authorize('update', $scratchpad->story);

        return view('scratchpads.edit', compact('scratchpad'));
    }

    public function update(Request $request, Scratchpad $scratchpad)
    {
        $this->authorize('update', $scratchpad->story);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        $scratchpad->update($validated);

        return redirect()->route('stories.scratchpads.index', $scratchpad->story)
            ->with('success', 'Scratchpad updated successfully.');
    }

    public function destroy(Scratchpad $scratchpad)
    {
        $this->authorize('delete', $scratchpad->story);

        $storyId = $scratchpad->story_id;
        $scratchpad->delete();

        return redirect()->route('stories.scratchpads.index', $storyId)
            ->with('success', 'Scratchpad deleted successfully.');
    }

    public function moveToSection(Request $request, Scratchpad $scratchpad)
    {
        $this->authorize('update', $scratchpad->story);

        $validated = $request->validate([
            'section_id' => 'required|exists:sections,id',
        ]);

        $section = Section::findOrFail($validated['section_id']);

        if ($section->chapter->story_id !== $scratchpad->story_id) {
            return back()->withErrors(['section_id' => 'Section must belong to the same story.']);
        }

        $maxOrder = $section->paragraphs()->max('order') ?? -1;

        $section->paragraphs()->create([
            'content' => $scratchpad->content,
            'order' => $maxOrder + 1,
        ]);

        $scratchpad->delete();

        return redirect()->route('sections.show', $section)
            ->with('success', 'Scratchpad moved to section successfully.');
    }
}
