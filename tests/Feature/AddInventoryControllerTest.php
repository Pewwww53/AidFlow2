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
        'received' => '2026-07-26',
        'expirationDate' => '2026-12-31',
    ])->andReturn(['id' => 'abc123']);

    $request = new Request([
        'item_name' => 'Rice',
        'category' => 'Food',
        'unit' => 'Pack',
        'quantity' => 50,
        'date_received' => '2026-07-26',
        'expiration_date' => '2026-12-31',
    ]);

    $controller = new AddInventoryController();
    $response = $controller->store($request, $firebase);

    expect($response->getSession()->get('success'))->toBe('Item added successfully');
});
