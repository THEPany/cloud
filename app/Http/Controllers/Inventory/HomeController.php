<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Organization $organization)
    {
        return Inertia::render('Inventory/Home/Index', [
            'organization' => $organization
        ]);
    }
}
