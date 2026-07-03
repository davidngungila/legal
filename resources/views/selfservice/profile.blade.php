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
                
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 relative group" id="profile-photo-container">
                        @if($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-full h-full rounded-full object-cover" id="profile-photo-img">
                        @else
                            <span class="text-white text-3xl font-bold" id="profile-photo-initials">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('photo-upload').click()">
                            <i data-feather="camera" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <input type="file" id="photo-upload" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewPhoto(this)">
                    
                    <p class="font-semibold text-gray-900 text-xl">{{ $user->first_name }} {{ $user->last_name }}</p>
                    @if($user->roles->count() > 0)
                        <div class="flex items-center justify-center space-x-1 mt-2 mb-2">
                            @foreach($user->roles as $role)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">{{ $role->display_name ?? $role->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if($employee)
                        <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                        <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                    @endif
                </div>
                
                @if($employee)
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentPhotoFile = null;

function previewPhoto(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please upload a JPG, PNG, or GIF image.');
        input.value = '';
        return;
    }
    
    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        alert('File size too large. Please upload an image smaller than 5MB.');
        input.value = '';
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        const container = document.getElementById('profile-photo-container');
        let img = container.querySelector('img');
        let initials = container.querySelector('#profile-photo-initials');
        
        if (!img) {
            img = document.createElement('img');
            img.id = 'profile-photo-img';
            img.className = 'w-full h-full rounded-full object-cover';
            container.appendChild(img);
        }
        
        img.src = e.target.result;
        img.style.display = 'block';
        if (initials) initials.style.display = 'none';
        
        currentPhotoFile = file;
        uploadPhoto();
    };
    reader.readAsDataURL(file);
}

async function uploadPhoto() {
    if (!currentPhotoFile) return;
    
    const formData = new FormData();
    formData.append('photo', currentPhotoFile);
    
    try {
        const response = await fetch('/profile/photo', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: formData
        });
        
        const result = await response.json();
        if (result.success) {
            // Update UI if we have the photo URL
            if (result.photo_url) {
                const container = document.getElementById('profile-photo-container');
                let img = container.querySelector('img');
                let initials = container.querySelector('#profile-photo-initials');
                
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'profile-photo-img';
                    img.className = 'w-full h-full rounded-full object-cover';
                    container.appendChild(img);
                }
                
                img.src = result.photo_url;
                img.style.display = 'block';
                if (initials) initials.style.display = 'none';
            }
            
            alert('Profile photo updated successfully!');
        } else {
            alert(result.message || 'Failed to update photo');
        }
    } catch (error) {
        console.error('Photo update error:', error);
        alert('An error occurred while updating photo');
    }
}
</script>
@endpush
