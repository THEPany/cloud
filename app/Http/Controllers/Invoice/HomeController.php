<?php

namespace App\Http\Controllers\Invoice;

use App\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('Invoice/Home/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }
}
