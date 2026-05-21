@extends('layouts.auth')

@section('title', 'Welcome - LegalHR Tanzania')

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
    
    <!-- Right Side - Splash Options -->
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
                <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-2">Welcome to LegalHR</h2>
                <p class="text-sm md:text-base text-gray-600">Choose how you'd like to get started</p>
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
            
            <!-- Registration Options -->
            <div class="space-y-4">
                <!-- Register as User -->
                <a href="{{ route('register') }}" 
                   class="w-full flex items-center justify-center px-6 py-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i data-feather="user-plus" class="w-5 h-5 mr-3"></i>
                    <span class="font-semibold">Register as User</span>
                </a>
                
                <!-- Register Company -->
                <a href="{{ route('client-registration.create') }}" 
                   class="w-full flex items-center justify-center px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i data-feather="building" class="w-5 h-5 mr-3"></i>
                    <span class="font-semibold">Register Company</span>
                </a>
                
                <!-- Recruitment Portal -->
                <a href="{{ route('job-vacancy.index') }}" 
                   class="w-full flex items-center justify-center px-6 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i data-feather="briefcase" class="w-5 h-5 mr-3"></i>
                    <span class="font-semibold">Recruitment Portal</span>
                </a>
                
                <!-- Login -->
                <a href="{{ route('login') }}" 
                   class="w-full flex items-center justify-center px-6 py-4 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i data-feather="log-in" class="w-5 h-5 mr-3"></i>
                    <span class="font-semibold">Login to Existing Account</span>
                </a>
            </div>
            
            <!-- Additional Options -->
            <div class="mt-8 text-center">
                <div class="space-y-3">
                    <a href="{{ route('sample-users') }}" 
                       class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium block">
                        <i data-feather="users" class="w-4 h-4 inline mr-1"></i>
                        View Sample Users
                    </a>
                    
                    <a href="#" 
                       class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium block">
                        <i data-feather="help-circle" class="w-4 h-4 inline mr-1"></i>
                        Need Help?
                    </a>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="text-center">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">System Features</h3>
                    <div class="grid grid-cols-2 gap-2 text-xs text-blue-700">
                        <div class="flex items-center">
                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                            <span>HR Management</span>
                        </div>
                        <div class="flex items-center">
                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                            <span>Payroll System</span>
                        </div>
                        <div class="flex items-center">
                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                            <span>Leave Management</span>
                        </div>
                        <div class="flex items-center">
                            <i data-feather="check" class="w-3 h-3 mr-1"></i>
                            <span>Performance Tracking</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animations to splash elements
    const splashElements = document.querySelectorAll('.login-form > *');
    splashElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            element.style.transition = 'all 0.5s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });
});
</script>
