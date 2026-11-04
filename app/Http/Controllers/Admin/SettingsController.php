<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{

    public function index() {
        return view('admin.settings');
    }

    //
    public function update(Request $request)
    {
         // Get the currently logged-in Admin
        $user = $request->user();

        // Validate submitted data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        // Update the Admin's name
        $user->name = $validated['name'];

        // Update email only if changed
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];

            // Require re-verification if email changes
            $user->email_verified_at = null;
        }

          // Save changes to MySQL
        $user->save();

         // Redirect with success message
        return redirect()
        ->route('admin.settings')
        ->with('success', 'Profile updated successfully!');



    }

    
        public function updatePassword(Request $request)
        {
            // Validate submitted passwords
            $validated = $request->validate([
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

            // Get authenticated Admin
            $user = $request->user();

            // Hash the new password
            $user->password = Hash::make($validated['password']);

            // Save new password to MySQL
            $user->save();

            // Redirect with success message
            return redirect()
                ->route('admin.settings')
                ->with(
                    'password_success',
                    'Password updated successfully!'
                );
        }

}
