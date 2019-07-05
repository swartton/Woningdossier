<?php

namespace App\Http\Controllers\Cooperation\Admin;

use App\Helpers\Hoomdossier;
use App\Http\Requests\Cooperation\Admin\MessageRequest;
use App\Models\Building;
use App\Models\BuildingCoachStatus;
use App\Models\Cooperation;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MessagesController extends Controller
{
    protected $fragment;

    public function __construct(Cooperation $cooperation, Request $request)
    {
        if ($request->has('fragment')) {
            $this->fragment = $request->get('fragment');
        }
    }

    public function index(Cooperation $cooperation)
    {

        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole('coach')) {
            $connectedBuildingsByUserId = BuildingCoachStatus::getConnectedBuildingsByUser(Hoomdossier::user(), $cooperation);
            $buildingIds                = $connectedBuildingsByUserId->pluck('building_id')->all();
        } else {
            $privateMessages = PrivateMessage::where('to_cooperation_id', $cooperation->id)
                ->conversationRequest()
                ->get();

            $buildingIds     = $privateMessages->pluck('building_id')->all();
        }

        $buildings = Building::whereHas('privateMessages')->findMany($buildingIds);

        return view('cooperation.admin.messages.index', compact('buildings'));
    }

    /**
     * Method that handles sending messages for the /admin section
     *
     * @param  Cooperation     $cooperation
     * @param  MessageRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function sendMessage(Cooperation $cooperation, MessageRequest $request)
    {
        MessageService::create($request);

        return redirect(back()->getTargetUrl().$this->fragment);
    }
}
