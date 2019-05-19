<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Organization;

class AppController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('App/Index', [
            'organization' => Organization::whereSlug($slug)->first()->only('name', 'slug')
        ]);
    }

    public function other($organization)
    {
        return Inertia::render('App/Other', [
            'organization' => $organization
        ]);
    }
}
