<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Organization $organization)
    {
        return Inertia::render('Invoice/Home/Index', [
            'organization' => $organization
        ]);
    }
}
