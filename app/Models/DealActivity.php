<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealActivity extends Model
{
    //public $timestamps = false;

    protected $fillable = [
        'deal_id',
        'user_id',
        'type',
        'label',
        'description',
        'meta',
        'due_at',
        'completed_at',
        //'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
