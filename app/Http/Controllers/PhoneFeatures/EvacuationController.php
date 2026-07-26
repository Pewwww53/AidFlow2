<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use App\Models\OccupiedTent;
use App\Models\ScanEvent;
use Illuminate\Http\Request;

class EvacuationController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('phoneFeatures.evacuation');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'tent_code' => 'required|string',
            'barangay_code' => 'nullable|string',
        ]);

        ScanEvent::create([
            'tent_code' => $validated['tent_code'],
            'barangay_code' => $validated['barangay_code'] ?? null,
            'scanned_at' => now(),
        ]);

        OccupiedTent::updateOrCreate(
            ['tent_code' => $validated['tent_code']],
            ['barangay_code' => $validated['barangay_code'] ?? null]
        );

        return response()->json(['success' => true]);
    }
}
