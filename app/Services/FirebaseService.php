<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FirebaseService
{
    private $databaseUrl;

    public function __construct()
    {
        $this->databaseUrl = rtrim(env('FIREBASE_DATABASE_URL'), '/');
    }

    public function getAccount($username)
    {
        $url = "{$this->databaseUrl}/accounts/{$username}.json";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    private function get($path)
    {
        $response = Http::withoutVerifying()->get(
            "{$this->databaseUrl}/{$path}.json"
        );

        return $response->successful()
            ? $response->json()
            : [];
    }

    private function write($path, $data, $method = 'put')
    {
        $response = Http::withoutVerifying()->{$method}(
            "{$this->databaseUrl}/{$path}.json",
            $data
        );

        return $response->successful() ? $response->json() : null;
    }

    public function getInventory()
    {
        return $this->get('inventory');
    }

    public function createInventory(array $data)
    {
        return $this->write('inventory', $data, 'post');
    }

    public function updateInventory($id, array $data)
    {
        return $this->write("inventory/{$id}", $data, 'put');
    }

    public function deleteInventory($id)
    {
        $response = Http::withoutVerifying()->delete(
            "{$this->databaseUrl}/inventory/{$id}.json"
        );

        return $response->successful();
    }

    public function getReliefPacks()
    {
        return $this->get('relief_packs');
    }

    public function getTents()
    {
        return $this->get('tents');
    }

    public function getScans()
    {
        return $this->get('scanEvents');
    }

    public function getOccupiedTents()
    {
        return $this->get('occupiedTents');
    }
}