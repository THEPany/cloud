<?php

namespace App\Http\Controllers\Person;

use Inertia\Inertia;
use App\Organization;
use App\Model\Invoice\{Bill};
use App\Model\Person\{Client};
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Request, Redirect};

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $slug
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index($slug)
    {
        $this->authorize('view', Client::class);

        return Inertia::render('Person/Client/Index', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'filters' => Request::all('search', 'trashed'),
            'clients' => $organization->clients()
                ->with(['bills' => function ($bill) {
                    $bill->with('payments:id,bill_id,paid_out')->where('status', Bill::STATUS_CURRENT);
                }])
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->only('id', 'name', 'last_name', 'id_card', 'phone', 'all_due_amount', 'deleted_at')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $slug
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($slug)
    {
        $this->authorize('create', Client::class);

        return Inertia::render('Person/Client/Create', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store($slug)
    {
        $this->authorize('create', Client::class);

        $organization = Organization::whereSlug($slug)->firstOrFail();

        $organization->clients()->create(
            request()->validate([
                'name' => ['required', 'string', 'min:3', 'max:100'],
                'last_name' => ['required', 'string', 'min:3', 'max:100'],
                'id_card' => ['required', 'string', 'min:13', 'max:13', Rule::unique('person_clients')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })],
                'email' => ['nullable', 'string', 'email', Rule::unique('person_clients')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })],
                'phone' => ['nullable', 'string', 'min:10', 'max:13', Rule::unique('person_clients')->where(function ($query) use ($organization) {
                    return $query->where('organization_id', $organization->id);
                })],
            ])
        );

        return Redirect::route('person.clients', $slug)->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $slug
     * @param \App\Model\Person\Client $client
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($slug, Client $client)
    {
        $this->authorize('update', $client);

        return Inertia::render('Person/Client/Edit', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'last_name' => $client->last_name,
                'id_card' => $client->id_card,
                'email' => $client->email,
                'phone' => $client->phone,
                'deleted_at' => $client->deleted_at,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $slug
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($slug, Client $client)
    {
        $this->authorize('update', $client);

        $client->update(
            request()->validate([
                'name' => ['required', 'string', 'min:3', 'max:100'],
                'last_name' => ['required', 'string', 'min:3', 'max:100'],
                'id_card' => ['required', 'string', 'min:13', 'max:13', Rule::unique('person_clients')->ignore($client->id)],
                'email' => ['nullable', 'string', 'email', Rule::unique('person_clients')->ignore($client->id)],
                'phone' => ['nullable', 'string', 'min:10', 'max:13', Rule::unique('person_clients')->ignore($client->id)],
            ])
        );

        return Redirect::route('person.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ])->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($slug, Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return Redirect::route('person.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ]);
    }

    /**
     * @param $slug
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore($slug, Client $client)
    {
        $this->authorize('delete', $client);

        $client->restore();

        return Redirect::route('person.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ]);
    }
}
