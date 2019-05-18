<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return Inertia::render('Home/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organizations' => auth()->user()->collaboratingOrganizations()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->with('user:id,name')
                ->paginate()->only('name', 'email', 'slug', 'user')
        ]);
    }
}
