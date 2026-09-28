<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

/** Provides the mobile-oriented feature landing page. */
class PhoneFeaturesController extends Controller
{
    public function __construct()
    {
    }

    /** Render the mobile feature menu. */
    public function index()
    {
        return view('phoneFeatures.index');
    }
}
