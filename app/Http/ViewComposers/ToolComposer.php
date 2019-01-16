<?php

namespace App\Http\ViewComposers;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageView;
use App\Models\Step;
use Illuminate\View\View;

class ToolComposer
{
    public function create(View $view)
    {
        $cooperation = Cooperation::find(HoomdossierSession::getCooperation());

        $view->with('cooperation', app()->make('Cooperation'));
        $view->with('cooperationStyle', app()->make('CooperationStyle'));

        // since we dont really need to load the vars in this view since its just a alert
        // the alert is also loaded on pages where a user is not authenticated so some vars would fail.
        $excludedViews = ['cooperation.tool.components.alert'];

        if (!in_array($view->getName(), $excludedViews)) {
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
            $view->with('myUnreadMessagesCount', PrivateMessageView::getTotalUnreadMessages());

            $view->with('steps', $cooperation->getActiveOrderedSteps());
            $view->with('interests', Interest::orderBy('order')->get());
            $view->with('currentStep', Step::where('slug', str_replace(['tool', '/'], '', request()->getRequestUri()))->first());
        }
    }
}
