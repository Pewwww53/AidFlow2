<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AddInventoryController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        return view('phoneFeatures.addInventory');
    }

    public function store(Request $request, FirebaseService $firebase)
    {
        $validated = $request->validate([
            'item_name' => 'nullable|string|required_without:name',
            'name' => 'nullable|string|required_without:item_name',
            'quantity' => 'nullable|integer|min:0|required_without:stock',
            'stock' => 'nullable|integer|min:0|required_without:quantity',
            'category' => 'required|string',
            'unit' => 'required|string',
            'date_received' => 'nullable|date|required_without:received',
            'received' => 'nullable|date|required_without:date_received',
            'expiration_date' => 'nullable|date|required_without:expirationDate',
            'expirationDate' => 'nullable|date|required_without:expiration_date',
        ]);

        $payload = [
            'name' => $validated['item_name'] ?? $validated['name'] ?? null,
            'category' => $validated['category'],
            'unit' => $validated['unit'],
            'stock' => (int) ($validated['quantity'] ?? $validated['stock'] ?? 0),
            'received' => $validated['date_received'] ?? $validated['received'] ?? null,
            'expirationDate' => $validated['expiration_date'] ?? $validated['expirationDate'] ?? null,
        ];

        $firebase->createInventory($payload);

        return redirect()->route('phoneFeatures.addInventory')->with('success', 'Item added successfully');
    }
}
