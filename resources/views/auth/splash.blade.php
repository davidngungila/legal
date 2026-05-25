@extends('layouts.auth')

@section('title', 'Welcome - LegalHR Tanzania')

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
            
            <div class="grid grid-cols-2 gap-4 text-left max-w-md mx-auto">
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="p-2 rounded-lg bg-indigo-500/20 text-indigo-300">
                        <i data-feather="check-circle" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium">HR Management</span>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="p-2 rounded-lg bg-emerald-500/20 text-emerald-300">
                        <i data-feather="shield" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium">Labor Compliant</span>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="p-2 rounded-lg bg-amber-500/20 text-amber-300">
                        <i data-feather="users" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium">Self Service</span>
                </div>
                <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                    <div class="p-2 rounded-lg bg-purple-500/20 text-purple-300">
                        <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                    </div>
                    <span class="text-sm font-medium">Analytics</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Side - Splash Options -->
    <div class="login-right">
        <div class="login-form-card">
            <div class="lg:hidden text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-[#040344]/5 rounded-2xl mb-4">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-14 h-14 object-contain">
                </div>
                <h1 class="text-3xl font-bold text-[#040344]">Orvion</h1>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Welcome to LegalHR</h2>
                <p class="text-slate-500 font-medium">Choose how you'd like to get started today.</p>
            </div>
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-xl flex items-center gap-3 animate-fadeIn">
                    <i data-feather="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 animate-fadeIn">
                    <i data-feather="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif
            
            <div class="space-y-4">
                <a href="{{ route('login') }}" class="auth-btn-primary group">
                    <span class="flex items-center justify-center gap-3">
                        <i data-feather="log-in" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        Login to Existing Account
                    </span>
                </a>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                    <div class="relative flex justify-center text-xs uppercase"><span class="bg-white px-4 text-slate-400 font-bold tracking-widest">or register</span></div>
                </div>

                <a href="{{ route('register') }}" class="auth-btn-outline group">
                    <i data-feather="user-plus" class="w-5 h-5 text-indigo-500 group-hover:scale-110 transition-transform"></i>
                    Register as User
                </a>
                
                <a href="{{ route('client-registration.create') }}" class="auth-btn-outline group">
                    <i data-feather="building" class="w-5 h-5 text-emerald-500 group-hover:scale-110 transition-transform"></i>
                    Register Company
                </a>
                
                <a href="{{ route('job-vacancy.index') }}" class="auth-btn-outline group">
                    <i data-feather="briefcase" class="w-5 h-5 text-purple-500 group-hover:scale-110 transition-transform"></i>
                    Recruitment Portal
                </a>
            </div>
            
            <div class="mt-10 pt-8 border-t border-slate-50 text-center">
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="{{ route('sample-users') }}" class="text-sm font-bold text-slate-400 hover:text-[#040344] transition-colors flex items-center gap-2">
                        <i data-feather="users" class="w-4 h-4"></i>
                        Sample Users
                    </a>
                    <a href="#" class="text-sm font-bold text-slate-400 hover:text-[#040344] transition-colors flex items-center gap-2">
                        <i data-feather="help-circle" class="w-4 h-4"></i>
                        Need Help?
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
