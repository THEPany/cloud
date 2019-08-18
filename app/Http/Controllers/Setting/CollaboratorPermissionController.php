<?php

namespace App\Http\Controllers\Setting;

use App\User;
use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;
use Silber\Bouncer\Database\Ability;

class CollaboratorPermissionController extends Controller
{
    /**
     * @param \App\Organization $organization
     * @return \Inertia\Response
     */
    public function index(Organization $organization)
    {
        return Inertia::render('Setting/Collaborator/Index', [
            'organization' => $organization,
            'contributors' => $organization->contributors()->paginate()
        ]);
    }


    public function show(Organization $organization, User $user)
    {
        return Inertia::render('Setting/Collaborator/Permission', [
            'organization' => $organization,
            'permissions' => Ability::all()->groupBy('entity_type'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => $user->getAbilities()
            ]
        ]);
    }

    public function store(Organization $organization, User $user)
    {
        request()->validate(['permissions.*' => ['required','numeric']]);

        $user->getAbilities()->each(function ($permission) use ($user) {
            $user->disallow($permission);
        });

        collect(request()->permissions)->each(function ($permission) use ($user) {
            $user->allow($permission);
        });

        return redirect()->route('setting.permissions.show', ['organization' => $organization, 'user' => $user])->with('success', 'Permisos asignados correctamente.');
    }
}
