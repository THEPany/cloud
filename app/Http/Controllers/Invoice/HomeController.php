<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('Invoice/Home/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }
}
