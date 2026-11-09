<?php

namespace App\Services;

use App\Models\Character;
use App\Models\Location;
use App\Models\Reference;
use Illuminate\Support\Facades\DB;

class EntityConversionService
{
    public function convertCharacterToLocation(Character $character): Location
    {
        return DB::transaction(function () use ($character) {
            // Create new location with character data
            $location = Location::create([
                'story_id' => $character->story_id,
                'name' => $character->name,
                'description' => $character->description,
                'image' => $character->image,
                'status' => $character->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $character->paragraphs()->pluck('paragraphs.id')->toArray();
            $location->paragraphs()->attach($paragraphIds);

            // Delete old character
            $character->paragraphs()->detach();
            $character->delete();

            return $location;
        });
    }

    public function convertCharacterToReference(Character $character): Reference
    {
        return DB::transaction(function () use ($character) {
            // Create new reference with character data
            $reference = Reference::create([
                'story_id' => $character->story_id,
                'title' => $character->name,
                'content' => $character->description,
                'image' => $character->image,
                'status' => $character->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $character->paragraphs()->pluck('paragraphs.id')->toArray();
            $reference->paragraphs()->attach($paragraphIds);

            // Delete old character
            $character->paragraphs()->detach();
            $character->delete();

            return $reference;
        });
    }

    public function convertLocationToCharacter(Location $location): Character
    {
        return DB::transaction(function () use ($location) {
            // Create new character with location data
            $character = Character::create([
                'story_id' => $location->story_id,
                'name' => $location->name,
                'description' => $location->description,
                'image' => $location->image,
                'status' => $location->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $location->paragraphs()->pluck('paragraphs.id')->toArray();
            $character->paragraphs()->attach($paragraphIds);

            // Delete old location
            $location->paragraphs()->detach();
            $location->delete();

            return $character;
        });
    }

    public function convertLocationToReference(Location $location): Reference
    {
        return DB::transaction(function () use ($location) {
            // Create new reference with location data
            $reference = Reference::create([
                'story_id' => $location->story_id,
                'title' => $location->name,
                'content' => $location->description,
                'image' => $location->image,
                'status' => $location->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $location->paragraphs()->pluck('paragraphs.id')->toArray();
            $reference->paragraphs()->attach($paragraphIds);

            // Delete old location
            $location->paragraphs()->detach();
            $location->delete();

            return $reference;
        });
    }

    public function convertReferenceToCharacter(Reference $reference): Character
    {
        return DB::transaction(function () use ($reference) {
            // Create new character with reference data
            $character = Character::create([
                'story_id' => $reference->story_id,
                'name' => $reference->title,
                'description' => $reference->content,
                'image' => $reference->image,
                'status' => $reference->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $reference->paragraphs()->pluck('paragraphs.id')->toArray();
            $character->paragraphs()->attach($paragraphIds);

            // Delete old reference
            $reference->paragraphs()->detach();
            $reference->delete();

            return $character;
        });
    }

    public function convertReferenceToLocation(Reference $reference): Location
    {
        return DB::transaction(function () use ($reference) {
            // Create new location with reference data
            $location = Location::create([
                'story_id' => $reference->story_id,
                'name' => $reference->title,
                'description' => $reference->content,
                'image' => $reference->image,
                'status' => $reference->status,
            ]);

            // Transfer all paragraph relationships
            $paragraphIds = $reference->paragraphs()->pluck('paragraphs.id')->toArray();
            $location->paragraphs()->attach($paragraphIds);

            // Delete old reference
            $reference->paragraphs()->detach();
            $reference->delete();

            return $location;
        });
    }
}
