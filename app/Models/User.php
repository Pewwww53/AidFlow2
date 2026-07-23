<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
class User implements AuthenticatableContract
{
    use Authenticatable, Notifiable;

    protected $firebase;
    protected $fillable = ['fullName', 'username', 'email', 'password', 'role'];
    protected $hidden = ['password'];
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->firebase = app(FirebaseService::class);
        $this->attributes = $attributes;
        $this->firebaseId = $attributes['id'] ?? null;
    }

    /**
     * Get attribute value
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set attribute value
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Check if attribute exists
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get the name of the unique identifier for the user
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['id'] ?? null;
    }

    /**
     * Get the password for the user
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? null;
    }

    /**
     * Get the token value for the "remember me" functionality
     */
    public function getRememberToken()
    {
        return $this->attributes['remember_token'] ?? null;
    }

    /**
     * Set the token value for the "remember me" functionality
     */
    public function setRememberToken($value)
    {
        $this->attributes['remember_token'] = $value;
    }

    /**
     * Get the column name for the "remember me" token
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Find user by username
     */


    public static function findByUsername($username)
    {
        Log::info('Username: ' . $username);

        $firebase = app(FirebaseService::class);
        $userData = $firebase->getUserByUsername($username);

        if ($userData) {
            return new static($userData);
        }

        return null;
    }
    /**
     * Find user by ID
     */
    public static function find($id)
    {
        $firebase = app(FirebaseService::class);
        $userData = $firebase->getUserById($id);

        if ($userData) {
            return new static($userData);
        }

        return null;
    }

    /**
     * Get all users
     */
    public static function all()
    {
        $firebase = app(FirebaseService::class);
        $usersData = $firebase->getAllUsers();

        $users = [];
        foreach ($usersData as $userData) {
            $users[] = new static($userData);
        }

        return $users;
    }

    /**
     * Save user to Firebase
     */
    public function save()
    {
        $firebase = app(FirebaseService::class);

        if (isset($this->attributes['id'])) {
            // Update existing user
            return $firebase->updateUser($this->attributes['id'], $this->attributes);
        } else {
            // Create new user
            $result = $firebase->createUser($this->attributes);
            if ($result && isset($result['id'])) {
                $this->attributes['id'] = $result['id'];
            }
            return $result;
        }
    }

    /**
     * Delete user from Firebase
     */
    public function delete()
    {
        $firebase = app(FirebaseService::class);

        if (isset($this->attributes['id'])) {
            return $firebase->deleteUser($this->attributes['id']);
        }

        return false;
    }

    /**
     * Convert to array
     */
    public function toArray()
    {
        return $this->attributes;
    }
}
