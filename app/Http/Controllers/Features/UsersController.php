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

    public function update(Request $request, $username)
    {
        $firebase = app(FirebaseService::class);

        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'birthDate' => 'required',
            'gender' => 'required|in:Male,Female',
            'email' => 'required|email',
            'role' => 'required|in:admin,user',
            'password' => 'nullable|min:8',
        ]);

        $data = [
            'fullName' => $validated['fullName'],
            'address' => $validated['address'],
            'birthDate' => $validated['birthDate'],
            'gender' => $validated['gender'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $result = $firebase->updateUser($username, $data);

        if ($result) {
            return back()->with('success', 'User updated successfully.');
        }

        return back()->withErrors([
            'error' => 'Failed to update user.'
        ]);
    }

    public function destroy($username)
    {
        $currentUsername = session('user.username'); // or however you store the logged-in user
        if ($username === $currentUsername) {
            return back()->withErrors([
                'error' => 'You cannot delete yourself.'
            ]);
        }

        $firebase = app(FirebaseService::class);

        if ($firebase->deleteUser($username)) {
            return back()->with('success', 'User deleted successfully.');
        }

        return back()->withErrors([
            'error' => 'Failed to delete user.'
        ]);
    }
}
