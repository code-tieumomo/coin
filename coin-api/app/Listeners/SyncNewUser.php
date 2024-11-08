<?php

namespace App\Listeners;

use App\Models\Assignment;
use Illuminate\Auth\Events\Registered;

class SyncNewUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        $publicAssignments = Assignment::active()->public()->get();
        $publicAssignments->each(function (Assignment $assignment) use ($user) {
            $assignment->users()->attach($user->id);
        });
    }
}
