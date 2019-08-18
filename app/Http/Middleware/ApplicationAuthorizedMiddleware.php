<?php

namespace App\Http\Middleware;

use App\Model;
use App\Organization;
use Closure;
use Illuminate\Support\Facades\App;

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

        foreach ($request->route()->parameters() as $parameter) {
            if ($parameter instanceof Model && isset($parameter->organization_id)) {
               abort_unless($this->organization->id == $parameter->organization_id, 404);
            }
        }

        return $next($request);
    }

    protected function getInstance($request)
    {
        $this->organization = $request->organization instanceof Organization
            ? $request->organization
            : abort(404);
    }

    protected function notAccessToOrganization()
    {
        return $this->organization->contributors->pluck('id')->search(auth()->id()) === false;
    }
}
