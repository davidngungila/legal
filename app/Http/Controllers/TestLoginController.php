<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestLoginController extends Controller
{
    public function testLogin()
    {
        // Test authentication with the admin user
        $credentials = [
            'email' => 'admin@legalhr.com',
            'password' => 'password',
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login failed'
            ]);
        }
    }
}
