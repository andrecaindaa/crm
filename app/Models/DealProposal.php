<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealProposal extends Model
{
    protected $fillable = [
        'deal_id',
        'file_path',
        'original_name',
        'sent_at',
        'sent_by',
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

