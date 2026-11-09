<?php

namespace App\Http\Controllers;

use App\Models\Character;
use App\Models\Location;
use App\Models\Reference;
use Illuminate\Http\Request;

class AiItemController extends Controller
{
    public function confirmCharacter(Character $character)
    {
        $this->authorize('update', $character->story);

        $character->update(['status' => 'confirmed']);

        return back()->with('success', 'Character confirmed successfully.');
    }

    public function rejectCharacter(Character $character)
    {
        $this->authorize('update', $character->story);

        $character->update(['status' => 'rejected']);

        return back()->with('success', 'Character rejected and hidden.');
    }

    public function confirmLocation(Location $location)
    {
        $this->authorize('update', $location->story);

        $location->update(['status' => 'confirmed']);

        return back()->with('success', 'Location confirmed successfully.');
    }

    public function rejectLocation(Location $location)
    {
        $this->authorize('update', $location->story);

        $location->update(['status' => 'rejected']);

        return back()->with('success', 'Location rejected and hidden.');
    }

    public function confirmReference(Reference $reference)
    {
        $this->authorize('update', $reference->story);

        $reference->update(['status' => 'confirmed']);

        return back()->with('success', 'Reference confirmed successfully.');
    }

    public function rejectReference(Reference $reference)
    {
        $this->authorize('update', $reference->story);

        $reference->update(['status' => 'rejected');

        return back()->with('success', 'Reference rejected and hidden.');
    }
}
