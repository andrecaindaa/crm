<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Person;

class Entity extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'vat',
        'email',
        'phone',
        'address',
        'status',
    ];

    public function people()
    {
        return $this->hasMany(Person::class);
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

