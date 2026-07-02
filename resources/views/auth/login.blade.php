@extends('layouts.auth')

@section('title', 'Login - LegalHR Tanzania')

@section('content')
<div class="login-container">
    <!-- Left Side - Branding -->
    <div class="login-left">
        <div class="floating-shape w-64 h-64 bg-indigo-500 top-[-10%] left-[-10%]"></div>
        <div class="floating-shape w-96 h-96 bg-blue-600 bottom-[-20%] right-[-10%]"></div>
        
        <div class="relative z-10 text-center px-8">
            <div class="inline-flex items-center justify-center w-32 h-32 bg-white rounded-3xl border border-white/20 mb-8 shadow-2xl overflow-hidden group hover:scale-105 transition-transform duration-500">
                <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-24 h-24 object-contain group-hover:rotate-12 transition-transform duration-500">
            </div>
            
            <h1 class="text-5xl font-extrabold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/70">Orvion</h1>
            <p class="text-xl text-white/80 font-medium mb-12">Next Generation HR Management</p>
            
            <div class="space-y-4 text-left max-w-xs mx-auto">
                <div class="flex items-center gap-4 text-white/90">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-semibold tracking-wide">Complete HR Management</span>
                </div>
                <div class="flex items-center gap-4 text-white/90">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10">
                        <i data-feather="shield" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-semibold tracking-wide">Labor Compliant</span>
                </div>
                <div class="flex items-center gap-4 text-white/90">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center border border-white/10">
                        <i data-feather="users" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-semibold tracking-wide">Employee Self Service</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Login Form -->
    <div class="login-right">
        <div class="login-form-card">
            <div class="lg:hidden text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#040344]/5 rounded-2xl mb-4">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-14 h-14 object-contain">
                </div>
                <h1 class="text-3xl font-bold text-[#040344]">Orvion</h1>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                <p class="text-slate-500 font-medium">Sign in to your account to continue.</p>
            </div>
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl flex items-center gap-3 animate-fadeIn">
                    <i data-feather="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                <div class="auth-input-group">
                    <label for="email" class="auth-label">Email Address</label>
                    <div class="auth-input-wrapper">
                        <i data-feather="mail" class="auth-input-icon"></i>
                        <input type="email" id="email" name="email" required autofocus
                               class="auth-input"
                               placeholder="name@company.com">
                    </div>
                </div>
                
                <div class="auth-input-group">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="auth-label mb-0">Password</label>
                        <a href="#" class="text-xs font-bold text-[#040344] hover:underline">Forgot?</a>
                    </div>
                    <div class="auth-input-wrapper">
                        <i data-feather="lock" class="auth-input-icon"></i>
                        <input type="password" id="password" name="password" required
                               class="auth-input"
                               placeholder="••••••••">
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="w-4 h-4 text-[#040344] border-slate-300 rounded focus:ring-[#040344]">
                    <label for="remember" class="ml-2 text-sm font-medium text-slate-600">Remember me for 30 days</label>
                </div>
                
                <button type="submit" class="auth-btn-primary group">
                    <span class="flex items-center justify-center gap-2">
                        LOGIN
                        <i data-feather="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </button>
            </form>
            
            <div class="mt-10 pt-8 border-t border-slate-50">
                <div class="text-center space-y-4">
                    <p class="text-sm text-slate-500 font-medium">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-[#040344] font-bold hover:underline">Sign up now</a>
                    </p>
                    
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('splash') }}" class="text-xs font-bold text-slate-400 hover:text-[#040344] transition-colors flex items-center gap-2">
                            <i data-feather="home" class="w-3.5 h-3.5"></i>
                            Back to Home
                        </a>
                        <span class="text-slate-200">|</span>
                        <a href="{{ route('sample-users') }}" class="text-xs font-bold text-slate-400 hover:text-[#040344] transition-colors flex items-center gap-2">
                            <i data-feather="users" class="w-3.5 h-3.5"></i>
                            Sample Users
                        </a>
                    </div>
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
