<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealFollowUp extends Model
{
   protected $fillable = [
        'deal_id',
        'sent_by',
        'sent_at',
        'next_send_at',
        'active',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'next_send_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

     public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
