<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.user.{userId}', function (User $user, int $userId): bool {
    if ((int) $user->id === (int) $userId) {
        return true;
    }

    return (bool) $user->is_admin;
});
