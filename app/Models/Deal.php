<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function lastActivityAt(): ?\Carbon\Carbon
    {
        $dates = collect([
            $this->created_at,
            $this->proposals()->max('created_at'),
            $this->proposals()->max('sent_at'),
            $this->followUps()->max('sent_at'),
            $this->activities()->max('created_at'),
        ])->filter();

        return $dates->max();
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

}
