<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{


    public function index()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
            // Validate login form
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            // Attempt login
            if (Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['password'],
                'is_active' => true,
            ])) {

                // Prevent session fixation
                $request->session()->regenerate();

                $user = Auth::user();

                // Redirect based on role
                return match ($user->role) {

                    'admin' => redirect()->route('admin.dashboard'),

                    'principal' => redirect()->route('principal.dashboard'),

                    'teacher' => redirect()->route('teacher.dashboard'),

                    default => tap(redirect()->route('login'), function () {
                        Auth::logout();
                    }),
                };
            }

            // Login failed
            return back()->withErrors(['email' => 'Invalid email or password.',])->withInput($request->only('email'));

    }
    

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate the current session
        $request->session()->invalidate();

        // Generate a new CSRF token
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }



}
