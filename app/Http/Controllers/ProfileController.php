<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController
{
    /**
     * Display profile page
     */
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($request->only(['name', 'email', 'phone']));

        return redirect()->back()
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->back()
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $user->id . '.' . $photo->getClientOriginalExtension();
            
            // Store photo
            $photo->storeAs('public/avatars', $photoName);
            
            $user->update(['avatar' => $photoName]);
        }

        return redirect()->back()
            ->with('success', 'Profile photo updated successfully!');
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        $user = auth()->user();
        $user->update(['avatar' => null]);

        return redirect()->back()
            ->with('success', 'Profile photo deleted successfully!');
    }

    /**
     * Get activity log
     */
    public function activityLog()
    {
        $user = auth()->user();
        $activities = $user->activities()->orderBy('created_at', 'desc')->paginate(20);
        
        return view('profile.activity', compact('activities'));
    }

    /**
     * Export profile data
     */
    public function export()
    {
        $user = auth()->user();
        
        $data = [
            'user' => $user->toArray(),
            'clients' => $user->clients,
            'settings' => $user->settings,
            'export_date' => now()->toDateTimeString(),
        ];

        return response()->json($data);
    }
}
