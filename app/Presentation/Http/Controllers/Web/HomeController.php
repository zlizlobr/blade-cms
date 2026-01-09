<?php

namespace App\Presentation\Http\Controllers\Web;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        return view('home');
    }
}
