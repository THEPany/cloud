<?php

namespace App\Http\Controllers\Setting;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Organization $organization)
    {
        return Inertia::render('Setting/Home/Index', [
            'organization' => $organization
        ]);
    }
}
