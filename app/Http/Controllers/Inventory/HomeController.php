<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('Inventory/Home/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }
}
