<?php

namespace Tests\Feature;

use App\Http\Controllers\PhoneFeatures\ReliefGoodsController;
use App\Services\FirebaseService;
use App\Services\ReliefPackCalculator;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ReliefGoodsScanTest extends TestCase
{
    public function test_scanning_a_relief_pack_consumes_its_inventory_items(): void
    {
        $firebase = Mockery::mock(FirebaseService::class);
        $firebase->shouldReceive('consumeReliefPack')
            ->once()
            ->with(1, ['Rice' => 6])
            ->andReturn(['status' => 'consumed']);

        $calculator = Mockery::mock(ReliefPackCalculator::class);
        $calculator->shouldReceive('requiredItems')->once()->andReturn(['Rice' => 6]);

        $response = (new ReliefGoodsController())->scan(
            Request::create('/phoneFeatures/reliefGoods/scan', 'POST', ['package_id' => '#1']),
            $firebase,
            $calculator
        );

        $this->assertTrue($response->getData()->success);
    }

    public function test_scanning_a_pack_with_insufficient_stock_fails(): void
    {
        $firebase = Mockery::mock(FirebaseService::class);
        $firebase->shouldReceive('consumeReliefPack')
            ->once()
            ->andReturn([
                'status' => 'insufficient_stock',
                'item' => 'Rice',
            ]);

        $calculator = Mockery::mock(ReliefPackCalculator::class);
        $calculator->shouldReceive('requiredItems')->once()->andReturn(['Rice' => 6]);

        $response = (new ReliefGoodsController())->scan(
            Request::create('/phoneFeatures/reliefGoods/scan', 'POST', ['package_id' => '#1']),
            $firebase,
            $calculator
        );

        $this->assertFalse($response->getData()->success);
        $this->assertSame(422, $response->getStatusCode());
    }
}