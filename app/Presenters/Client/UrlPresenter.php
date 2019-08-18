<?php

namespace App\Presenters\Client;

use App\Organization;
use App\Model\Person\Client;

class UrlPresenter
{
    /**
     * @var \App\Organization
     */
    private $organization;
    /**
     * @var \App\Model\Person\Client
     */
    private $client;

    /**
     * UrlPresenter constructor.
     *
     * @param \App\Organization $organization
     * @param \App\Model\Person\Client $client
     */
    public function __construct(Organization $organization, Client $client)
    {
        $this->organization = $organization;
        $this->client = $client;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if(method_exists($this, $key))
        {
            return $this->$key();
        }
        return $this->$key;
    }

    /**
     * @return string
     */
    public function edit()
    {
        return route('person.clients.edit', [
            'organization' => $this->organization,
            'client' => $this->client
        ]);
    }

    /**
     * @return string
     */
    public function update()
    {
        return route('person.clients.update',[
            'organization' => $this->organization,
            'client' => $this->client
        ]);
    }

    /**
     * @return string
     */
    public function delete()
    {
        return route('person.clients.destroy', [
            'organization' => $this->organization,
            'client' => $this->client
        ]);
    }

    /**
     * @return string
     */
    public function restore()
    {
        return route('person.clients.destroy', [
            'organization' => $this->organization,
            'client' => $this->client
        ]);
    }

}
