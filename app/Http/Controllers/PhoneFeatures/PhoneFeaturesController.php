<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PhoneFeaturesController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('phoneFeatures.index');
    }
}
