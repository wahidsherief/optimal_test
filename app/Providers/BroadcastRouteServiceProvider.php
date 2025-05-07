<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastRouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // broadcast routes
        // Broadcast routes for Employer API
        Broadcast::routes([
            'middleware' => ['auth:employer-api'],
            'prefix' => 'api/employer',
        ]);

        // Broadcast routes for Candidate API
        Broadcast::routes([
            'middleware' => ['auth:candidate-api'],
            'prefix' => 'api/candidate',
        ]);

        // Broadcast routes for Admin API
        Broadcast::routes([
            'middleware' => ['auth:admin-api'],
            'prefix' => 'api/admin',
        ]);

        require base_path('routes/channels.php');
    }
}
