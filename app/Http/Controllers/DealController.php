<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Entity;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Support\DealTimelineBuilder;

class DealController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Deal::class);

        $stages = Deal::stages();

        $deals = Deal::where('owner_id', auth()->id())
            ->with(['entity'])
            ->get()
            ->groupBy('stage');

        return Inertia::render('Deals/Kanban', [
            'stages' => $stages,
            'deals' => $deals,
        ]);
    }

    public function show(Deal $deal)
    {
        $this->authorize('view', $deal);

        $deal->load([
            'entity',
            'person',
            'proposals.sender',
            'followUps.sender',
        ]);

        return Inertia::render('Deals/Show', [
            'deal' => $deal,
            'timeline' => DealTimelineBuilder::build($deal),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Deal::class);

        return Inertia::render('Deals/Create', [
            'entities' => Entity::where('user_id', auth()->id())->get(),
            'people' => Person::where('user_id', auth()->id())->get(),
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
            'stage' => 'required|string|in:' . implode(',', array_keys(Deal::stages())),
        ]);

        Deal::create([
            ...$data,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('deals.index');
    }

    public function updateStage(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'stage' => 'required|string|in:' . implode(',', array_keys(Deal::stages())),
        ]);

        $deal->update($data);

        return back();
    }
}
