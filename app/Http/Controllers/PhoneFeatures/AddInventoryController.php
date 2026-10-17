<?php

namespace App\Http\Controllers\PhoneFeatures;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

/** Supplies the mobile inventory form and creates inventory records from its inputs. */
class AddInventoryController extends Controller
{
    private const EXPIRATION_CATEGORIES = ['Food', 'Medicine', 'Drink', 'Hygiene'];

    public function __construct()
    {
    }

    /** Load distinct existing batch names for the inventory form. */
    public function index(FirebaseService $firebase)
    {
        $inventory = $firebase->getInventory();
        $batches = collect(is_array($inventory) ? $inventory : [])
            ->filter(fn ($item) => is_array($item) && filled($item['batch'] ?? null))
            ->pluck('batch')
            ->map(fn ($batch) => (string) $batch)
            ->unique()
            ->values();

        return view('phoneFeatures.addInventory', [
            'batches' => $batches,
            'expirationCategories' => self::EXPIRATION_CATEGORIES,
        ]);
    }

    /** Normalize supported form field names and save the validated inventory item. */
    public function store(Request $request, FirebaseService $firebase)
    {
        $expirationRequired = in_array($request->input('category'), self::EXPIRATION_CATEGORIES, true);
        $expirationDateRules = $expirationRequired
            ? 'nullable|date|required_without:expirationDate'
            : 'nullable|date';
        $expirationDateAliasRules = $expirationRequired
            ? 'nullable|date|required_without:expiration_date'
            : 'nullable|date';

        $validated = $request->validate([
            'item_name' => 'nullable|string|required_without:name',
            'other_item_name' => 'nullable|string|required_if:item_name,Other',
            'name' => 'nullable|string|required_without:item_name',
            'quantity' => 'nullable|integer|min:0|required_without:stock',
            'stock' => 'nullable|integer|min:0|required_without:quantity',
            'minimumStock' => 'required|integer|min:0',
            'category' => 'required|string',
            'unit' => 'required|string',
            'date_received' => 'nullable|date|required_without:received',
            'received' => 'nullable|date|required_without:date_received',
            'expiration_date' => $expirationDateRules,
            'expirationDate' => $expirationDateAliasRules,
            'batch_option' => 'required|in:existing,new',
            'batch' => 'nullable|string|max:100|required_if:batch_option,existing',
            'new_batch' => 'nullable|string|max:100|required_if:batch_option,new',
        ]);

        $batch = trim($validated['batch_option'] === 'new'
            ? $validated['new_batch']
            : $validated['batch']);

        $payload = [
            'name' => ($validated['item_name'] ?? null) === 'Other'
                ? $validated['other_item_name']
                : ($validated['item_name'] ?? $validated['name'] ?? null),
            'category' => $validated['category'],
            'unit' => $validated['unit'],
            'stock' => (int) ($validated['quantity'] ?? $validated['stock'] ?? 0),
            'minimumStock' => (int) $validated['minimumStock'],
            'received' => $validated['date_received'] ?? $validated['received'] ?? null,
            'added' => now()->toDateString(),
            'removed' => null,
            'distributed' => null,
            'expirationDate' => $validated['expiration_date'] ?? $validated['expirationDate'] ?? null,
            'batch' => $batch,
        ];

        $firebase->createInventory($payload);

        return redirect()->route('phoneFeatures.addInventory')->with('success', 'Item added successfully');
    }
}
