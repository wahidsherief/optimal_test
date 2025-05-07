<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('employer.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('candidate.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
