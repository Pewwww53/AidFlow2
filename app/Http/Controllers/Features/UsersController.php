<?php

namespace App\Http\Controllers\Features;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $users = User::all();

        if ($request->search) {
            $users = array_filter($users, function ($user) use ($request) {
                $search = strtolower($request->search);
                return strpos(strtolower($user->fullName ?? ''), $search) !== false ||
                    strpos(strtolower($user->username ?? ''), $search) !== false ||
                    strpos(strtolower($user->email ?? ''), $search) !== false;
            });
        }

        if ($request->role) {
            $users = array_filter($users, function ($user) use ($request) {
                return ($user->role ?? null) === $request->role;
            });
        }

        // Convert to collection for pagination
        $page = $request->get('page', 1);
        $perPage = 11;
        $users = array_values($users); // Re-index array
        $users = new \Illuminate\Pagination\Paginator(
            array_slice($users, ($page - 1) * $perPage, $perPage),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('features.users.index', ['users' => $users]);
    }

    function generateUsername($fullName)
    {
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $fullName));
        $username = $baseUsername;
        $counter = 1;

        while (User::findByUsername($username)) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'birthDate' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email|max:255',
            'role' => 'required|in:admin,user',
        ]);

        $firebase = app(FirebaseService::class);

        $validated['username'] = $this->generateUsername($validated['fullName']);
        // Create user in Firebase
        $userData = [
            'address' => $validated['address'],
            'birthDate' => $validated['birthDate'],
            'email' => $validated['email'],
            'fullName' => $validated['fullName'],
            'gender' => $validated['gender'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ];

        $result = $firebase->createUser($validated['username'], $userData);

        if ($result) {
            return redirect()->back()->with('success', 'User created successfully');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to create user']);
    }

    public function update(Request $request, User $user)
    {
        $username = $user->username ?? $user->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|in:admin,user',
        ]);

        // Check if new username is already taken by another user
        if ($validated['username'] !== $username) {
            $existingUser = User::findByUsername($validated['username']);
            if ($existingUser) {
                return redirect()->back()->withErrors(['error' => 'Username already exists']);
            }
        }

        $firebase = app(FirebaseService::class);

        // Prepare update data
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        // If username changed, delete old and create new
        if ($validated['username'] !== $username) {
            $firebase->deleteUser($username);
            $result = $firebase->createUser($validated['username'], $updateData);
        } else {
            $result = $firebase->updateUser($username, $updateData);
        }

        if ($result) {
            return redirect()->back()->with('success', 'User updated successfully');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to update user']);
    }

    public function destroy(User $user)
    {
        $username = $user->username ?? $user->id;
        $currentUsername = Auth::user()->username ?? Auth::user()->id;

        if ($username === $currentUsername) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete yourself']);
        }

        $firebase = app(FirebaseService::class);
        $result = $firebase->deleteUser($username);

        if ($result) {
            return redirect()->back()->with('success', 'User deleted successfully');
        }

        return redirect()->back()->withErrors(['error' => 'Failed to delete user']);
    }
}
