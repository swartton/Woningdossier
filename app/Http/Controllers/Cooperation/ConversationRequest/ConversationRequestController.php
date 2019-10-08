<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\MeasureApplication;
use App\Models\PrivateMessage;

class ConversationRequestController extends Controller
{
    /**
     * Show the form.
     *
     * @param  Cooperation  $cooperation
     * @param  null  $option
     * @param  null  $measureApplicationShort
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Cooperation $cooperation, $option = null, $measureApplicationShort = null)
    {
        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }
        $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

        // set the measure application name if there is a measure application
        $measureApplicationName = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : '';
        $selectedOption = $option;
        $shouldShowOptionList = is_null($option) ? true : false;

        return view('cooperation.conversation-requests.index', compact('selectedOption', 'measureApplicationName', 'shouldShowOptionList'));
    }

    /**
     * Save the conversation request for whatever the conversation request may be.
     *
     * @param  ConversationRequest  $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ConversationRequest $request, Cooperation $cooperation)
    {
        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }
        $action      = $request->get('action', '');
        $message     = strip_tags($request->get('message', ''));
        $measureApplicationName = $request->get('measure_application_name', null);
        $allowAccess = 'on' == $request->get('allow_access', '');
        $message = is_null($measureApplicationName) ? $message : "<b>{$measureApplicationName}: </b>{$message}";

        $cooperation = HoomdossierSession::getCooperation(true);
        $building = HoomdossierSession::getBuilding(true);


        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'is_public'         => true,
                'from_user_id'      => Hoomdossier::user()->id,
                'from_user'         => \App\Helpers\Hoomdossier::user()->getFullName(),
                'message'           => $message,
                'to_cooperation_id' => $cooperation->id,
                'building_id'       => $building->id,
                'request_type'      => $action,
                'allow_access'      => $allowAccess,
            ]
        );


        $building->setStatus('pending');

        // if the user allows access to his building on the request, log the activity.
        if ($allowAccess) {
            event(new UserAllowedAccessToHisBuilding());
        }

        return redirect()->route('cooperation.tool.my-plan.index')
                         ->with('success', __('woningdossier.cooperation.conversation-requests.store.success', [
                             'url' => route('cooperation.my-account.messages.index', compact('cooperation'))
                         ]));
    }
}
