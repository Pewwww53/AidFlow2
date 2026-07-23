<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Custom validation without database unique rules
        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        // Check if username already exists in Firebase
        if ($this->firebaseService->usernameExists($validated['username'])) {
            return back()
                ->withErrors(['username' => 'The username has already been taken.'])
                ->onlyInput('username', 'email', 'fullName');
        }

        // Check if email already exists in Firebase
        if ($this->firebaseService->emailExists($validated['email'])) {
            return back()
                ->withErrors(['email' => 'The email has already been registered.'])
                ->onlyInput('username', 'email', 'fullName');
        }

        try {
            // Create user object with hashed password
            $user = new User([
                'fullName' => $validated['fullName'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Save to Firebase
            $user->save();

            return redirect('/')->with('success', 'Account created successfully. Please log in.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['general' => 'An error occurred while creating your account.'])
                ->withInput();
        }
    }
}
