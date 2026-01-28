<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Entity;
use App\Models\Person;


class DealController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Deal::class);

        $stages = [
            'lead',
            'proposal',
            'negotiation',
            'follow_up',
            'won',
            'lost',
        ];

        $deals = Deal::where('owner_id', auth()->id())
            ->get()
            ->groupBy('stage');

        return Inertia::render('Deals/Board', [
            'stages' => $stages,
            'deals' => $deals,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Deal::class);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'entity_id' => 'nullable|exists:entities,id',
            'person_id' => 'nullable|exists:people,id',
            'value' => 'nullable|numeric',
            'stage' => 'required|string',
        ]);

        Deal::create([
            ...$data,
            'owner_id' => auth()->id(),
        ]);

        return back();
    }

    public function updateStage(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $request->validate([
            'stage' => 'required|string',
        ]);

        $deal->update([
            'stage' => $request->stage,
        ]);

        return back();
    }

    public function create()
    {
        $this->authorize('create', Deal::class);

        return Inertia::render('Deals/Create', [
            'entities' => Entity::where('user_id', auth()->id())->get(),
            'people' => Person::where('user_id', auth()->id())->get(),
        ]);
    }

}
