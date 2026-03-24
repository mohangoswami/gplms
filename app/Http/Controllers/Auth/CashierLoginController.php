<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Cashier;

class CashierLoginController extends Controller
{
    /**
     * Show the login form for cashiers.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.cashier-login');
    }

    /**
     * Handle a login request for a cashier.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if (Auth::guard('cashier')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            return redirect()->route('fee.allStudentsRecord')->with('status', 'Welcome, ' ,  Auth::guard('cashier')->user()->name );
        }

        return redirect()->back()->withInput()->withErrors(['email' => 'Invalid email or password.']);
    }

    /**
     * Handle a logout request for a cashier.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('cashier')->logout();

        return redirect()->route('cashier.login')->with('status', 'Logged out successfully.');
    }
}
