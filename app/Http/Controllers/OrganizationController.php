<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Mail\OrganizationInvitationEmail;
use App\{Organization, Plan, Restriction, User};

class OrganizationController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $organizations = Organization::query()
            ->whereHas('contributors', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('name')
            ->filter(Request::only('search', 'trashed'))
            ->paginate()
            ->only('id', 'user_id', 'name', 'slug', 'email', 'phone', 'address', 'city', 'region', 'country', 'postal_code', 'deleted_at');

        return Inertia::render('Organization/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => $organizations,
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return Inertia::render('Organization/Create');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        abort_unless($this->restrictionOrganization(), 403, 'Límite alcanzado, por favor actualice su plan.');

        abort_unless(auth()->user()->isSubscribed(), 403);

        request()->user()->organizations()->create(
            request()->validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:150', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );

        return redirect()->route('organizations.index')->with(['flash_success' => 'Organización creada correctamente.']);
    }

    /**
     * @param \App\Organization $organization
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Organization $organization)
    {
        return Inertia::render('Organization/Edit', [
            'users' => User::whereNotin('id', $organization->contributors()->get()->map->id->toArray())->get()->map->only('id', 'name', 'email'),
            'organization' => [
                'id' => $organization->id,
                'user_id' => $organization->user_id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'deleted_at' => $organization->deleted_at,
                'users' => $organization->contributors()->orderBy('name')->get()->map->only('id', 'name', 'email', 'phone'),
            ],
        ]);
    }

    /**
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Organization $organization)
    {
        abort_unless(auth()->user()->isSubscribed()
            && auth()->id() == $organization->user_id, 403);

        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );
        return Redirect::route('organizations.edit', $organization)->with(['flash_success' => 'Organización actualizada correctamente.']);
    }

    /**
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();
        return Redirect::route('organizations.edit', $organization);
    }

    /**
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Organization $organization)
    {
        $organization->restore();
        return Redirect::route('organizations.edit', $organization);
    }

    /**
     * @param \App\Organization $organization
     * @param \App\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendInvitationLink(Organization $organization, User $user)
    {
        abort_unless($this->restrictionContributor($organization), 403, 'Límite alcanzado, por favor actualice su plan.');
        Mail::to($user)->send(new OrganizationInvitationEmail($organization, $user));
        return Redirect::route('organizations.edit', $organization);
    }

    /**
     * @param \App\Organization $organization
     * @param \App\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invitation(Organization $organization, User $user)
    {
        $organization->addContributor($user);
        return Redirect::route('home.index');
    }

    /**
     * @param \App\Organization $organization
     * @param \App\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeContributor(Organization $organization, User $user)
    {
        abort_if($organization->user_id === $user->id, 403, 'No se puede eliminar el propietario de la organización.');
        $organization->removeContributor($user);
        return Redirect::route('organizations.edit', $organization);
    }

    /**
     * ------------------------------
     * Restriction Plan Organization
     * ------------------------------
     */
    protected function restrictionOrganization()
    {
        try {
            $plan = Plan::query()
                ->whereBraintreePlan(auth()->user()->subscription('main')->braintree_plan)
                ->first();

            $restriction = Restriction::query()
                ->whereRestrictionKeyAndPlanId(Organization::RESTRICTION_ORGANIZATION, $plan->id)
                ->first();

            return Organization::query()
                    ->whereHas('contributors', function ($query) {
                        $query->where('user_id', auth()->id());
                    })->count() < $restriction->restriction_limit;
        }catch (\Exception $e) {
            return false;
        }
    }

    protected function restrictionContributor(Organization $organization)
    {
        try {
            $plan = Plan::query()
                ->whereBraintreePlan(auth()->user()->subscription('main')->braintree_plan)
                ->first();

            $restriction = Restriction::query()
                ->whereRestrictionKeyAndPlanId(Organization::RESTRICTION_CONTRIBUTOR, $plan->id)
                ->first();

            return $organization->contributors()->count() < $restriction->restriction_limit;

        }catch (\Exception $e) {
            return false;
        }

    }
}
