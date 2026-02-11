<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Entity;
use App\Models\Person;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Support\DealTimelineBuilder;
use App\Models\DealActivity;

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
            'owner',
            'proposals.sender',
            'followUps.sender',
            'activities.user',
        ]);

    $activeFollowUp = $deal->followUps()
        ->where('active', true)
        ->orderByDesc('next_send_at')
        ->first();

        return Inertia::render('Deals/Show', [
            'deal' => $deal,
            'timeline' => DealTimelineBuilder::build($deal),
            'activeFollowUp' => $activeFollowUp,
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

    if ($deal->stage === $data['stage']) {
        return back();
    }

    $oldStage = $deal->stage;

    $deal->update($data);

    DealActivity::create([
        'deal_id' => $deal->id,
        'user_id' => auth()->id(),
        'type' => 'stage_changed',
        'label' => 'Estado alterado',
        'meta' => [
            'from' => $oldStage,
            'to' => $data['stage'],
        ],
        'created_at' => now(),
    ]);

    return back();
}

}
