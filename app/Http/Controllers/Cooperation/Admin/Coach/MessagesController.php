<?php

namespace App\Http\Controllers\Cooperation\Admin\Coach;

use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\Admin\Coach\MessagesRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Services\BuildingCoachStatusService;
use App\Services\BuildingPermissionService;
use App\Services\InboxService;
use App\Services\MessageService;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
    public function index(Cooperation $cooperation)
    {
        $myCreatedMessages = PrivateMessage::myCreatedMessages()->get();

        $mainMessages = PrivateMessage::mainMessages()->get();

        $mainMessages = collect($mainMessages)->merge($myCreatedMessages)->unique('id');

        return view('cooperation.admin.coach.messages.index', compact('mainMessages'));
    }

    public function edit(Cooperation $cooperation, $buildingId)
    {
        $privateMessages = PrivateMessage::conversation($buildingId)->get();

        $this->authorize('edit', $privateMessages->first());

        $groupParticipants = PrivateMessage::getGroupParticipants($buildingId);

        return view('cooperation.admin.coach.messages.edit', compact('privateMessages', 'buildingId', 'groupParticipants'));
    }

    public function store(Cooperation $cooperation, MessagesRequest $request)
    {
        MessageService::create($request);

        return redirect()->back();
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

        // remove the building permission
        BuildingPermissionService::revokePermission($fromId, $building->id);

        // no coach connected so the status gos back to in consideration, the coordinator can take further actions from now on.
        PrivateMessage::find($privateMessageRequestId)->update(['status' => PrivateMessage::STATUS_IN_CONSIDERATION]);

        // revoke the access for the coach to talk with the resident
        BuildingCoachStatusService::revokeAccess($fromId, $building->id, $privateMessageRequestId);

        return redirect()->back();
    }
}
