<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = [
        'user_id',
        'entity_id',
        'name',
        'email',
        'phone',
        'position',
        'notes',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

