<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user)
    {
        try {
            $user->addToIndex();
        } catch (\Exception $e) {
            Log::warning('User failed to index. '. json_encode($user));
            return false;
        }
    }

    public function updated(User $user)
    {
        $user->updateIndex();
    }

    public function deleted(User $user)
    {
        $user->removeFromIndex();
    }
}
