<?php

namespace Tests\Feature;

use App\Http\Controllers\PhoneFeatures\EvacuationController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PhoneEvacuationScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_records_tent_and_occupied_tent(): void
    {
        $controller = new EvacuationController();
        $request = Request::create('/phoneFeatures/evacuation/scan', 'POST', [
            'tent_code' => 'T-001',
            'barangay_code' => 'B-1',
        ]);

        $response = $controller->scan($request);

        $this->assertTrue($response->getData()->success);
        $this->assertDatabaseHas('scan_events', [
            'tent_code' => 'T-001',
            'barangay_code' => 'B-1',
        ]);
        $this->assertDatabaseHas('occupied_tents', [
            'tent_code' => 'T-001',
            'barangay_code' => 'B-1',
        ]);
    }
}
