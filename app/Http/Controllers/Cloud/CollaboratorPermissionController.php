<?php

namespace App\Http\Controllers\Cloud;

use App\User;
use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;
use Silber\Bouncer\Database\Ability;

class CollaboratorPermissionController extends Controller
{
    /**
     * @param $slug
     * @return \Inertia\Response
     */
    public function index($slug)
    {
        return Inertia::render('Cloud/Collaborator/Index', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'contributors' => $organization->contributors()->paginate()
        ]);
    }


    public function show($slug, User $user)
    {
        return Inertia::render('Cloud/Collaborator/Permission', [
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

    public function store($slug, User $user)
    {
        request()->validate(['permissions.*' => ['required','numeric']]);

        $user->getAbilities()->each(function ($permission) use ($user) {
            $user->disallow($permission);
        });

        collect(request()->permissions)->each(function ($permission) use ($user) {
            $user->allow($permission);
        });

        return redirect()->route('setting.permissions.show', ['slug' => $slug, 'user' => $user])->with('success', 'Permisos asignados correctamente.');
    }
}
