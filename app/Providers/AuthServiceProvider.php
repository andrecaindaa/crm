<?php

namespace App\Providers;

use App\Models\Deal;
use App\Models\Entity;
use App\Models\Person;
use App\Models\CalendarEvent;

use App\Policies\DealPolicy;
use App\Policies\EntityPolicy;
use App\Policies\PersonPolicy;
use App\Policies\CalendarEventPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Deal::class => DealPolicy::class,
        Entity::class => EntityPolicy::class,
        Person::class => PersonPolicy::class,
        CalendarEvent::class => CalendarEventPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
