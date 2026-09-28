<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/** Provides the shared desktop features layout entry point. */
class FeaturesController extends Controller
{
    public function __construct()
    {
    }

    /** Render the desktop features shell. */
    public function index()
    {
        return view('features.layout');
    }
}
