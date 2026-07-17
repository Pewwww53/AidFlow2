<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    public function index(FirebaseService $firebase)
    {
        $inventory = collect($firebase->getInventory());

        $tents = collect($firebase->getTents());

        $reliefPacks = collect($firebase->getReliefPacks());

        $recentScannedTents = collect($firebase->getScans());

        $occupiedTents = collect($firebase->getOccupiedTents());

        $occupancyData = $occupiedTents
            ->map(function ($item) {
                return $item['barangayCode'] ?? $item['barangay_code'] ?? null;
            })
            ->filter()
            ->countBy()
            ->all();

        return view('features.dashboard', [

            'inventory' => $inventory,

            'totalItems' => $inventory->count(),

            'totalStock' => $inventory->sum('stock'),

            'standardReliefPacks' => $inventory->where('type', 'standard')->count(),

            'occupiedTentsCount' => $occupiedTents->count(),

            'occupiedTents' => $occupiedTents,

            'occupancyData' => $occupancyData,

            'barangayCount' => count($occupancyData),

            'recentScannedTents' => $recentScannedTents,
        ]);
    }
}
