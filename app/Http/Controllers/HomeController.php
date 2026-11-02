<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $view = auth()->user()->role === 'admin' ? 'dashboard.admin' : 'dashboard.member';

        return view($view);
    }
}