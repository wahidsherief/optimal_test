<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('index');
});


// Route::get('/free', function () {
//     // Get list of all users from candidates and employers
//     $candidates = \App\Models\Candidate::all();
//     $employers = \App\Models\Employer::all();

//     // Process candidates
//     foreach ($candidates as $candidate) {
//         $result = [
//             'role' => 'candidate', // Assuming 'candidate' is the role
//             'user' => $candidate,
//         ];

//         if (isset($result['role']) && isset($result['user']->id)) {
//             $result['user_type'] = $result['role'];
//             // Assuming $this->orderService is available in this context
//             app(\App\Services\OrderService::class)->saveFreeProduct($result['user_type'], $result['user']->id);
//         }
//     }

//     // Process employers
//     foreach ($employers as $employer) {
//         $result = [
//             'role' => 'employer', // Assuming 'employer' is the role
//             'user' => $employer,
//         ];

//         if (isset($result['role']) && isset($result['user']->id)) {
//             $result['user_type'] = $result['role'];
//             // Assuming $this->orderService is available in this context
//             app(\App\Services\OrderService::class)->saveFreeProduct($result['user_type'], $result['user']->id);
//         }
//     }

//     return 'Free products processed for all candidates and employers.';
// });
