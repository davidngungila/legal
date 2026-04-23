@extends('layouts.auth')

@section('title', 'Forgot Password - LegalHR Tanzania')

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
            <div class="mb-8">
                <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <i data-feather="briefcase" class="w-10 h-10 text-indigo-600"></i>
                </div>
                <h1 class="text-4xl font-bold mb-2">LegalHR</h1>
                <p class="text-xl opacity-90">Tanzania</p>
            </div>
            
            <div class="space-y-4 text-lg">
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="check-circle" class="w-5 h-5"></i>
                    <span>Complete HR Management</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Tanzania Labor Compliant</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="users" class="w-5 h-5"></i>
                    <span>Employee Self Service</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                    <span>Advanced Analytics</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Forgot Password Form -->
    <div class="login-right">
        <div class="login-form">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-gray-600">No worries, we'll send you reset instructions.</p>
            </div>
            
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
            
            <form method="POST" action="{{ route('password.post') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i data-feather="mail" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="email" id="email" name="email" required
                               class="form-input pl-10"
                               placeholder="Enter your email address">
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">
                    <span class="flex items-center justify-center">
                        <i data-feather="send" class="w-5 h-5 mr-2"></i>
                        SEND RESET LINK
                    </span>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <div class="back-to-login">
                    <span class="text-gray-600 text-sm">Remember your password? </span>
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Back to login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
