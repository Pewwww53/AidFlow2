<?php

use App\Http\Controllers\Features\InventoryController;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

it('combines inventory list rows with the same name and sums their stock', function () {
    $firebase = Mockery::mock(FirebaseService::class);
    $firebase->shouldReceive('getInventory')->andReturn([
        'first' => [
            'name' => 'Toothbrush',
            'category' => 'Food',
            'unit' => 'Piece',
            'stock' => 1,
            'minimumStock' => 10,
            'expirationDate' => now()->subDay()->toDateString(),
            'batch' => 'Batch A',
            'added' => now()->subDays(2)->toDateString(),
        ],
        'second' => [
            'name' => 'Toothbrush',
            'category' => 'Food',
            'unit' => 'Piece',
            'stock' => 2,
            'minimumStock' => 10,
            'expirationDate' => now()->addMonths(2)->toDateString(),
            'added' => now()->subDays(2)->toDateString(),
        ],
        'third' => [
            'name' => 'Soap',
            'category' => 'Hygiene',
            'unit' => 'Piece',
            'stock' => 4,
            'minimumStock' => 3,
            'added' => now()->subDays(2)->toDateString(),
        ],
    ]);

    $response = (new InventoryController())->index(Request::create('/features/inventory'), $firebase);
    $html = $response->render();

    expect($response->getData()['inventoryListItems'])
        ->toHaveCount(2)
        ->and($response->getData()['inventoryListItems']->firstWhere('name', 'Toothbrush')['stock'])
        ->toBe(2)
        ->and($response->getData()['inventoryListItems']->firstWhere('name', 'Toothbrush')['records'])
        ->toHaveCount(1)
        ->and($response->getData()['lowStockAlerts']->firstWhere('name', 'Toothbrush')['minimumStock'])
        ->toBe(10)
        ->and($response->getData()['nearExpiryAlerts'])
        ->toHaveCount(1)
        ->and($response->getData()['standardReliefPackSupply'])
        ->toBe(0)
        ->and($response->getData()['minimumStandardReliefPacks'])
        ->toBe(1)
        ->and($html)
        ->toContain('Expiration Date', 'Expired', 'Batch A', 'Inventory Alerts', 'Near expiry (within 3 months)', 'Only 0 standard relief packs');
});

it('updates an item minimum-stock alert level without dropping its existing batch data', function () {
    $firebase = Mockery::mock(FirebaseService::class);
    $firebase->shouldReceive('getInventory')->once()->andReturn([
        'item-1' => [
            'name' => 'Toothbrush',
            'category' => 'Hygiene',
            'unit' => 'Piece',
            'stock' => 8,
            'minimumStock' => 10,
            'batch' => 'Batch A',
            'added' => now()->subDay()->toDateString(),
        ],
    ]);
    $firebase->shouldReceive('updateInventory')
        ->once()
        ->with('item-1', Mockery::on(fn ($item) => $item['minimumStock'] === 5 && $item['batch'] === 'Batch A'))
        ->andReturn(null);

    $request = Request::create('/features/inventory/item-1', 'PUT', [
        'name' => 'Toothbrush',
        'category' => 'Hygiene',
        'unit' => 'Piece',
        'stock' => 8,
        'minimumStock' => 5,
        'received' => '',
        'expirationDate' => '',
    ]);

    $response = (new InventoryController())->update($request, 'item-1', $firebase);

    expect($response->getSession()->get('success'))->toBe('Item updated successfully');
});
