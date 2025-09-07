<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerAuthController extends Controller
{
    // Show seller login form
    public function showLoginForm()
    {
        return view('seller.auth.login'); // Make sure this matches your actual blade file path
    }

    // Handle seller login
    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // ✅ Try to authenticate using seller guard
    if (Auth::guard('seller')->attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();

        // ✅ Redirect to named route
        return redirect()->route('seller.dashboard');
    }

    // 🔽 Show error if login fails
    return back()->withErrors([
        'email' => 'The provided credentials are incorrect.',
    ])->onlyInput('email');
}
    // ✅ Handle seller logout
    public function logout(Request $request)
    {
        Auth::guard('seller')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.auth.login')->with('success', 'You have been logged out.');
    }
}
