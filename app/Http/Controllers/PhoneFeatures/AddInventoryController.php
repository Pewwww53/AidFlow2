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

    public function index(FirebaseService $firebase)
    {
        $inventory = $firebase->getInventory();
        $batches = collect(is_array($inventory) ? $inventory : [])
            ->filter(fn ($item) => is_array($item) && filled($item['batch'] ?? null))
            ->pluck('batch')
            ->map(fn ($batch) => (string) $batch)
            ->unique()
            ->values();

        return view('phoneFeatures.addInventory', compact('batches'));
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
            'batch_option' => 'required|in:existing,new',
            'batch' => 'nullable|string|max:100|required_if:batch_option,existing',
            'new_batch' => 'nullable|string|max:100|required_if:batch_option,new',
        ]);

        $batch = trim($validated['batch_option'] === 'new'
            ? $validated['new_batch']
            : $validated['batch']);

        $payload = [
            'name' => $validated['item_name'] ?? $validated['name'] ?? null,
            'category' => $validated['category'],
            'unit' => $validated['unit'],
            'stock' => (int) ($validated['quantity'] ?? $validated['stock'] ?? 0),
            'received' => $validated['date_received'] ?? $validated['received'] ?? null,
            'expirationDate' => $validated['expiration_date'] ?? $validated['expirationDate'] ?? null,
            'batch' => $batch,
        ];

        $firebase->createInventory($payload);

        return redirect()->route('phoneFeatures.addInventory')->with('success', 'Item added successfully');
    }
}
