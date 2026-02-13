<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealFollowUp;
use App\Mail\DealFollowUpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DealFollowUpController extends Controller
{
    public function templates()
    {
        return response()->json(
            config('followups.emails')
        );
    }

    public function store(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'body' => 'required|string',
            'interval_days' => 'required|in:2,5,7',
        ]);

        $email = $deal->person?->email
            ?? $deal->entity?->email;

        if (!$email) {
            return back()->withErrors([
                'email' => 'Este negócio não tem email associado.',
            ]);
        }

        Mail::to($email)->send(
            new DealFollowUpMail($deal, $data['body'])
        );

            DealFollowUp::create([
            'deal_id' => $deal->id,
            'sent_by' => auth()->id(),
            'sent_at' => now(),
            'next_send_at' => now(),
             'interval_days' => $data['interval_days'],//
            'active' => false,
        ]);

        return back();
    }

    public function cancel(DealFollowUp $followUp)
    {
        $this->authorize('update', $followUp->deal);

        $followUp->update([
            'active' => false,
        ]);

        return back();
    }

}
