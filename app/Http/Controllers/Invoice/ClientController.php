<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Model\Invoice\Client;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function index($slug)
    {
        return Inertia::render('Invoice/Client/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'clients' => $organization->clients()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->only('id', 'name', 'last_name', 'id_card', 'phone', 'deleted_at')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function create($slug)
    {
        return Inertia::render('Invoice/Client/Create', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($slug)
    {
        $organization = Organization::whereSlug($slug)->firstOrFail();

        $organization->clients()->create(
            request()->validate([
                'name' => ['required', 'string', 'min:3', 'max:100'],
                'last_name' => ['required', 'string', 'min:3', 'max:100'],
                'id_card' => ['required', 'string', 'min:13', 'max:13', 'unique:invoice_clients'],
                'email' => ['nullable', 'string', 'email', 'unique:invoice_clients'],
                'phone' => ['nullable', 'string', 'min:10', 'max:13', 'unique:invoice_clients'],
            ])
        );

        return Redirect::route('invoice.clients.index', $slug)->with(['flash_success' => 'Cliente creado correctamente.']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $slug
     * @param \App\Model\Invoice\Client $client
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($slug, Client $client)
    {
        return Inertia::render('Invoice/Client/Edit', [
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
     * @param \App\Model\Invoice\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($slug, Client $client)
    {
        $client->update(
            request()->validate([
                'name' => ['required', 'string', 'min:3', 'max:100'],
                'last_name' => ['required', 'string', 'min:3', 'max:100'],
                'id_card' => ['required', 'string', 'min:13', 'max:13', Rule::unique('invoice_clients')->ignore($client->id)],
                'email' => ['nullable', 'string', 'email', Rule::unique('invoice_clients')->ignore($client->id)],
                'phone' => ['nullable', 'string', 'min:10', 'max:13', Rule::unique('invoice_clients')->ignore($client->id)],
            ])
        );
        return Redirect::route('invoice.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ])->with(['flash_success' => 'Cliente actualizado correctamente.']);
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Client $client
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($slug, Client $client)
    {
        $client->delete();

        return Redirect::route('invoice.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ]);
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Client $client
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($slug, Client $client)
    {
        $client->restore();

        return Redirect::route('invoice.clients.edit', [
            'slug' => $slug,
            'client' => $client
        ]);
    }
}
