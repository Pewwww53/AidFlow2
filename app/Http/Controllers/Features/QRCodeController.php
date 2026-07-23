<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Models\ScanEvent;
use App\Models\OccupiedTent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QRCodeController extends Controller
{
    public function __construct()
    {

    }

    public function index()
    {
        return view('features.qrcode.index');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'tent_code' => 'required|string',
            'barangay_code' => 'nullable|string',
        ]);

        // Record the scan event
        ScanEvent::create([
            'tent_code' => $validated['tent_code'],
            'barangay_code' => $validated['barangay_code'] ?? null,
            'scanned_at' => now(),
        ]);

        // Mark tent as occupied
        OccupiedTent::updateOrCreate(
            ['tent_code' => $validated['tent_code']],
            ['barangay_code' => $validated['barangay_code'] ?? null]
        );

        return response()->json(['success' => true]);
    }
}
