<?php

namespace App\Http\Controllers\Cloud;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Cloud/Home/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => auth()->user()->collaboratingOrganizations()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->with('user:id,name')
                ->paginate()->only('name', 'email', 'slug', 'user')
        ]);
    }
}
