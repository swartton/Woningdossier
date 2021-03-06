<?php

namespace App\Http\Middleware;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\User;
use Closure;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If logged in and sentry is found, add extra contextual information
        // which helps debugging exceptions
        if (\Auth::account() instanceof Account && \Auth::account()->user() instanceof User && app()->bound('sentry')) {
            /** @var Account $account */
            $account = Hoomdossier::account();
            $user = Hoomdossier::user();
            $building = HoomdossierSession::getBuilding(true);
            $inputSource = HoomdossierSession::getInputSource(true);
            $inputSourceValue = HoomdossierSession::getInputSourceValue(true);

            $u = [
                'account' => $account->id,
                'id' => $user->id ?? 'none',
                'role' => HoomdossierSession::currentRole(),
                'is_observing' => HoomdossierSession::isUserObserving() ? 'yes' : 'no',
                'is_comparing' => HoomdossierSession::isUserComparingInputSources() ? 'yes' : 'no',
                'input_source' => $inputSource->short,
                'operating_on_own_building' => optional($building)->user_id == ($user->id ?? 0) ? 'yes' : 'no',
                'operating_as' => $inputSourceValue->short,
                'all_session_data' => \App\Helpers\HoomdossierSession::all(),
            ];

            $tags = [
                'building:id' => optional($building)->id,
                'building:owner' => optional($building)->user_id,
            ];

            if (! optional($building)->user instanceof User) {
                \Log::error("SentryContext : building -> user is no instance of App\Models\User !! a: ".$account->id.', u: '.$user->id.', b: '.optional($building)->id);
            }

            \Sentry\configureScope(function (Scope $scope) use ($u, $tags) {
                $scope->setUser($u);
                $scope->setExtras($tags);
            });
        } else {
            // either the user is not set or sentry is not bound.
            \Auth::logout();
        }

        return $next($request);
    }
}
