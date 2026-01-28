<?php

namespace App\Providers;

use App\Models\Deal;
use App\Policies\DealPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Deal::class => DealPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
