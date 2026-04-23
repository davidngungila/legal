@extends('layouts.auth')

@section('title', 'Login - LegalHR Tanzania')

@section('content')
<div class="login-container">
    <!-- Left Side - Branding -->
    <div class="login-left">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
        
        <div class="text-center z-10">
            <!-- Logo Section -->
            <div class="mb-6 md:mb-8">
                <div class="w-28 h-28 md:w-36 md:h-36 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 md:mb-6 shadow-2xl">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-20 h-20 md:w-40 md:h-40 object-contain">
                </div>
            </div>
            
            <!-- System Info -->
            <div class="mb-6 md:mb-8">
                <h1 class="text-2xl md:text-4xl font-bold mb-2">Orvion</h1>
                <p class="text-lg md:text-xl opacity-90">HR Management System</p>
            </div>
            
            <div class="space-y-3 md:space-y-4 text-base md:text-lg">
                <div class="flex items-center justify-center space-x-2 md:space-x-3">
                    <i data-feather="check-circle" class="w-4 h-4 md:w-5 md:h-5"></i>
                    <span class="text-sm md:text-base">Complete HR Management</span>
                </div>
                <div class="flex items-center justify-center space-x-2 md:space-x-3">
                    <i data-feather="shield" class="w-4 h-4 md:w-5 md:h-5"></i>
                    <span class="text-sm md:text-base">Labor Compliant</span>
                </div>
                <div class="flex items-center justify-center space-x-2 md:space-x-3">
                    <i data-feather="users" class="w-4 h-4 md:w-5 md:h-5"></i>
                    <span class="text-sm md:text-base">Employee Self Service</span>
                </div>
                <div class="flex items-center justify-center space-x-2 md:space-x-3">
                    <i data-feather="bar-chart-2" class="w-4 h-4 md:w-5 md:h-5"></i>
                    <span class="text-sm md:text-base">Advanced Analytics</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Login Form -->
    <div class="login-right">
        <div class="login-form">
            <!-- Mobile Logo Section (shown only on small phones) -->
            <div class="mobile-logo">
                <div class="mobile-logo-container">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo">
                </div>
                <h1>Orvion</h1>
            </div>
            
            <div class="text-center mb-6 md:mb-8">
                <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
                <p class="text-sm md:text-base text-gray-600">Sign in to your LegalHR account</p>
            </div>
            
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i data-feather="mail" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="email" id="email" name="email" required
                               class="form-input pl-10"
                               placeholder="Enter your email">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i data-feather="lock" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="password" id="password" name="password" required
                               class="form-input pl-10"
                               placeholder="Enter your password">
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-primary">
                    <span class="flex items-center justify-center">
                        <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                        LOGIN
                    </span>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <div class="forgot-password">
                    <a href="#" class="text-[#040344] hover:text-[#040344]/80 text-sm">Forgot your password?</a>
                </div>
                
                <div class="signup-link mt-4">
                    <span class="text-gray-600 text-sm">Don't have an account? </span>
                    <a href="{{ route('register') }}" class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium">Sign up</a>
                    <span class="mx-2 text-gray-400">|</span>
                    <a href="{{ route('sample-users') }}" class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium">Sample Users</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Auto-fill credentials if coming from sample users page
document.addEventListener('DOMContentLoaded', function() {
    const email = sessionStorage.getItem('loginEmail');
    const password = sessionStorage.getItem('loginPassword');
    
    if (email && password) {
        const emailField = document.getElementById('email');
        const passwordField = document.getElementById('password');
        
        if (emailField && passwordField) {
            emailField.value = email;
            passwordField.value = password;
            
            // Clear sessionStorage
            sessionStorage.removeItem('loginEmail');
            sessionStorage.removeItem('loginPassword');
        }
    }
});
</script>
