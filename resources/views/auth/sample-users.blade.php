@extends('layouts.auth')

@section('title', 'Sample Users - LegalHR Tanzania')

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
            <div class="mb-8">
                <div class="w-36 h-36 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-40 h-40 object-contain">
                </div>
            </div>
            
            <!-- System Info -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2">Orvion</h1>
                <p class="text-xl opacity-90">HR Management System</p>
            </div>
            
            <div class="space-y-4 text-lg">
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="check-circle" class="w-5 h-5"></i>
                    <span>Complete HR Management</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Labor Compliant</span>
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
    
    <!-- Right Side - Sample Users -->
    <div class="login-right">
        <div class="login-form">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Sample Users</h2>
                <p class="text-gray-600">Use these credentials to test the system</p>
            </div>
            
            <div class="space-y-4">
                <!-- Super Admin -->
                <div class="bg-gradient-to-r from-purple-50 to-indigo-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-purple-900">Super Admin</h3>
                        <span class="px-2 py-1 bg-purple-600 text-white text-xs rounded-full">Full Access</span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div><strong>Email:</strong> admin@legalhr.com</div>
                        <div><strong>Password:</strong> admin123</div>
                    </div>
                    <button onclick="fillCredentials('admin@legalhr.com', 'admin123')" 
                            class="mt-2 w-full bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition-colors">
                        Use These Credentials
                    </button>
                </div>
                
                <!-- HR Admin -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-blue-900">HR Admin</h3>
                        <span class="px-2 py-1 bg-blue-600 text-white text-xs rounded-full">HR Access</span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div><strong>Email:</strong> hr@legalhr.com</div>
                        <div><strong>Password:</strong> hr123</div>
                    </div>
                    <button onclick="fillCredentials('hr@legalhr.com', 'hr123')" 
                            class="mt-2 w-full bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                        Use These Credentials
                    </button>
                </div>
                
                <!-- Manager -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-green-900">Department Manager</h3>
                        <span class="px-2 py-1 bg-green-600 text-white text-xs rounded-full">Limited Access</span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div><strong>Email:</strong> manager@legalhr.com</div>
                        <div><strong>Password:</strong> manager123</div>
                    </div>
                    <button onclick="fillCredentials('manager@legalhr.com', 'manager123')" 
                            class="mt-2 w-full bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                        Use These Credentials
                    </button>
                </div>
                
                <!-- Employee -->
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900">Employee</h3>
                        <span class="px-2 py-1 bg-gray-600 text-white text-xs rounded-full">Self Service</span>
                    </div>
                    <div class="space-y-1 text-sm">
                        <div><strong>Email:</strong> employee@legalhr.com</div>
                        <div><strong>Password:</strong> emp123</div>
                    </div>
                    <button onclick="fillCredentials('employee@legalhr.com', 'emp123')" 
                            class="mt-2 w-full bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition-colors">
                        Use These Credentials
                    </button>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <div class="space-y-2">
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">← Back to Login</a>
                    <div class="text-gray-500 text-xs">
                        💡 Click any "Use These Credentials" button to auto-fill the login form
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillCredentials(email, password) {
    // Store credentials in sessionStorage
    sessionStorage.setItem('loginEmail', email);
    sessionStorage.setItem('loginPassword', password);
    
    // Redirect to login page
    window.location.href = '{{ route('login') }}';
}

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
@endsection
