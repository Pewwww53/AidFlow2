<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;

class EvacuationController extends Controller
{
    public function __construct()
    {
    }

    public function index(FirebaseService $firebase)
    {
        $recentScannedTents = collect($firebase->getScans());
        $occupiedTents = collect($firebase->getOccupiedTents());

        $occupancyData = $occupiedTents
            ->map(function ($item) {
                return $item['barangayCode'] ?? $item['barangay_code'] ?? null;
            })
            ->filter()
            ->countBy()
            ->all();

        return view('features.evacuation.index', [
            'recentScannedTents' => $recentScannedTents,
            'occupiedTentsCount' => $occupiedTents->count(),
            'occupiedTents' => $occupiedTents,
            'barangayCount' => count($occupancyData),
            'occupancyData' => $occupancyData,
        ]);
    }
}
