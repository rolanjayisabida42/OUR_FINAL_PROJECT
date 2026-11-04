<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class SettingsController extends Controller
{
    //
     // DISPLAY SETTINGS PAGE
    public function index()
    {
        return view('principal.settings');
    }

     // UPDATE PRINCIPAL PROFILE
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->name = $validated['name'];

        if ($user->email !== $validated['email']) {

            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('principal.settings')
            ->with(
                'profile_success',
                'Profile updated successfully!'
            );
    }


    // UPDATE PRINCIPAL PASSWORD
    public function updatePassword(Request $request)
    {
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

        $user = $request->user();

        $user->password = Hash::make($validated['password']);

        $user->save();

        return redirect()
            ->route('principal.settings')
            ->with(
                'password_success',
                'Password updated successfully!'
            );
    }

}
