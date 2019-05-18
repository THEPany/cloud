<?php

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppController extends Controller
{
    public function index($organization)
    {
        return Inertia::render('App/Index', [
            'organization' => Organization::whereSlug($organization)->first()->only('name', 'slug')
        ]);
    }

    public function other($organization)
    {
        return Inertia::render('App/Other', [
            'organization' => $organization
        ]);
    }
}
