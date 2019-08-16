<?php

namespace App\Http\Controllers\Person;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('Person/Home/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }
}
