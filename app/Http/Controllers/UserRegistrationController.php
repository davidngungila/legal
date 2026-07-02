<?php

namespace App\Http\Controllers;

use App\Models\UserRegistration;
use App\Models\User;
use App\Models\Client;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\UserRegistrationConfirmation;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\DB;

class UserRegistrationController extends Controller
{
    /**
     * Display user registration form.
     */
    public function create()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('super_admin');
        
        $clients = $isSuperAdmin ? Client::all() : Client::where('id', $currentUser->current_client_id)->get();
        $roles = Role::whereIn('name', ['employee', 'line_manager', 'hr_officer', 'lead_hr_admin', 'finance_payroll_officer'])->get();
        
        return view('hris.user-registration.create', compact('clients', 'roles', 'isSuperAdmin'));
    }

    /**
     * Store a newly registered user.
     */
    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->hasRole('super_admin');
        
        $minDate = now()->subYears(120)->format('Y-m-d');
        
        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user_registrations|unique:users',
            'phone_number' => 'required|string|max:20|unique:user_registrations',
            'date_of_birth' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:' . $minDate],
            'gender' => 'required|in:male,female,other',
            'department_name' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'project_location' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
        ];

        $customMessages = [
            'email.unique' => 'User created already exist - This email is already registered.',
            'phone_number.unique' => 'User created already exist - This phone number is already registered.',
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future',
            'date_of_birth.after_or_equal' => 'Date of birth cannot be more than 120 years ago',
        ];
        
        if ($isSuperAdmin) {
            $rules['client_id'] = 'required|exists:clients,id';
        }

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            $clientId = $isSuperAdmin ? $request->client_id : $currentUser->current_client_id;
            
            $userRegistration = UserRegistration::create([
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
                'client_id' => $clientId,
                'role_id' => $request->role_id,
            ]);
            
            // Now create User record too!
            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->surname,
                'email' => $request->email,
                'phone' => $request->phone_number,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
                'current_client_id' => $clientId,
            ]);
            
            // Assign role to User
            $role = Role::find($request->role_id);
            if ($role) {
                $user->roles()->attach($role->id);
            }
            
            // Attach user to client
            $user->clients()->syncWithoutDetaching([
                $clientId => [
                    'role' => $role->name === 'line_manager' ? 'manager' : ($role->name === 'employee' ? 'employee' : 'admin'),
                    'is_active' => true,
                    'joined_at' => now(),
                ]
            ]);
            
            // Send confirmation email
            try {
                Mail::to($userRegistration->email)->send(new UserRegistrationConfirmation($userRegistration));
            } catch (\Exception $e) {
                \Log::error('Failed to send registration email: ' . $e->getMessage());
            }
            
            AuditLogger::log(
                'created',
                $user,
                'Users',
                "Created user: {$user->first_name} {$user->last_name}",
                null,
                $user->toArray()
            );
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'user' => $userRegistration
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
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
        $roles = Role::all();
        $clients = Client::all();
        $isSuperAdmin = auth()->user()->hasRole('super_admin');
        
        return view('hris.user-registration.edit', compact('user', 'roles', 'clients', 'isSuperAdmin'));
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
            'password' => 'nullable|string|min:8',
            'password_confirmation' => 'nullable|required_with:password|same:password',
            'role_id' => 'required|exists:roles,id',
            'client_id' => 'nullable|exists:clients,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Prepare update data
            $updateData = $request->only([
                'first_name',
                'middle_name',
                'surname',
                'email',
                'phone_number',
                'date_of_birth',
                'gender',
                'department_name',
                'section_name',
                'designation',
                'project_location',
                'role_id',
                'is_active'
            ]);

            // Handle client_id only if provided and not hidden
            if ($request->filled('client_id')) {
                $updateData['client_id'] = $request->client_id;
            }

            // Handle password update only if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            // Convert is_active to boolean
            $updateData['is_active'] = $request->has('is_active');

            $user->update($updateData);

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
