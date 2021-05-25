<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @author Bipin Parmar
     *
     * @return View
     */
    public function index()
    {
        return view('admin.home');
    }
}
