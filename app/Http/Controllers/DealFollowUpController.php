<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealFollowUp;
use App\Mail\DealFollowUpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DealFollowUpController extends Controller
{
    public function store(Request $request, Deal $deal)
    {
        $this->authorize('update', $deal);

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $email = $deal->person?->email
            ?? $deal->entity?->email;

        if (!$email) {
            return back()->withErrors([
                'email' => 'Este negócio não tem email associado.',
            ]);
        }

        // Enviar email
        Mail::to($email)->send(
            new DealFollowUpMail($deal, $data['body'])
        );

        // Guardar follow-up (modo simples, sem agendamento ainda)
        DealFollowUp::create([
            'deal_id' => $deal->id,
            'next_send_at' => now(), // reutilização segura
            'active' => false,
        ]);

        return back();
    }
}
