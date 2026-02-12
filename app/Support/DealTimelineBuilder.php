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
         * 1. Criação do negócio
         */
        $items->push([
            'type' => 'deal_created',
            'label' => 'Negócio criado',
            'date' => $deal->created_at,
            'user' => $deal->owner,
        ]);

        /**
         * 2. Propostas
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
         * 3. Follow-ups
         */
        foreach ($deal->followUps as $followUp) {

            if (!$followUp->sent_at) {
                continue;
            }

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

        /**
 * 4. Atividades do negócio
 */
foreach ($deal->activities as $activity) {

    $items->push([
        'type' => $activity->type,
        'label' => $activity->label,
        'date' => $activity->created_at,
        'user' => $activity->user,
        'meta' => [
            ...($activity->meta ?? []),
            'description' => $activity->description,
            'due_at' => $activity->due_at,
            'completed_at' => $activity->completed_at,
        ],
    ]);
}


        return $items
            ->filter(fn ($item) => $item['date'])
            ->sortByDesc('date')
            ->values();
    }
}
