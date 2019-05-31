<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Organization;

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
}
