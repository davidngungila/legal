@extends('layouts.auth')

@section('title', 'Register - LegalHR Tanzania')

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
    
    <!-- Right Side - Registration Form -->
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
                <h2 class="text-xl md:text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                <p class="text-sm md:text-base text-gray-600">Fill in your information to get started</p>
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
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <div class="relative">
                            <i data-feather="user" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                            <input type="text" id="first_name" name="first_name" required
                                   class="form-input pl-10"
                                   placeholder="Enter your first name"
                                   value="{{ old('first_name') }}">
                        </div>
                        @error('first_name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <div class="relative">
                            <i data-feather="user" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                            <input type="text" id="last_name" name="last_name" required
                                   class="form-input pl-10"
                                   placeholder="Enter your last name"
                                   value="{{ old('last_name') }}">
                        </div>
                        @error('last_name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i data-feather="mail" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="email" id="email" name="email" required
                               class="form-input pl-10"
                               placeholder="Enter your email"
                               value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <div class="relative">
                        <i data-feather="phone" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="tel" id="phone" name="phone" required
                               class="form-input pl-10"
                               placeholder="Enter your phone number"
                               value="{{ old('phone') }}">
                    </div>
                    @error('phone')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <div class="relative">
                        <i data-feather="building" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <input type="text" id="company" name="company" required
                               class="form-input pl-10"
                               placeholder="Enter your company name"
                               value="{{ old('company') }}">
                    </div>
                    @error('company')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                    <div class="relative">
                        <i data-feather="briefcase" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                        <select id="role" name="role" required
                                class="form-input pl-10 appearance-none">
                            <option value="">Select your role</option>
                            <option value="hr_admin" {{ old('role') == 'hr_admin' ? 'selected' : '' }}>HR Administrator</option>
                            <option value="hr_officer" {{ old('role') == 'hr_officer' ? 'selected' : '' }}>HR Officer</option>
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Line Manager</option>
                            <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>
                    @error('role')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i data-feather="lock" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                            <input type="password" id="password" name="password" required
                                   class="form-input pl-10"
                                   placeholder="Create a strong password">
                        </div>
                        @error('password')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <div class="relative">
                            <i data-feather="lock" class="w-5 h-5 text-gray-400 absolute left-3 top-3"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="form-input pl-10"
                                   placeholder="Confirm your password">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="terms" id="terms" required
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to Terms of Service and Privacy Policy, and confirm that I will comply with Tanzania Labour Laws and regulations.
                    </label>
                </div>
                
                <button type="submit" class="btn-primary">
                    <span class="flex items-center justify-center">
                        <i data-feather="user-plus" class="w-5 h-5 mr-2"></i>
                        CREATE ACCOUNT
                    </span>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <div class="signup-link">
                    <span class="text-gray-600 text-sm">Already have an account? </span>
                    <a href="{{ route('login') }}" class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium">Sign in</a>
                    <span class="mx-2 text-gray-400">|</span>
                    <a href="{{ route('splash') }}" class="text-[#040344] hover:text-[#040344]/80 text-sm font-medium">Go to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add entrance animations to form elements
    const formElements = document.querySelectorAll('.login-form > *');
    formElements.forEach((element, index) => {
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
