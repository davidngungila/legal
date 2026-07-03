@extends('layouts.app')

@section('title', 'My Profile - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">My Profile</h1>
            <p class="text-gray-600 mt-2">Manage your personal information and preferences</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Profile
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="save" class="w-4 h-4 inline mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <div class="w-24 h-24 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 relative group" id="profile-photo-container">
                        @if($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-full h-full rounded-full object-cover" id="profile-photo-img">
                        @else
                            <span class="text-white text-3xl font-bold" id="profile-photo-initials">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('photo-upload').click()">
                            <i data-feather="camera" class="w-6 h-6 text-white"></i>
                        </div>
                        <input type="file" id="photo-upload" class="hidden" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="profileManager.previewPhoto(this)">
                    </div>
                    
                    <!-- Photo Preview Modal -->
                    <div id="photo-preview-modal" class="fixed inset-0 modal-backdrop-blur z-50 hidden flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl max-w-lg w-full p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Preview Photo</h3>
                            <div class="mb-4">
                                <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100">
                                    <img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="mb-4 text-sm text-gray-600">
                                <p>File: <span id="preview-file-name"></span></p>
                                <p>Size: <span id="preview-file-size"></span></p>
                                <p>Type: <span id="preview-file-type"></span></p>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="profileManager.cancelPhotoUpload()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                    Cancel
                                </button>
                                <button type="button" onclick="profileManager.uploadPhoto()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700" id="upload-photo-btn">
                                    Upload Photo
                                </button>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    <p class="text-gray-600 mb-2">{{ $user->job_title ?? 'Employee' }}</p>
                    @if($user->roles->count() > 0)
                        <div class="flex items-center justify-center space-x-1 mb-3">
                            @foreach($user->roles as $role)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">{{ $role->display_name ?? $role->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex items-center justify-center space-x-2 mb-4">
                        <span class="px-3 py-1 {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-sm font-semibold rounded-full">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">Online</span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-center">
                            <i data-feather="mail" class="w-4 h-4 mr-2"></i>
                            {{ $user->email }}
                        </div>
                        @if($user->phone)
                        <div class="flex items-center justify-center">
                            <i data-feather="phone" class="w-4 h-4 mr-2"></i>
                            {{ $user->phone }}
                        </div>
                        @endif
                        @if($user->location)
                        <div class="flex items-center justify-center">
                            <i data-feather="map-pin" class="w-4 h-4 mr-2"></i>
                            {{ $user->location }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3">
                        <button onclick="document.getElementById('photo-upload').click()" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                            <i data-feather="camera" class="w-4 h-4 inline mr-2"></i>
                            Change Photo
                        </button>
                        @if($user->profile_photo)
                        <button onclick="profileManager.deleteProfilePhoto()" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>
                            Remove Photo
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h4 class="font-semibold text-gray-900 mb-4">Quick Stats</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Member Since</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['member_since'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Last Login</span>
                        <span class="text-sm font-medium text-gray-900">{{ $stats['last_login'] }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Profile Completion</span>
                        <span class="text-sm font-medium text-green-600">{{ $stats['profile_completion'] }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Security Score</span>
                        <span class="text-sm font-medium {{ $stats['security_score'] >= 80 ? 'text-green-600' : ($stats['security_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $stats['security_score'] >= 80 ? 'Strong' : ($stats['security_score'] >= 60 ? 'Medium' : 'Weak') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            @if($currentClient)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h4 class="font-semibold text-gray-900 mb-4">Current Client</h4>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $currentClient->name }}</p>
                            <p class="text-sm text-gray-600">{{ $currentClient->industry }}</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Industry</span>
                                <span class="font-medium">{{ $currentClient->industry }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Employees</span>
                                <span class="font-medium">{{ $currentClient->employee_count ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="px-2 py-1 {{ $currentClient->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs font-semibold rounded-full">
                                    {{ $currentClient->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Profile Details -->
        <div class="lg:col-span-2">
            <!-- Personal Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" value="{{ $user->first_name }}" class="form-input" data-profile-field>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" value="{{ $user->last_name }}" class="form-input" data-profile-field>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="form-input" data-profile-field>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" class="form-input" data-profile-field>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                        <input type="date" value="1985-06-15" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                        <select class="form-select">
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text" value="Plot 123, Upanga Road, Dar es Salaam" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" value="Dar es Salaam" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select class="form-select">
                            <option selected>Tanzania</option>
                            <option>Kenya</option>
                            <option>Uganda</option>
                            <option>Rwanda</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Professional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee ID</label>
                        <input type="text" value="EMP001" class="form-input" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                        <select class="form-select">
                            <option selected>Human Resources</option>
                            <option>Finance</option>
                            <option>IT</option>
                            <option>Operations</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Title</label>
                        <input type="text" value="HR Administrator" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Manager</label>
                        <input type="text" value="Sarah Williams" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" value="2023-01-15" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                        <select class="form-select">
                            <option selected>Permanent</option>
                            <option>Contract</option>
                            <option>Probation</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Name</label>
                        <input type="text" value="Jane Doe" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Relationship</label>
                        <select class="form-select">
                            <option selected>Spouse</option>
                            <option>Parent</option>
                            <option>Sibling</option>
                            <option>Friend</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" value="+255 22 987 6543" class="form-input">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" value="jane.doe@email.com" class="form-input">
                    </div>
                </div>
            </div>

            <!-- User Preferences -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">User Preferences</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Appearance</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                                <select class="form-select" name="theme" data-setting-field>
                                    <option value="light" {{ $settings->theme === 'light' ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ $settings->theme === 'dark' ? 'selected' : '' }}>Dark</option>
                                    <option value="auto" {{ $settings->theme === 'auto' ? 'selected' : '' }}>Auto</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                <select class="form-select" name="language" data-setting-field>
                                    <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="sw" {{ $settings->language === 'sw' ? 'selected' : '' }}>Swahili</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                <select class="form-select" name="timezone" data-setting-field>
                                    <option value="Africa/Dar_es_Salaam" {{ $settings->timezone === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Dar es Salaam</option>
                                    <option value="Africa/Nairobi" {{ $settings->timezone === 'Africa/Nairobi' ? 'selected' : '' }}>Nairobi</option>
                                    <option value="UTC" {{ $settings->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Notifications</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3" {{ $settings->notification_email ? 'checked' : '' }} name="notification_email" data-setting-field>
                                <span class="text-sm text-gray-700">Email notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3" {{ $settings->notification_push ? 'checked' : '' }} name="notification_push" data-setting-field>
                                <span class="text-sm text-gray-700">Push notifications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3" {{ $settings->notification_sms ? 'checked' : '' }} name="notification_sms" data-setting-field>
                                <span class="text-sm text-gray-700">SMS notifications</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Security Settings</h3>
                <div class="space-y-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Change Password</h4>
                        <form id="password-change-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="form-input" required>
                                    <span class="text-xs text-red-600 hidden" id="current_password_error"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" name="password" id="new_password" class="form-input" required minlength="8">
                                    <span class="text-xs text-red-600 hidden" id="new_password_error"></span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>
                                    <span class="text-xs text-red-600 hidden" id="password_confirmation_error"></span>
                                </div>
                            </div>
                            
                            <!-- Password Strength Indicator -->
                            <div class="hidden" id="password-strength">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">Password Strength:</span>
                                    <span class="text-sm font-medium" id="strength-text">Weak</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full transition-all duration-300" id="strength-bar"></div>
                                </div>
                            </div>
                            
                            <!-- Password Requirements -->
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 mb-2">Password Requirements:</p>
                                <ul class="text-xs text-gray-600 space-y-1">
                                    <li class="flex items-center">
                                        <i data-feather="check-circle" class="w-3 h-3 mr-1" id="req-length"></i>
                                        At least 8 characters
                                    </li>
                                    <li class="flex items-center">
                                        <i data-feather="check-circle" class="w-3 h-3 mr-1" id="req-uppercase"></i>
                                        At least one uppercase letter
                                    </li>
                                    <li class="flex items-center">
                                        <i data-feather="check-circle" class="w-3 h-3 mr-1" id="req-lowercase"></i>
                                        At least one lowercase letter
                                    </li>
                                    <li class="flex items-center">
                                        <i data-feather="check-circle" class="w-3 h-3 mr-1" id="req-number"></i>
                                        At least one number
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors" id="change-password-btn">
                                    <i data-feather="lock" class="w-4 h-4 mr-2"></i>
                                    Change Password
                                </button>
                                <button type="button" onclick="profileManager.clearPasswordForm()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                                    Clear
                                </button>
                            </div>
                        </form>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Two-Factor Authentication</h4>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Enable 2FA</p>
                                <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                            </div>
                            <button class="px-4 py-2 {{ $settings->two_factor_enabled ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors text-sm" onclick="profileManager.toggle2FA()">
                                {{ $settings->two_factor_enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Login Preferences</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3" {{ $settings->auto_logout ? 'checked' : '' }} name="auto_logout" data-setting-field>
                                <span class="text-sm text-gray-700">Auto-logout after inactivity</span>
                            </label>
                            <div class="flex items-center space-x-4">
                                <label class="text-sm text-gray-700">Session timeout (minutes):</label>
                                <input type="number" name="session_timeout" value="{{ $settings->session_timeout }}" class="form-input w-20" data-setting-field>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Profile Management System
class ProfileManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupAutoSave();
        this.initializeFeather();
    }

    setupEventListeners() {
        // Profile form submission
        const saveBtn = document.querySelector('button:has(.feather-save)');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveAll());
        }

        // Export profile
        const exportBtn = document.querySelector('button:has(.feather-download)');
        if (exportBtn) {
            exportBtn.addEventListener('click', () => this.exportProfile());
        }

        // Profile field changes
        document.querySelectorAll('[data-profile-field]').forEach(field => {
            field.addEventListener('input', () => this.markAsChanged(field));
        });

        // Settings field changes
        document.querySelectorAll('[data-setting-field]').forEach(field => {
            field.addEventListener('change', () => this.saveSettings());
            if (field.type === 'text' || field.type === 'number') {
                field.addEventListener('input', () => this.saveSettings());
            }
        });

        // Password change form
        this.setupPasswordChangeForm();

        // Security settings
        this.setupSecuritySettings();
    }

    setupAutoSave() {
        let autoSaveTimer;
        document.querySelectorAll('[data-profile-field]').forEach(field => {
            field.addEventListener('input', () => {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => this.autoSave(), 2000);
            });
        });
    }

    setupPasswordChangeForm() {
        const form = document.getElementById('password-change-form');
        if (!form) return;

        // Form submission
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.changePassword();
        });

        // Password strength checker
        const newPasswordInput = document.getElementById('new_password');
        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', () => {
                this.checkPasswordStrength();
                this.validatePasswordRequirements();
            });
        }

        // Confirm password validation
        const confirmInput = document.getElementById('password_confirmation');
        if (confirmInput) {
            confirmInput.addEventListener('input', () => {
                this.validatePasswordConfirmation();
            });
        }

        // Clear errors on input
        ['current_password', 'new_password', 'password_confirmation'].forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (input) {
                input.addEventListener('input', () => {
                    this.clearFieldError(fieldId);
                });
            }
        });
    }

    setupSecuritySettings() {
        // 2FA toggle
        const twoFaBtn = document.querySelector('button[onclick*="toggle2FA"]');
        if (twoFaBtn) {
            twoFaBtn.addEventListener('click', () => this.toggle2FA());
        }
    }

    showLoading(text = 'Loading...') {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'loading-overlay';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        loadingDiv.innerHTML = `
            <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
                <i data-feather="loader" class="w-6 h-6 animate-spin text-indigo-600"></i>
                <span class="text-gray-800 font-medium">${text}</span>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        feather.replace();
    }

    hideLoading() {
        const loadingDiv = document.getElementById('loading-overlay');
        if (loadingDiv) loadingDiv.remove();
    }

    showNotification(message, type = 'success') {
        const notificationDiv = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-100 text-green-800 border-green-400' : 
                        type === 'error' ? 'bg-red-100 text-red-800 border-red-400' : 'bg-yellow-100 text-yellow-800 border-yellow-400';
        notificationDiv.className = `fixed top-4 right-4 px-6 py-4 rounded-lg border ${bgColor} shadow-lg z-50 animate-fade-in`;
        notificationDiv.textContent = message;
        document.body.appendChild(notificationDiv);
        
        setTimeout(() => {
            notificationDiv.remove();
        }, 5000);
    }

    markAsChanged(field) {
        field.classList.add('border-yellow-400');
    }

    clearChangedFields() {
        document.querySelectorAll('[data-profile-field]').forEach(field => {
            field.classList.remove('border-yellow-400');
        });
    }

    checkPasswordStrength() {
        const newPasswordInput = document.getElementById('new_password');
        const strengthText = document.getElementById('strength-text');
        const strengthBar = document.getElementById('strength-bar');
        const strengthDiv = document.getElementById('password-strength');
        
        if (!newPasswordInput || !strengthText || !strengthBar || !strengthDiv) return;
        
        const password = newPasswordInput.value;
        let strength = 0;
        
        if (password.length >= 8) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[a-z]/.test(password)) strength += 25;
        if (/[0-9]/.test(password)) strength += 25;
        
        strengthDiv.classList.remove('hidden');
        
        let color, label;
        if (strength <= 25) { color = 'bg-red-500'; label = 'Weak'; }
        else if (strength <= 50) { color = 'bg-orange-500'; label = 'Fair'; }
        else if (strength <= 75) { color = 'bg-yellow-500'; label = 'Good'; }
        else { color = 'bg-green-500'; label = 'Strong'; }
        
        strengthText.textContent = label;
        strengthBar.className = `h-2 rounded-full transition-all duration-300 ${color}`;
        strengthBar.style.width = `${strength}%`;
    }

    validatePasswordRequirements() {
        const newPasswordInput = document.getElementById('new_password');
        if (!newPasswordInput) return;
        
        const password = newPasswordInput.value;
        
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumber = document.getElementById('req-number');
        
        if (reqLength) {
            const icon = reqLength.querySelector('i');
            if (icon) icon.setAttribute('data-feather', password.length >= 8 ? 'check-circle' : 'x-circle');
        }
        if (reqUppercase) {
            const icon = reqUppercase.querySelector('i');
            if (icon) icon.setAttribute('data-feather', /[A-Z]/.test(password) ? 'check-circle' : 'x-circle');
        }
        if (reqLowercase) {
            const icon = reqLowercase.querySelector('i');
            if (icon) icon.setAttribute('data-feather', /[a-z]/.test(password) ? 'check-circle' : 'x-circle');
        }
        if (reqNumber) {
            const icon = reqNumber.querySelector('i');
            if (icon) icon.setAttribute('data-feather', /[0-9]/.test(password) ? 'check-circle' : 'x-circle');
        }
        
        feather.replace();
    }

    validatePasswordConfirmation() {
        const newPasswordInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('password_confirmation');
        const errorSpan = document.getElementById('password_confirmation_error');
        
        if (!newPasswordInput || !confirmInput || !errorSpan) return;
        
        if (confirmInput.value && newPasswordInput.value !== confirmInput.value) {
            errorSpan.textContent = 'Passwords do not match';
            errorSpan.classList.remove('hidden');
        } else {
            errorSpan.textContent = '';
            errorSpan.classList.add('hidden');
        }
    }

    clearFieldError(fieldId) {
        const errorSpan = document.getElementById(`${fieldId}_error`);
        if (errorSpan) {
            errorSpan.textContent = '';
            errorSpan.classList.add('hidden');
        }
    }

    displayPasswordErrors(errors) {
        Object.keys(errors).forEach(key => {
            const errorSpan = document.getElementById(`${key}_error`);
            if (errorSpan) {
                errorSpan.textContent = errors[key];
                errorSpan.classList.remove('hidden');
            }
        });
    }

    clearPasswordForm() {
        const form = document.getElementById('password-change-form');
        if (form) form.reset();
        
        const strengthDiv = document.getElementById('password-strength');
        if (strengthDiv) strengthDiv.classList.add('hidden');
        
        ['current_password', 'new_password', 'password_confirmation'].forEach(fieldId => {
            this.clearFieldError(fieldId);
        });
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    async changePassword() {
        const form = document.getElementById('password-change-form');
        const formData = new FormData(form);
        
        if (!this.validatePasswordForm()) {
            return;
        }

        const data = {
            current_password: formData.get('current_password'),
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation')
        };

        try {
            const submitBtn = document.getElementById('change-password-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> Changing Password...';
            feather.replace();
            
            const response = await fetch('/profile/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Password changed successfully! Please login again.', 'success');
                this.clearPasswordForm();
                
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else {
                this.showNotification(result.message || 'Failed to change password', 'error');
                if (result.errors) {
                    this.displayPasswordErrors(result.errors);
                }
            }
        } catch (error) {
            console.error('Password change error:', error);
            this.showNotification('An error occurred while changing password', 'error');
        } finally {
            const submitBtn = document.getElementById('change-password-btn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i data-feather="lock" class="w-4 h-4 mr-2"></i> Change Password';
                feather.replace();
            }
        }
    }

    validatePasswordForm() {
        let isValid = true;
        const currentPassword = document.getElementById('current_password');
        const newPassword = document.getElementById('new_password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        if (!currentPassword.value) {
            this.clearFieldError('current_password');
            const errorSpan = document.getElementById('current_password_error');
            if (errorSpan) {
                errorSpan.textContent = 'Please enter your current password';
                errorSpan.classList.remove('hidden');
            }
            isValid = false;
        }
        
        if (!newPassword.value) {
            this.clearFieldError('new_password');
            const errorSpan = document.getElementById('new_password_error');
            if (errorSpan) {
                errorSpan.textContent = 'Please enter a new password';
                errorSpan.classList.remove('hidden');
            }
            isValid = false;
        }
        
        if (!passwordConfirmation.value) {
            this.clearFieldError('password_confirmation');
            const errorSpan = document.getElementById('password_confirmation_error');
            if (errorSpan) {
                errorSpan.textContent = 'Please confirm your new password';
                errorSpan.classList.remove('hidden');
            }
            isValid = false;
        }
        
        return isValid;
    }

    async autoSave() {
        const changedFields = document.querySelectorAll('[data-profile-field].border-yellow-400');
        if (changedFields.length === 0) return;

        try {
            const formData = new FormData();
            changedFields.forEach(field => {
                formData.append(field.name, field.value);
            });

            const response = await fetch('/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();
            if (result.success) {
                this.clearChangedFields();
                this.showNotification('Changes auto-saved', 'info');
            }
        } catch (error) {
            console.error('Auto-save error:', error);
        }
    }

    async saveProfile() {
        const formData = new FormData();
        const fields = document.querySelectorAll('[data-profile-field]');
        
        fields.forEach(field => {
            formData.append(field.name, field.value);
        });

        try {
            this.showLoading('Saving profile...');
            
            const response = await fetch('/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Profile updated successfully!', 'success');
                this.clearChangedFields();
            } else {
                this.showNotification(result.message || 'Failed to update profile', 'error');
            }
        } catch (error) {
            console.error('Profile update error:', error);
            this.showNotification('An error occurred while updating profile', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async saveSettings() {
        const formData = new FormData();
        const fields = document.querySelectorAll('[data-setting-field]');
        
        fields.forEach(field => {
            if (field.type === 'checkbox') {
                formData.append(field.name, field.checked);
            } else {
                formData.append(field.name, field.value);
            }
        });

        try {
            const response = await fetch('/profile/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Settings updated successfully!', 'success');
            } else {
                this.showNotification(result.message || 'Failed to update settings', 'error');
            }
        } catch (error) {
            console.error('Settings update error:', error);
            this.showNotification('An error occurred while updating settings', 'error');
        }
    }

    async saveAll() {
        await this.saveProfile();
        await this.saveSettings();
    }

    // Enhanced photo management
    previewPhoto(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];
        
        // Validate file type and size
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            this.showNotification('Invalid file type. Please upload a JPG, PNG, or GIF image.', 'error');
            input.value = '';
            return;
        }
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            this.showNotification('File size too large. Please upload an image smaller than 5MB.', 'error');
            input.value = '';
            return;
        }

        // Preview the image
        const reader = new FileReader();
        reader.onload = (e) => {
            this.showPhotoPreview(e.target.result, file);
        };
        reader.readAsDataURL(file);
    }

    showPhotoPreview(imageSrc, file) {
        const modal = document.getElementById('photo-preview-modal');
        const previewImg = document.getElementById('preview-img');
        const fileName = document.getElementById('preview-file-name');
        const fileSize = document.getElementById('preview-file-size');
        const fileType = document.getElementById('preview-file-type');

        if (previewImg) previewImg.src = imageSrc;
        if (fileName) fileName.textContent = file.name;
        if (fileSize) fileSize.textContent = this.formatFileSize(file.size);
        if (fileType) fileType.textContent = file.type;

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        this.currentPhotoFile = file;
    }

    cancelPhotoUpload() {
        const modal = document.getElementById('photo-preview-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        
        document.getElementById('photo-upload').value = '';
        this.currentPhotoFile = null;
    }

    async uploadPhoto() {
        if (!this.currentPhotoFile) {
            this.showNotification('No photo selected', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('photo', this.currentPhotoFile);

        try {
            const uploadBtn = document.getElementById('upload-photo-btn');
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> Uploading...';
                feather.replace();
            }
            
            const response = await fetch('/profile/photo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Profile photo updated successfully!', 'success');
                this.updatePhotoDisplay(result.photo_url);
                this.updateUserPhotoInUI(result.photo_url);
                
                const modal = document.getElementById('photo-preview-modal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                
                document.getElementById('photo-upload').value = '';
                this.currentPhotoFile = null;
            } else {
                this.showNotification(result.message || 'Failed to update photo', 'error');
            }
        } catch (error) {
            console.error('Photo update error:', error);
            this.showNotification('An error occurred while updating photo', 'error');
        } finally {
            const uploadBtn = document.getElementById('upload-photo-btn');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Upload Photo';
                feather.replace();
            }
        }
    }

    updatePhotoDisplay(photoUrl) {
        const container = document.getElementById('profile-photo-container');
        const initials = document.getElementById('profile-photo-initials');
        const img = document.getElementById('profile-photo-img');
        
        if (photoUrl && img) {
            img.src = photoUrl;
            img.style.display = 'block';
            if (initials) initials.style.display = 'none';
        } else if (initials) {
            initials.style.display = 'block';
            if (img) img.style.display = 'none';
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    updateProfilePhoto(input) {
        this.previewPhoto(input);
    }

    updateUserPhotoInUI(photoUrl) {
        const photoElements = document.querySelectorAll('img[alt*="Profile Photo"], img[alt*="profile photo"]');
        photoElements.forEach(img => {
            if (photoUrl) {
                img.src = photoUrl;
                img.style.display = 'block';
            }
        });
    }

    async deleteProfilePhoto() {
        if (!confirm('Are you sure you want to delete your profile photo?')) return;

        try {
            this.showLoading('Deleting photo...');
            
            const response = await fetch('/profile/photo', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Profile photo deleted successfully!', 'success');
                this.updatePhotoDisplay(null);
                this.updateUserPhotoInUI(null);
            } else {
                this.showNotification(result.message || 'Failed to delete photo', 'error');
            }
        } catch (error) {
            console.error('Photo delete error:', error);
            this.showNotification('An error occurred while deleting photo', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async exportProfile() {
        try {
            this.showLoading('Exporting profile...');
            
            const response = await fetch('/profile/export', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            });

            const result = await response.json();

            if (result.success) {
                const dataStr = JSON.stringify(result, null, 2);
                const dataUri = 'data:application/json;charset=utf-8,'+ encodeURIComponent(dataStr);
                const exportFileDefaultName = `profile-export-${new Date().toISOString().split('T')[0]}.json`;
                
                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();
                
                this.showNotification('Profile exported successfully!', 'success');
            } else {
                this.showNotification(result.message || 'Failed to export profile', 'error');
            }
        } catch (error) {
            console.error('Profile export error:', error);
            this.showNotification('An error occurred while exporting profile', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async toggle2FA() {
        try {
            this.showLoading('Updating 2FA settings...');
            
            const response = await fetch('/settings/security', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    two_factor_enabled: true
                })
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('2FA enabled successfully!', 'success');
                // Update UI
                const btn = document.querySelector('button:has-text("Enable 2FA")');
                if (btn) {
                    btn.textContent = 'Disable 2FA';
                    btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                }
            } else {
                this.showNotification(result.message || 'Failed to update 2FA settings', 'error');
            }
        } catch (error) {
            console.error('2FA toggle error:', error);
            this.showNotification('An error occurred while updating 2FA settings', 'error');
        } finally {
            this.hideLoading();
        }
    }
}

// Initialize ProfileManager
let profileManager;
document.addEventListener('DOMContentLoaded', () => {
    profileManager = new ProfileManager();
});

// Helper functions for onclick attributes
function previewPhoto(input) { profileManager.previewPhoto(input); }
function cancelPhotoUpload() { profileManager.cancelPhotoUpload(); }
function uploadPhoto() { profileManager.uploadPhoto(); }
function updateProfilePhoto(input) { profileManager.updateProfilePhoto(input); }
function deleteProfilePhoto() { profileManager.deleteProfilePhoto(); }
function exportProfile() { profileManager.exportProfile(); }
function saveAll() { profileManager.saveAll(); }
</script>
@endpush
