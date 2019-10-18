<?php

namespace App\Listeners;

use App\Models\Log;
use Carbon\Carbon;

class LogRegisteredUserListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        Log::create([
            'user_id' => $event->user->id,
            'building_id' => $event->user->building->id,
            'message' => __('woningdossier.log-messages.registered-user', [
                'full_name' => $event->user->getFullName(),
                'time' => Carbon::now(),
            ]),
        ]);
    }
}
