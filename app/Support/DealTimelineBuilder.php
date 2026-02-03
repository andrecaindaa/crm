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
         * Propostas carregadas
         */
        foreach ($deal->proposals as $proposal) {
            $items->push([
                'type' => 'proposal',
                'title' => 'Proposta adicionada',
                'description' => $proposal->original_name,
                'date' => $proposal->created_at,
                'user' => null,
                'meta' => [
                    'proposal_id' => $proposal->id,
                ],
            ]);

            if ($proposal->sent_at) {
                $items->push([
                    'type' => 'email',
                    'title' => 'Proposta enviada por email',
                    'description' => 'Email enviado ao cliente',
                    'date' => $proposal->sent_at,
                    'user' => $proposal->sender
                        ? [
                            'id' => $proposal->sender->id,
                            'name' => $proposal->sender->name,
                        ]
                        : null,
                    'meta' => [
                        'proposal_id' => $proposal->id,
                    ],
                ]);
            }
        }

        /**
         * Follow-ups automáticos
         */
        foreach ($deal->followUps ?? [] as $followUp) {
            $items->push([
                'type' => 'follow_up',
                'title' => 'Follow-up automático',
                'description' => $followUp->email_body,
                'date' => $followUp->sent_at,
                'user' => $followUp->sender
                    ? [
                        'id' => $followUp->sender->id,
                        'name' => $followUp->sender->name,
                    ]
                    : null,
                'meta' => [],
            ]);
        }

        /**
         * Ordenação final (mais recente primeiro)
         */
        return $items
            ->filter(fn ($item) => $item['date'])
            ->sortByDesc('date')
            ->values();
    }
}
