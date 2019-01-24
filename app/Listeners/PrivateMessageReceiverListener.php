<?php

namespace App\Listeners;

use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;

class PrivateMessageReceiverListener
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
        $groupParticipants = PrivateMessage::getGroupParticipants($event->privateMessage->building_id);
        $buildingFromOwner = Building::find($event->privateMessage->building_id);
        $privateMessage = PrivateMessage::find($event->privateMessage->id);

        // now we creat for every group participant a privatemessageview
        foreach ($groupParticipants as $groupParticipant) {
            // check the group participant is the owner of the building and the send message is private
            $isMessagePrivateAndGroupParticipantOwnerFromBuilding = $buildingFromOwner->user_id == $groupParticipant->id && PrivateMessage::isPrivate($privateMessage);

            if ($groupParticipant->id != \Auth::id() && ! $isMessagePrivateAndGroupParticipantOwnerFromBuilding) {
                PrivateMessageView::create([
                    'private_message_id' => $event->privateMessage->id,
                    'user_id' => $groupParticipant->id,
                ]);
            }
        }

        // avoid unnecessary privateMessagesViews, we dont want to create a row for the user itself
        if (! \Auth::user()->hasRoleAndIsCurrentRole(['coordinator'])) {
            // since a cooperation is not a 'participant' of a chat we need to create a row for the manually
            PrivateMessageView::create([
                'private_message_id' => $event->privateMessage->id,
                'cooperation_id' => HoomdossierSession::getCooperation(),
            ]);
        }
    }
}