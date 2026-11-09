<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Story;
use App\Services\EntityConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CharacterController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);
        $characters = $story->characters;
        return view('characters.index', compact('story', 'characters'));
    }

    public function create(Story $story)
    {
        $this->authorize('view', $story);
        return view('characters.create', compact('story'));
    }

    public function store(Request $request, Story $story)
    {
        $this->authorize('update', $story);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('characters', 'public');
        }
        $story->characters()->create($validated);
        return redirect()->route('stories.characters.index', $story)->with('success', 'Character created successfully.');
    }

    public function show(Character $character)
    {
        $this->authorize('view', $character->story);
        $character->load('paragraphs.section.chapter');
        return view('characters.show', compact('character'));
    }

    public function edit(Character $character)
    {
        $this->authorize('update', $character->story);
        return view('characters.edit', compact('character'));
    }

    public function update(Request $request, Character $character)
    {
        $this->authorize('update', $character->story);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($character->image) Storage::disk('public')->delete($character->image);
            $validated['image'] = $request->file('image')->store('characters', 'public');
        }
        $character->update($validated);
        return redirect()->route('characters.show', $character)->with('success', 'Character updated successfully.');
    }

    public function destroy(Character $character)
    {
        $this->authorize('delete', $character->story);
        if ($character->image) Storage::disk('public')->delete($character->image);
        $storyId = $character->story_id;
        $character->delete();
        return redirect()->route('stories.characters.index', $storyId)->with('success', 'Character deleted successfully.');
    }

    public function convertToLocation(Character $character, EntityConversionService $converter)
    {
        $this->authorize('update', $character->story);

        $location = $converter->convertCharacterToLocation($character);

        return redirect()->route('locations.show', $location)
            ->with('success', 'Character converted to location successfully.');
    }

    public function convertToReference(Character $character, EntityConversionService $converter)
    {
        $this->authorize('update', $character->story);

        $reference = $converter->convertCharacterToReference($character);

        return redirect()->route('references.show', $reference)
            ->with('success', 'Character converted to reference successfully.');
    }
}
