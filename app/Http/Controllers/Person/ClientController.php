<?php

namespace App\Http\Controllers\Person;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\{Organization, Model\Person\Client};
use Illuminate\Support\Facades\{Request, Redirect};
use App\Http\Requests\{StoreClientRequest, UpdateClientRequest};

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Organization $organization)
    {
        $this->authorize('view', Client::class);

        return Inertia::render('Person/Client/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organization' => $organization,
            'clients' => $organization->clients()
                ->currentBills()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->transform(function ($client) {
                    return collect($client)->merge(['url' => ['edit' => $client->url->edit]]);
                })
                ->only('id', 'name', 'last_name', 'id_card', 'phone', 'all_due_amount', 'deleted_at', 'url')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Organization $organization)
    {
        $this->authorize('create', Client::class);

        return Inertia::render('Person/Client/Create', [
            'organization' => $organization
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreClientRequest $request
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreClientRequest $request, Organization $organization)
    {
        $this->authorize('create', Client::class);

        $organization->clients()->create($request->validated());

        return Redirect::route('person.clients', $organization)->with('success', __('client.created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Organization $organization
     * @param \App\Model\Person\Client $client
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Organization $organization, Client $client)
    {
        $this->authorize('update', $client);

        return Inertia::render('Person/Client/Edit', [
            'organization' => $organization,
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
     * @param \App\Http\Requests\UpdateClientRequest $request
     * @param \App\Organization $organization
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateClientRequest $request, Organization $organization, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return Redirect::to($client->url->edit)->with('success', __('client.updated'));
    }

    /**
     * @param \App\Organization $organization
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Organization $organization, Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return Redirect::to($client->url->edit);
    }

    /**
     * @param \App\Organization $organization
     * @param \App\Model\Person\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Organization $organization, Client $client)
    {
        $this->authorize('delete', $client);

        $client->restore();

        return Redirect::to($client->url->edit);
    }
}
