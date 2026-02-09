<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealProposal;
use App\Mail\DealProposalMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DealProposalController extends Controller
{
    public function store(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $request->validate([
            'proposal' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $file = $request->file('proposal');
        $path = $file->store('proposals');

        $deal->proposals()->create([
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ]);

        return back();
    }

    public function send(Request $request, DealProposal $proposal)
    {
        $this->authorize('update', $proposal->deal);

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $email =
            $proposal->deal->person?->email
            ?: $proposal->deal->entity?->email;

        if (! $email) {
            return back()->withErrors([
                'email' => 'Este negócio não tem email associado.',
            ]);
        }

        Mail::to($email)->send(
            new DealProposalMail($proposal, $data['body'])
        );

        $proposal->update([
            'sent_at' => now(),
            'sent_by' => auth()->id(),
        ]);

        return back();
    }
}
