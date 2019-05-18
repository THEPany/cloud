<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MarketingController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Marketing/Index');
    }
}
