<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
     // DISPLAY TEACHER SETTINGS
    public function index()
    {
        return view('teacher.settings');
    }

    // UPDATE TEACHER PROFILE
    public function update(Request $request)
    {
        // Get logged-in teacher
        $user = $request->user();

        // Validate profile data
        $validated = $request->validateWithBag('profile', [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        // Update name
        $user->name = $validated['name'];

        // Update email if changed
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }

        // Save to MySQL
        $user->save();

        return redirect()
            ->route('teacher.settings')
            ->with(
                'profile_success',
                'Profile updated successfully!'
            );
    }


    // UPDATE TEACHER PASSWORD
    public function updatePassword(Request $request)
    {
        // Validate passwords
        $validated = $request->validateWithBag('password', [
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'different:current_password',
                'confirmed',
                Password::min(12)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // Get logged-in teacher
        $user = $request->user();

        // Hash and update password
        $user->password = Hash::make($validated['password']);

        // Save to MySQL
        $user->save();

        return redirect()
            ->route('teacher.settings')
            ->with(
                'password_success',
                'Password updated successfully!'
            );
    }
    
}
