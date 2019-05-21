<?php

namespace App\Http\Middleware;

use App\Organization;
use Closure;

class ApplicationAuthorizedMiddleware
{
    private $organization;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->getInstance($request);

        if ($this->notAccessToOrganization()) {
            return redirect()->route('home.index');
        }

        abort_unless($this->organization->user->isSubscribed(),
            403,
            'Lo sentimos, pero la suscripción ha caducado, póngase en contacto con el propietario del sitio para evitar la suspensión.');

        return $next($request);
    }

    protected function getInstance($request)
    {
        $this->organization = Organization::whereSlug($request->slug)->firstOrFail();
    }

    protected function notAccessToOrganization()
    {
        return $this->organization->contributors->pluck('id')->search(auth()->id()) === false;
    }
}
