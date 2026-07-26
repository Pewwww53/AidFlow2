<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;

class ReliefGoodsController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('phoneFeatures.reliefGoods');
    }
}
