<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use App\Models\OccupiedTent;
use App\Models\ScanEvent;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class EvacuationController extends Controller
{
    public function index()
    {
        return view('phoneFeatures.evacuation');
    }

    public function scan(Request $request, FirebaseService $firebase)
    {
        $validated = $request->validate([
            'tent_code' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9_-]+$/'],
            'barangay_code' => 'required|string|max:10',
            'barangay_name' => 'required|string|max:100',
            'confirm_unoccupy' => 'sometimes|boolean',
        ]);

        $tentCode = strtoupper(trim($validated['tent_code']));
        $barangayCode = strtoupper($validated['barangay_code']);
        $barangayName = $validated['barangay_name'];
        $occupied = $firebase->getOccupiedTent($tentCode);
        $confirmUnoccupy = $validated['confirm_unoccupy'] ?? false;

        if ($confirmUnoccupy && !$occupied) {
            return response()->json([
                'success' => true,
                'action' => 'unoccupied',
                'message' => "Tent {$tentCode} is already unoccupied.",
            ]);
        }

        if ($occupied && !$confirmUnoccupy) {
            return response()->json([
                'success' => false,
                'requires_confirmation' => true,
                'action' => 'unoccupied',
                'message' => "Tent {$tentCode} is already occupied. Confirm to mark it unoccupied.",
            ]);
        }

        $scannedAt = now();
        $willBeOccupied = !$occupied;
        $firebaseData = [
            'tentCode' => $tentCode,
            'barangayCode' => $barangayCode,
            'barangayName' => $barangayName,
            'scannedAt' => $scannedAt->toISOString(),
        ];

        if (!$firebase->recordTentScan($tentCode, $firebaseData, $willBeOccupied)) {
            return response()->json([
                'success' => false,
                'message' => 'Firebase could not record the scan. Please try again.',
            ], 502);
        }

        ScanEvent::create([
            'tent_code' => $tentCode,
            'barangay_code' => $barangayCode,
            'scanned_at' => $scannedAt,
        ]);

        if ($willBeOccupied) {
            OccupiedTent::updateOrCreate(
                ['tent_code' => $tentCode],
                ['barangay_code' => $barangayCode]
            );
        } else {
            OccupiedTent::where('tent_code', $tentCode)->delete();
        }

        return response()->json([
            'success' => true,
            'action' => $willBeOccupied ? 'occupied' : 'unoccupied',
            'message' => "Tent {$tentCode} marked " . ($willBeOccupied ? 'occupied.' : 'unoccupied.'),
        ]);
    }

}