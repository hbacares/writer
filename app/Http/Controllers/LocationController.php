<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Story;
use App\Services\EntityConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LocationController extends Controller
{
    public function index(Story $story)
    {
        $this->authorize('view', $story);
        $locations = $story->locations;
        return view('locations.index', compact('story', 'locations'));
    }

    public function create(Story $story)
    {
        $this->authorize('view', $story);
        return view('locations.create', compact('story'));
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
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }
        $story->locations()->create($validated);
        return redirect()->route('stories.locations.index', $story)->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $this->authorize('view', $location->story);
        $location->load('paragraphs.section.chapter');
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $this->authorize('update', $location->story);
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $this->authorize('update', $location->story);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($location->image) Storage::disk('public')->delete($location->image);
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }
        $location->update($validated);
        return redirect()->route('locations.show', $location)->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location->story);
        if ($location->image) Storage::disk('public')->delete($location->image);
        $storyId = $location->story_id;
        $location->delete();
        return redirect()->route('stories.locations.index', $storyId)->with('success', 'Location deleted successfully.');
    }

    public function convertToCharacter(Location $location, EntityConversionService $converter)
    {
        $this->authorize('update', $location->story);

        $character = $converter->convertLocationToCharacter($location);

        return redirect()->route('characters.show', $character)
            ->with('success', 'Location converted to character successfully.');
    }

    public function convertToReference(Location $location, EntityConversionService $converter)
    {
        $this->authorize('update', $location->story);

        $reference = $converter->convertLocationToReference($location);

        return redirect()->route('references.show', $reference)
            ->with('success', 'Location converted to reference successfully.');
    }
}
