<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserRegistrationConfirmation;

class UserRegistrationController extends Controller
{
    /**
     * Display user registration form.
     */
    public function create()
    {
        return view('hris.user-registration.create');
    }

    /**
     * Store a newly registered user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user_registrations',
            'phone_number' => 'required|string|max:20|unique:user_registrations',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'department_name' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'project_location' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.unique' => 'User created already exist - This email is already registered.',
            'phone_number.unique' => 'User created already exist - This phone number is already registered.',
            'date_of_birth.before' => 'Date of birth must be before today.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserRegistration::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'surname' => $request->surname,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'department_name' => $request->department_name,
                'section_name' => $request->section_name,
                'designation' => $request->designation,
                'project_location' => $request->project_location,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Send confirmation email
            try {
                Mail::to($user->email)->send(new UserRegistrationConfirmation($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send registration email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('User registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the list of registered users.
     */
    public function index()
    {
        $users = UserRegistration::active()->paginate(10);
        return view('hris.user-registration.index', compact('users'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(UserRegistration $user)
    {
        return view('hris.user-registration.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, UserRegistration $user)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user_registrations,email,' . $user->id,
            'phone_number' => 'required|string|max:20|unique:user_registrations,phone_number,' . $user->id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'department_name' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'project_location' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            \Log::error('User update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate the specified user.
     */
    public function deactivate(UserRegistration $user)
    {
        try {
            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('User deactivation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate the specified user.
     */
    public function activate(UserRegistration $user)
    {
        try {
            $user->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('User activation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }
}
