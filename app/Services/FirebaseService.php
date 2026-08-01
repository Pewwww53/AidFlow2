<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    public function getOccupiedTent(string $tentCode): ?array
    {
        $response = Http::get(
            "{$this->databaseUrl}/occupiedTents/".rawurlencode($tentCode).'.json'
        );
        $tent = $response->successful() ? $response->json() : null;

        return is_array($tent) ? $tent : null;
    }

    public function recordTentScan(string $tentCode, array $data, bool $occupied): bool
    {
        $event = $data;
        $event['action'] = $occupied ? 'occupied' : 'unoccupied';
        $eventKey = (string) Str::uuid();

        $response = Http::patch("{$this->databaseUrl}/.json", [
            "scanEvents/{$eventKey}" => $event,
            'occupiedTents/'.$tentCode => $occupied ? $data : null,
        ]);

        return $response->successful();
    }
    /**
     * Get all users from Firebase
     */
    public function getAllUsers()
    {
        $users = $this->get('accounts');

        if (!is_array($users)) {
            return [];
        }

        // Convert associative array to indexed array of user objects
        $result = [];
        foreach ($users as $username => $userData) {
            if (is_array($userData)) {
                $userData['username'] = $username;
                if (!isset($userData['id'])) {
                    $userData['id'] = $username;
                }
                $result[] = $userData;
            }
        }
        return $result;
    }

    /**
     * Get user by username
     */
    public function getUserByUsername($username)
    {
        $userData = $this->getAccount($username);

        if ($userData && is_array($userData)) {
            $userData['username'] = $username;
            if (!isset($userData['id'])) {
                $userData['id'] = $username;
            }
        }

        return $userData;
    }

    /**
     * Get user by ID (ID is typically the username in Firebase)
     */
    public function getUserById($id)
    {
        return $this->getUserByUsername($id);
    }

    /**
     * Create a new user in Firebase
     */
    public function createUser($username, array $data)
    {
        return $this->write("accounts/{$username}", $data, 'put');
    }

    /**
     * Update user in Firebase
     */
    public function updateUser($username, array $data)
    {
        return $this->write("accounts/{$username}", $data, 'put');
    }

    /**
     * Delete user from Firebase
     */
    public function deleteUser($username)
    {
        $response = Http::withoutVerifying()->delete(
            "{$this->databaseUrl}/accounts/{$username}.json"
        );

        return $response->successful();
    }
}