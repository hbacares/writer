<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Chapter $chapter)
    {
        $this->authorize('view', $chapter->story);

        return redirect()->route('chapters.show', $chapter);
    }

    public function create(Chapter $chapter)
    {
        $this->authorize('view', $chapter->story);

        return view('sections.create', compact('chapter'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $this->authorize('update', $chapter->story);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = $chapter->sections()->max('order') ?? -1;

        $chapter->sections()->create([
            ...$validated,
            'order' => $maxOrder + 1,
        ]);

        return redirect()->route('chapters.show', $chapter)
            ->with('success', 'Section created successfully.');
    }

    public function show(Section $section)
    {
        $this->authorize('view', $section->chapter->story);

        $section->load('paragraphs');

        return view('sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        $this->authorize('update', $section->chapter->story);

        return view('sections.edit', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $this->authorize('update', $section->chapter->story);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $section->update($validated);

        return redirect()->route('sections.show', $section)
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $this->authorize('delete', $section->chapter->story);

        $chapterId = $section->chapter_id;
        $section->delete();

        return redirect()->route('chapters.show', $chapterId)
            ->with('success', 'Section deleted successfully.');
    }

    public function reorderParagraphs(Request $request, Section $section)
    {
        $this->authorize('update', $section->chapter->story);

        $validated = $request->validate([
            'paragraphs' => 'required|array',
            'paragraphs.*' => 'required|integer|exists:paragraphs,id',
        ]);

        foreach ($validated['paragraphs'] as $order => $paragraphId) {
            $section->paragraphs()->where('id', $paragraphId)->update(['order' => $order]);
        }

        // Normalize paragraph order to ensure sequential values
        $section->normalizeParagraphOrder();

        return back()->with('success', 'Paragraphs reordered successfully.');
    }
}
