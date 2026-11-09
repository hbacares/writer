<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Story;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);

        return redirect()->route('stories.show', $story);
    }

    public function create(Story $story)
    {
        $this->authorize('view', $story);

        return view('chapters.create', compact('story'));
    }

    public function store(Request $request, Story $story)
    {
        $this->authorize('update', $story);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxOrder = $story->chapters()->max('order') ?? -1;

        $story->chapters()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('stories.show', $story)
            ->with('success', 'Chapter created successfully.');
    }

    public function show(Chapter $chapter)
    {
        $this->authorize('view', $chapter->story);

        $chapter->load(['sections.paragraphs']);

        return view('chapters.show', compact('chapter'));
    }

    public function edit(Chapter $chapter)
    {
        $this->authorize('update', $chapter->story);

        return view('chapters.edit', compact('chapter'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $this->authorize('update', $chapter->story);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $chapter->update($validated);

        return redirect()->route('chapters.show', $chapter)
            ->with('success', 'Chapter updated successfully.');
    }

    public function destroy(Chapter $chapter)
    {
        $this->authorize('delete', $chapter->story);

        $storyId = $chapter->story_id;
        $chapter->delete();

        return redirect()->route('stories.show', $storyId)
            ->with('success', 'Chapter deleted successfully.');
    }

    public function reorderSections(Request $request, Chapter $chapter)
    {
        $this->authorize('update', $chapter->story);

        $validated = $request->validate([
            'sections' => 'required|array',
            'sections.*' => 'required|integer|exists:sections,id',
        ]);

        foreach ($validated['sections'] as $order => $sectionId) {
            $chapter->sections()->where('id', $sectionId)->update(['order' => $order]);
        }

        return back()->with('success', 'Sections reordered successfully.');
    }
}
