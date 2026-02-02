<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealFollowUp extends Model
{
    protected $fillable = [
        'deal_id',
        'next_send_at',
        'active',
    ];

    protected $casts = [
        'next_send_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
}
