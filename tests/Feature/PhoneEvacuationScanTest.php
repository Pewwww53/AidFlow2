<?php

namespace Tests\Feature;

use App\Http\Controllers\PhoneFeatures\EvacuationController;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PhoneEvacuationScanTest extends TestCase
{
    public function test_scan_records_tent_in_firebase(): void
    {
        $controller = new EvacuationController();
        $request = Request::create('/phoneFeatures/evacuation/scan', 'POST', [
            'tent_code' => 'BB-001',
            'barangay_code' => 'BB',
            'barangay_name' => 'Balong-Bato',
        ]);
        $request->setLaravelSession(app('session')->driver());
        $request->session()->put('user', ['fullName' => 'Test Account']);
        $firebase = Mockery::mock(FirebaseService::class);
        $firebase->shouldReceive('getOccupiedTent')->once()->with('BB-001')->andReturn(null);
        $firebase->shouldReceive('recordTentScan')->once()->with(
            'BB-001',
            Mockery::on(fn ($data) =>
                $data['tentCode'] === 'BB-001'
                && $data['barangayCode'] === 'BB'
                && $data['barangayName'] === 'Balong-Bato'
                && $data['accountName'] === 'Test Account'
                && isset($data['scannedAt'])
            ),
            true
        )->andReturn(true);

        $response = $controller->scan($request, $firebase);

        $this->assertTrue($response->getData()->success);
        $this->assertSame('occupied', $response->getData()->action);
    }

    public function test_second_scan_requires_confirmation_then_marks_tent_unoccupied(): void
    {
        $controller = new EvacuationController();
        $firebase = Mockery::mock(FirebaseService::class);
        $firebase->shouldReceive('getOccupiedTent')->twice()->with('WC-002')->andReturn([
            'tentCode' => 'WC-002',
        ]);

        $confirmationResponse = $controller->scan(Request::create(
            '/phoneFeatures/evacuation/scan',
            'POST',
            [
                'tent_code' => 'WC-002',
                'barangay_code' => 'WC',
                'barangay_name' => 'West Crame',
            ]
        ), $firebase);
        $this->assertTrue($confirmationResponse->getData()->requires_confirmation);

        $firebase->shouldReceive('recordTentScan')->once()->with(
            'WC-002',
            Mockery::on(fn ($data) => $data['barangayName'] === 'West Crame'),
            false
        )->andReturn(true);

        $response = $controller->scan(Request::create(
            '/phoneFeatures/evacuation/scan',
            'POST',
            [
                'tent_code' => 'WC-002',
                'barangay_code' => 'WC',
                'barangay_name' => 'West Crame',
                'confirm_unoccupy' => true,
            ]
        ), $firebase);
        $this->assertTrue($response->getData()->success);
        $this->assertSame('unoccupied', $response->getData()->action);
    }
}