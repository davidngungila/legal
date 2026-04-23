<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * Display users index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $users = User::whereHas('clients', function ($query) use ($clientId) {
            $query->where('clients.id', $clientId);
        })->orWhere('role', 'super_admin')->get();
        
        return view('users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('users.create', compact('clients'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,hr_admin,hr_staff,employee',
            'phone' => 'nullable|string|max:20',
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Attach to clients
        $user->clients()->attach($request->client_ids);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show user details
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $clients = Client::orderBy('name')->get();
        return view('users.edit', compact('user', 'clients'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,hr_admin,hr_staff,employee',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'phone', 'status']));

        // Update client assignments
        $user->clients()->sync($request->client_ids);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', 'Password reset successfully!');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "User status updated to {$newStatus}"
        ]);
    }
}
