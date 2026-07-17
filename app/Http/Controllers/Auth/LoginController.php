<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request, FirebaseService $firebase)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $account = $firebase->getAccount($request->username);

        if (!$account) {
            return back()->withErrors([
                'username' => 'Account not found.',
            ]);
        }

        if (($account['password'] ?? '') !== $request->password) {
            return back()->withErrors([
                'password' => 'Incorrect password.',
            ]);
        }


        session([
            'user' => $account,
            'username' => $request->username,
        ]);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['user', 'username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}