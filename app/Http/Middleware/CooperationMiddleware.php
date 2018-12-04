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
        HoomdossierSession::setCooperation($cooperation);

        // Set as default URL parameter
        if (HoomdossierSession::hasCooperation()) {
            //$cooperation = Cooperation::find($request->session()->get('cooperation'));
            if ($cooperation instanceof Cooperation) {
                \Log::debug('Setting default cooperation for URL -> '.$cooperation->id.' ('.$cooperation->slug.')');
                URL::defaults(['cooperation' => $cooperation->slug]);
            }
        }

        if ($request->session()->has('role_id') && !empty($request->session()->get('role_id'))){
	        \Log::debug("Session: role -> " . $request->session()->get('role_id'));
        }
        else {
        	\Log::debug("Session: no user role set.");
        }

        return $next($request);
    }
}
