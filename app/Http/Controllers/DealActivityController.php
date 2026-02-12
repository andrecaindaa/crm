<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealActivity;
use Illuminate\Http\Request;

class DealActivityController extends Controller
{
    public function store(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'type' => 'required|in:note,call,meeting,task',
            'description' => 'required|string',
            'due_at' => 'nullable|date',
        ]);

        DealActivity::create([
            'deal_id' => $deal->id,
            'user_id' => auth()->id(),
            'type' => $data['type'],
            'label' => ucfirst($data['type']),
            'description' => $data['description'],
            'due_at' => $data['due_at'] ?? null,
            'created_at' => now(),
        ]);

        return back();
    }

    public function complete(DealActivity $activity)
    {
        $this->authorize('update', $activity->deal);

        $activity->update([
            'completed_at' => now(),
        ]);

        return back();
    }
}
