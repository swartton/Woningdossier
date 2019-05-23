<?php

namespace App\Http\Middleware;

use App\Helpers\HoomdossierSession;
use App\Models\Cooperation;
use Closure;
use Illuminate\Support\Facades\URL;

class CooperationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cooperation = $request->route()->parameter('cooperation');

        if (! $cooperation instanceof Cooperation) {
            // No valid cooperation subdomain. Return to global index.
            \Log::debug('No cooperation found');

            return redirect()->route('index');
        }

        \Log::debug('Session: cooperation -> '.$cooperation->id.' ('.$cooperation->slug.')');

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            //$cooperation = Cooperation::find($request->session()->get('cooperation'));
            if ($cooperation instanceof Cooperation) {
                \Log::debug('Setting default cooperation for URL -> '.$cooperation->id.' ('.$cooperation->slug.')');
                URL::defaults(['cooperation' => $cooperation->slug]);
            }
        } else {
            // only set it when there is no cooperation.
            HoomdossierSession::setCooperation($cooperation);
        }

        if (HoomdossierSession::hasRole() && ! empty(HoomdossierSession::getRole())) {
            \Log::debug('Session: role -> '.HoomdossierSession::getRole());

            if (HoomdossierSession::isUserComparingInputSources()) {
                \Log::debug('Session: user is comparing his data / value to that from a '.HoomdossierSession::getCompareInputSourceShort());
            } else {
                \Log::debug('Session: user is not comparing his data');
            }
        } else {
            \Log::debug('Session: no user role set.');
        }

        return $next($request);
    }
}
