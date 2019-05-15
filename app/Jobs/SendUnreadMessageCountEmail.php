<?php

namespace App\Jobs;

use App\Mail\UnreadMessagesEmail;
use App\Models\Building;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\NotificationSetting;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUnreadMessageCountEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $cooperation;
    protected $building;
    protected $notificationSetting;

    /**
     * SendUnreadMessageCountEmail constructor.
     *
     * @param  User  $user
     * @param  NotificationSetting  $notificationSetting
     */
    public function __construct(User $user, NotificationSetting $notificationSetting)
    {
        $this->notificationSetting = $notificationSetting;
        $this->user                = $user;
        $this->cooperation         = $user->cooperations()->first();
        $this->building            = $user->buildings()->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->building instanceof Building) {

            // get the unread message for a building id
            $unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForUser($this->user, $this->cooperation);

            // only notify a user if he has unread messages.
            if ($unreadMessageCount > 0) {
                // send the mail to the user
                \Mail::to($this->user->email)
                     ->send(new UnreadMessagesEmail($this->user, $this->cooperation, $unreadMessageCount));

                // after that has been done, update the last_notified_at to the current date
                $this->notificationSetting->last_notified_at = Carbon::now();
                $this->notificationSetting->save();
            }
        } else {
            \Log::debug('it seems like user id '.$this->user->id.' has no building!');
        }
    }
}
