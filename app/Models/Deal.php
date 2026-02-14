<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deal extends Model
{
    protected $fillable = [
        'owner_id',
        'entity_id',
        'person_id',
        'title',
        'value',
        'stage',
        'probability',
        'expected_close_date',
    ];

    /**
     * Obtém a data da última atividade do negócio
     */
    public function lastActivityAt(): ?Carbon
    {
        $dates = collect([
            $this->created_at,
            $this->proposals()->max('created_at'),
            $this->proposals()->max('sent_at'),
            $this->followUps()->max('sent_at'),
            $this->activities()
                ->where('type', '!=', 'system_inactive')
                ->max('created_at'),
        ])
        ->filter()
        ->map(function ($date) {
            // Converter strings para Carbon se necessário
            if (is_string($date)) {
                return Carbon::parse($date);
            }
            return $date;
        });

        $maxDate = $dates->max();

        // Garantir que retorna Carbon ou null
       return $maxDate instanceof Carbon
    ? $maxDate
    : ($maxDate ? Carbon::parse($maxDate) : null);

    }

    /**
     * Dias desde a última atividade
     */
    public function getInactiveDaysAttribute(): int
    {
        $lastActivity = $this->lastActivityAt();
        return $lastActivity ? $lastActivity->diffInDays(now()) : 0;
    }

    /**
     * Verifica se o negócio está inativo
     */
    public function isInactive(int $threshold = 5): bool
    {
        return $this->inactive_days >= $threshold;
    }

    protected $casts = [
        'expected_close_date' => 'date',
        'value' => 'decimal:2',
    ];

    public function products()
    {

        return $this->belongsToMany(Product::class, 'deal_products')
            ->withPivot(['quantity', 'unit_price', 'total'])
            ->withTimestamps();
    }



    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function proposals()
    {
        return $this->hasMany(DealProposal::class);
    }


    public function followUps()
    {
        return $this->hasMany(DealFollowUp::class);
    }

    public static function stages(): array
    {
        return config('deals.stages');
    }

    public function isInStage(string $stage): bool
    {
        return $this->stage === $stage;
    }

    public function activities()
    {
        return $this->hasMany(DealActivity::class);
    }

    public function getRiskScoreAttribute(): int
    {
        $score = 0;

        if ($this->inactive_days >= 5) $score += 40;
        if ($this->stage === 'negotiation') $score += 20;
        if ($this->value > 5000) $score += 20;
        if ($this->followUps()->where('active', true)->exists()) $score -= 10;

        return max(0, min(100, $score));
    }


}
