<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Web;

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
