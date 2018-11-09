<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\BuildingCoachStatusService;
use App\Services\InboxService;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $myCreatedMessages = PrivateMessage::myCreatedMessages()->get();
        $mainMessages = PrivateMessage::mainMessages()->get();

        $mainMessages = collect($mainMessages)->merge($myCreatedMessages)->unique('id');

        return view('cooperation.admin.coach.messages.index', compact('mainMessages'));
    }

    public function edit(Cooperation $cooperation, $mainMessageId)
    {
        $privateMessages = PrivateMessage::conversation($mainMessageId)->get();

        InboxService::setRead($mainMessageId);

        return view('cooperation.admin.coach.messages.edit', compact('privateMessages', 'mainMessageId'));
    }


    public function revokeAccess(Cooperation $cooperation, Request $request)
    {
        $currentChatMainMessage = $request->get('main_message_id');

        // the resident himself cannot start a chat with a coach, resident or whatsoever.
        // the main message is started from the coach or coordinator

        // this is NOT the request to the cooperation.
        $mainMessage = PrivateMessage::find($currentChatMainMessage);

        // the building from the user / resident
        $building = Building::where('user_id', $mainMessage->to_user_id)->first();

        // either the coach or the coordinator, or someone with a higher role then resident.
        $fromId = $mainMessage->from_user_id;

        // get the most recent conversation between that user and coach
        $buildingCoachStatus = BuildingCoachStatus::where('coach_id', $fromId)->where('building_id', $building->id)->get()->last();

        $privateMessageRequestId = $buildingCoachStatus->private_message_id;

        // no coach connected so the status gos back to in consideration, the coordinator can take further actions from now on.
        PrivateMessage::find($privateMessageRequestId)->update(['status' => PrivateMessage::STATUS_IN_CONSIDERATION]);

        // revoke the access for the coach to talk with the resident
        BuildingCoachStatusService::revokeAccess($fromId, $building->id, $privateMessageRequestId);

        return redirect()->back();

    }
    public function store(Cooperation $cooperation, MessagesRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
    }
}
