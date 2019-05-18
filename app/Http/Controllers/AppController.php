<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AppController extends Controller
{
    public function index($organization)
    {
        return Inertia::render('App/Index', [
            'organization' => $organization
        ]);
    }

    public function other($organization)
    {
        return Inertia::render('App/Other', [
            'organization' => $organization
        ]);
    }
}
