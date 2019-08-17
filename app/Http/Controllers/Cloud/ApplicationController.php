<?php

namespace App\Http\Controllers\Cloud;

use Inertia\Inertia;
use App\Organization;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function index($slug)
    {
        return Inertia::render('Cloud/Index', [
            'organization' => Organization::whereSlug($slug)->firstOrFail()
        ]);
    }
}
