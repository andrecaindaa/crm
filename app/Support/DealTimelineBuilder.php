<?php

namespace App\Support;

use App\Models\Deal;
use Illuminate\Support\Collection;

class DealTimelineBuilder
{
    public static function build(Deal $deal): Collection
    {
        $items = collect();

        /**
         * Criação do negócio
         */
        $items->push([
            'type' => 'deal_created',
            'label' => 'Negócio criado',
            'date' => $deal->created_at,
            'user' => $deal->owner,
        ]);

        /**
         * Propostas
         */
        foreach ($deal->proposals as $proposal) {
            $items->push([
                'type' => 'proposal_uploaded',
                'label' => 'Proposta adicionada',
                'date' => $proposal->created_at,
                'meta' => [
                    'name' => $proposal->original_name,
                ],
            ]);

            if ($proposal->sent_at) {
                $items->push([
                    'type' => 'proposal_sent',
                    'label' => 'Proposta enviada por email',
                    'date' => $proposal->sent_at,
                    'user' => $proposal->sender,
                ]);
            }
        }

        /**
         * Follow-ups
         */
        foreach ($deal->followUps as $followUp) {
            $items->push([
                'type' => 'follow_up',
                'label' => 'Follow-up enviado',
                'date' => $followUp->sent_at,
                'user' => $followUp->sender,
                'meta' => [
                    'body' => $followUp->body,
                ],
            ]);
        }

        return $items->sortBy('date')->values();
    }
}
