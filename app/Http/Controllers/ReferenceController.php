<?php

namespace App\Http\Controllers;

use App\Models\Reference;
use App\Models\Story;
use App\Services\EntityConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReferenceController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);
        $references = $story->references;
        return view('references.index', compact('story', 'references'));
    }

    public function create(Story $story)
    {
        $this->authorize('view', $story);
        return view('references.create', compact('story'));
    }

    public function store(Request $request, Story $story)
    {
        $this->authorize('update', $story);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('references', 'public');
        }
        $story->references()->create($validated);
        return redirect()->route('stories.references.index', $story)->with('success', 'Reference created successfully.');
    }

    public function show(Reference $reference)
    {
        $this->authorize('view', $reference->story);
        $reference->load('paragraphs.section.chapter');
        return view('references.show', compact('reference'));
    }

    public function edit(Reference $reference)
    {
        $this->authorize('update', $reference->story);
        return view('references.edit', compact('reference'));
    }

    public function update(Request $request, Reference $reference)
    {
        $this->authorize('update', $reference->story);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($reference->image) Storage::disk('public')->delete($reference->image);
            $validated['image'] = $request->file('image')->store('references', 'public');
        }
        $reference->update($validated);
        return redirect()->route('references.show', $reference)->with('success', 'Reference updated successfully.');
    }

    public function destroy(Reference $reference)
    {
        $this->authorize('delete', $reference->story);
        if ($reference->image) Storage::disk('public')->delete($reference->image);
        $storyId = $reference->story_id;
        $reference->delete();
        return redirect()->route('stories.references.index', $storyId)->with('success', 'Reference deleted successfully.');
    }

    public function convertToCharacter(Reference $reference, EntityConversionService $converter)
    {
        $this->authorize('update', $reference->story);

        $character = $converter->convertReferenceToCharacter($reference);

        return redirect()->route('characters.show', $character)
            ->with('success', 'Reference converted to character successfully.');
    }

    public function convertToLocation(Reference $reference, EntityConversionService $converter)
    {
        $this->authorize('update', $reference->story);

        $location = $converter->convertReferenceToLocation($reference);

        return redirect()->route('locations.show', $location)
            ->with('success', 'Reference converted to location successfully.');
    }
}
