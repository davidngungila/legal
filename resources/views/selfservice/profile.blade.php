@extends('layouts.app')

@section('title', 'Update Profile - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Update Profile</h1>
            <p class="text-gray-600 mt-2">Manage your personal information and contact details</p>
            @if($currentClient)
            <div class="mt-2 flex items-center space-x-2">
                <span class="text-sm text-gray-500">Updating profile for:</span>
                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">{{ $currentClient->name }}</span>
            </div>
            @endif
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('selfservice.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="arrow-left" class="w-4 h-4 inline mr-2"></i>
                Back to Self Service
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('selfservice.profile.update') }}" class="space-y-6">
                @csrf
                
                <!-- Personal Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" id="first_name" name="first_name" value="{{ $user->first_name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" id="last_name" name="last_name" value="{{ $user->last_name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ $user->phone }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Employee Information (if available) -->
                @if($employee)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Employee Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $employee->address }}</textarea>
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" id="city" name="city" value="{{ $employee->city }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <input type="text" id="country" name="country" value="{{ $employee->country }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ $employee->postal_code }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Emergency Contact</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ $employee->emergency_contact_name }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        
                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                            <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" value="{{ $employee->emergency_contact_phone }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                @endif

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Profile Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Profile</h3>
                
                @if($employee)
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                            <span class="text-xl font-bold text-indigo-600">{{ substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                            <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Department:</span>
                            <span class="text-gray-900">{{ $employee->department }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Email:</span>
                            <span class="text-gray-900">{{ $employee->email }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Phone:</span>
                            <span class="text-gray-900">{{ $employee->phone ?? 'Not set' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Status:</span>
                            <span>{!! $employee->status_badge !!}</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="user" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                    <p>No employee record found</p>
                    <p class="text-sm mt-2">Contact HR to update your profile.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
