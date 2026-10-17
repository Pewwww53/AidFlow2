<?php

use App\Http\Controllers\PhoneFeatures\AddInventoryController;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

it('stores inventory items from the phone form in firebase', function () {
    $firebase = Mockery::mock(FirebaseService::class);
    $firebase->shouldReceive('createInventory')->once()->with([
        'name' => 'Rice',
        'category' => 'Food',
        'unit' => 'Pack',
        'stock' => 50,
        'minimumStock' => 10,
        'received' => '2026-07-26',
        'expirationDate' => '2026-12-31',
        'batch' => 'Batch 1',
    ])->andReturn(['id' => 'abc123']);

    $request = new Request([
        'item_name' => 'Rice',
        'category' => 'Food',
        'unit' => 'Pack',
        'quantity' => 50,
        'minimumStock' => 10,
        'date_received' => '2026-07-26',
        'expiration_date' => '2026-12-31',
        'batch_option' => 'existing',
        'batch' => 'Batch 1',
    ]);

    $controller = new AddInventoryController();
    $response = $controller->store($request, $firebase);

    expect($response->getSession()->get('success'))->toBe('Item added successfully');
});

it('stores inventory items without an expiration date when their category does not require one', function () {
    $firebase = Mockery::mock(FirebaseService::class);
    $firebase->shouldReceive('createInventory')->once()->with([
        'name' => 'Bucket',
        'category' => 'Equipment',
        'unit' => 'Piece',
        'stock' => 10,
        'minimumStock' => 2,
        'received' => '2026-07-26',
        'expirationDate' => null,
        'batch' => 'Batch 1',
    ])->andReturn(['id' => 'abc123']);

    $request = new Request([
        'item_name' => 'Bucket',
        'category' => 'Equipment',
        'unit' => 'Piece',
        'quantity' => 10,
        'minimumStock' => 2,
        'date_received' => '2026-07-26',
        'batch_option' => 'existing',
        'batch' => 'Batch 1',
    ]);

    $controller = new AddInventoryController();
    $response = $controller->store($request, $firebase);

    expect($response->getSession()->get('success'))->toBe('Item added successfully');
});
