<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    //
     // DISPLAY ALL TEACHER AND PRINCIPAL ACCOUNTS
        public function index(Request $request)
        {
            $users = User::query()
                ->whereIn('role', ['teacher', 'principal'])

                // SEARCH NAME OR EMAIL
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = trim($request->input('search'));

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                    });
                })

                // FILTER BY ROLE
                ->when(
                    in_array($request->input('role'), ['teacher', 'principal'], true),
                    fn ($query) => $query->where('role', $request->input('role'))
                )

                // FILTER BY STATUS
                ->when(
                    in_array($request->input('status'), ['active', 'inactive'], true),
                    fn ($query) => $query->where(
                        'is_active',
                        $request->input('status') === 'active'
                    )
                )

                ->latest()
                ->paginate(10)
                ->withQueryString();

            $stats = [
                'teachers' => User::where('role', 'teacher')->count(),
                'principals' => User::where('role', 'principal')->count(),
                'active' => User::whereIn('role', ['teacher', 'principal'])
                    ->where('is_active', true)->count(),
                'inactive' => User::whereIn('role', ['teacher', 'principal'])
                    ->where('is_active', false)->count(),
            ];

            $principalExists = User::where('role', 'principal')->exists();

            return view('admin.users.index', compact(
                'users',
                'stats',
                'principalExists'
            ));
        }




         // CREATE ACCOUNT
        public function store(Request $request)
        {
            $validated = $request->validateWithBag('createUser', [
                'name' => ['required', 'string', 'max:255'],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],

                'role' => [
                    'required',
                    Rule::in(['teacher', 'principal']),
                ],

                'grade_access' => [
                    'required_if:role,teacher',
                    'nullable',
                    Rule::in(['11', '12', 'both']),
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::min(12)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ]);

            // ONLY ONE PRINCIPAL ACCOUNT IS ALLOWED
            if (
                $validated['role'] === 'principal' &&
                User::where('role', 'principal')->exists()
            ) {
                return back()
                    ->withErrors([
                        'role' => 'A Principal account already exists.',
                    ], 'createUser')
                    ->withInput($request->except(
                        'password',
                        'password_confirmation'
                    ));
            }

             $user = new User();

                $user->name = $validated['name'];
                $user->email = $validated['email'];
                $user->role = $validated['role'];

                $user->grade_access = $validated['role'] === 'teacher'
                    ? $validated['grade_access']
                    : null;

                $user->password = Hash::make($validated['password']);
                $user->is_active = true;
                $user->email_verified_at = null;

                $user->save();

                return redirect()
                    ->route('admin.users.index')
                    ->with('success', 'Account created successfully!');
        }



        
    // UPDATE ACCOUNT
        public function update(Request $request, User $user)
        {
            // DO NOT ALLOW EDITING ADMIN ACCOUNTS HERE
            abort_unless(
                in_array($user->role, ['teacher', 'principal'], true),
                404
            );

            $validated = $request->validateWithBag('updateUser', [
                'name' => ['required', 'string', 'max:255'],

                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($user->id),
                ],

                'grade_access' => [
                    Rule::requiredIf($user->role === 'teacher'),
                    'nullable',
                    Rule::in(['11', '12', 'both']),
                ],

                // OPTIONAL ADMIN PASSWORD RESET
                'password' => [
                    'nullable',
                    'confirmed',
                    Password::min(12)
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ]);

            $user->name = $validated['name'];

            if ($user->email !== $validated['email']) {
                $user->email = $validated['email'];
                $user->email_verified_at = null;
            }

            $user->grade_access = $user->role === 'teacher'
                ? $validated['grade_access']
                : null;

            // ONLY CHANGE PASSWORD IF ADMIN ENTERED ONE
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'Account updated successfully!');
        }



        // ACTIVATE OR DEACTIVATE ACCOUNT
        public function toggleStatus(User $user)
        {
            abort_unless(
                in_array($user->role, ['teacher', 'principal'], true),
                404
            );

            $user->is_active = !$user->is_active;
            $user->save();

            return back()->with(
                'success',
                $user->is_active
                    ? 'Account activated successfully!'
                    : 'Account deactivated successfully!'
            );
        }

}
