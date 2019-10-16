<?php

namespace App\Http\Controllers\Cooperation\ConversationRequest;

use App\Events\UserAllowedAccessToHisBuilding;
use App\Helpers\HoomdossierSession;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cooperation\ConversationRequests\ConversationRequest;
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
        $userAlreadyHadContactWithCooperation = PrivateMessage::public()->conversation(HoomdossierSession::getBuilding())->first() instanceof PrivateMessage;

        // if the user is observing, he has nothing to do here.
        if (HoomdossierSession::isUserObserving()) {
            return redirect()->route('cooperation.tool.my-plan.index');
        }
        $measureApplication = MeasureApplication::where('short', $measureApplicationShort)->first();

        // set the measure application name if there is a measure application
        $measureApplicationName = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : '';

        $selectedOption = $option;

        return view('cooperation.conversation-requests.index', compact('selectedOption', 'measureApplicationName', 'userAlreadyHadContactWithCooperation'));
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
        $message     = $request->get('message', '');
        $allowAccess = 'on' == $request->get('allow_access', '');

        $building = HoomdossierSession::getBuilding(true);
        $user = $building->user;

        PrivateMessage::create(
            [
                // we get the selected option from the language file, we can do this cause the submitted value = key from localization
                'is_public'         => true,
                'from_user_id'      => $user->id,
                'from_user'         => $user->getFullName(),
                'message'           => $message,
                'to_cooperation_id' => $cooperation->id,
                'building_id'       => $building->id,
                'request_type'      => $action,
                'allow_access'      => $allowAccess,
            ]
        );

        $building->setStatus('pending');

        // if the user allows access we handle this with the event
        if ($allowAccess) {
            UserAllowedAccessToHisBuilding::dispatch($building);
        }

        return redirect()->route('cooperation.tool.my-plan.index')
                         ->with('success', __('woningdossier.cooperation.conversation-requests.store.success', [
                             'url' => route('cooperation.my-account.messages.index', compact('cooperation'))
                         ]));
    }
}
