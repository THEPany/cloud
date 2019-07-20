<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\{User,Organization};
use Silber\Bouncer\Database\{Ability, Role};

class AppController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('App/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }

    public function collaborator($slug)
    {
        return Inertia::render('App/Collaborator', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'contributors' => $organization->contributors()->paginate()
        ]);
    }

    public function assignPermission($slug, User $user)
    {
        return Inertia::render('App/Permission', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'permissions' => Ability::all()->groupBy('entity_type'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $user->getAbilities()
            ]
        ]);
    }

    public function permission($slug, User $user)
    {
        request()->validate(['permissions.*' => ['required','numeric']]);

        $user->getAbilities()->each(function ($permission) use ($user) {
            $user->disallow($permission);
        });

        collect(request()->permissions)->each(function ($permission) use ($user) {
            $user->allow($permission);
        });

        return redirect()->route('apps.collaborator', $slug)
            ->with('success', 'Permisos asignados correctamente.');
    }
}
