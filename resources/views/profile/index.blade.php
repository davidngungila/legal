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
                    <div class="w-24 h-24 bg-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 relative group">
                        @if($user->profile_photo)
                            <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->first_name }} {{ $user->last_name }}" class="w-full h-full rounded-full object-cover">
                        @else
                            <span class="text-white text-3xl font-bold">{{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}</span>
                        @endif
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('photo-upload').click()">
                            <i data-feather="camera" class="w-6 h-6 text-white"></i>
                        </div>
                        <input type="file" id="photo-upload" class="hidden" accept="image/*" onchange="updateProfilePhoto(this)">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $user->first_name }} {{ $user->last_name }}</h3>
                    <p class="text-gray-600 mb-4">{{ $user->job_title ?? 'Employee' }}</p>
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
                    <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i data-feather="camera" class="w-4 h-4 inline mr-2"></i>
                        Change Photo
                    </button>
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

            <!-- Security Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Security Settings</h3>
                <div class="space-y-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Change Password</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" class="form-input">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" class="form-input">
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Two-Factor Authentication</h4>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Enable 2FA</p>
                                <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                            </div>
                            <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                Enable
                            </button>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-4">Login Preferences</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3" checked>
                                <span class="text-sm text-gray-700">Remember me on this device</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox mr-3">
                                <span class="text-sm text-gray-700">Email notifications for login attempts</span>
                            </label>
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
            saveBtn.addEventListener('click', () => this.saveProfile());
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

    setupSecuritySettings() {
        // 2FA toggle
        const twoFaBtn = document.querySelector('button:has-text("Enable 2FA")');
        if (twoFaBtn) {
            twoFaBtn.addEventListener('click', () => this.toggle2FA());
        }

        // Password change
        const changePasswordBtn = document.querySelector('button:has-text("Change Password")');
        if (changePasswordBtn) {
            changePasswordBtn.addEventListener('click', () => this.showPasswordChangeModal());
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

    async updateProfilePhoto(input) {
        if (!input.files || !input.files[0]) return;

        const formData = new FormData();
        formData.append('photo', input.files[0]);

        try {
            this.showLoading('Updating photo...');
            
            const response = await fetch('/profile/photo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Profile photo updated successfully!', 'success');
                // Update the photo display
                this.updatePhotoDisplay(result.photo_url);
            } else {
                this.showNotification(result.message || 'Failed to update photo', 'error');
            }
        } catch (error) {
            console.error('Photo update error:', error);
            this.showNotification('An error occurred while updating photo', 'error');
        } finally {
            this.hideLoading();
            input.value = ''; // Clear the file input
        }
    }

    updatePhotoDisplay(photoUrl) {
        const photoContainer = document.querySelector('.w-24.h-24');
        if (photoContainer && photoUrl) {
            photoContainer.innerHTML = `
                <img src="${photoUrl}" alt="Profile Photo" class="w-full h-full rounded-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer" onclick="document.getElementById('photo-upload').click()">
                    <i data-feather="camera" class="w-6 h-6 text-white"></i>
                </div>
                <input type="file" id="photo-upload" class="hidden" accept="image/*" onchange="profileManager.updateProfilePhoto(this)">
            `;
            feather.replace();
        }
    }

    async deleteProfilePhoto() {
        if (!confirm('Are you sure you want to delete your profile photo?')) return;

        try {
            this.showLoading('Deleting photo...');
            
            const response = await fetch('/profile/photo', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Profile photo deleted successfully!', 'success');
                // Update the photo display to show initials
                this.updatePhotoDisplay(null);
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                // Create and download JSON file
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

    showPasswordChangeModal() {
        // Create password change modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Change Password</h3>
                <form id="password-change-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                            <input type="password" name="current_password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Handle form submission
        modal.querySelector('#password-change-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.changePassword(new FormData(e.target));
        });
    }

    async changePassword(formData) {
        try {
            this.showLoading('Changing password...');
            
            const response = await fetch('/profile/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Password changed successfully!', 'success');
                document.querySelector('.fixed').remove(); // Close modal
            } else {
                this.showNotification(result.message || 'Failed to change password', 'error');
            }
        } catch (error) {
            console.error('Password change error:', error);
            this.showNotification('An error occurred while changing password', 'error');
        } finally {
            this.hideLoading();
        }
    }

    markAsChanged(field) {
        field.classList.add('border-yellow-400');
        field.classList.add('ring-2', 'ring-yellow-200');
    }

    clearChangedFields() {
        document.querySelectorAll('[data-profile-field]').forEach(field => {
            field.classList.remove('border-yellow-400', 'ring-2', 'ring-yellow-200');
        });
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

    showLoading(message) {
        const btn = document.querySelector('button:has(.feather-save)');
        if (btn) {
            btn.innerHTML = `<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> ${message}`;
            btn.disabled = true;
            feather.replace();
        }
    }

    hideLoading() {
        const btn = document.querySelector('button:has(.feather-loader)');
        if (btn) {
            btn.innerHTML = '<i data-feather="save" class="w-4 h-4 mr-2"></i> Save Changes';
            btn.disabled = false;
            feather.replace();
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;
        
        const styles = {
            success: 'bg-gradient-to-r from-green-500 to-emerald-600 text-white',
            error: 'bg-gradient-to-r from-red-500 to-pink-600 text-white',
            warning: 'bg-gradient-to-r from-yellow-500 to-orange-600 text-white',
            info: 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'
        };
        
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info'
        };
        
        notification.className += ' ' + styles[type] || styles.info;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <i data-feather="${icons[type] || 'info'}" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold">${message}</p>
                    <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 4000);
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Global functions for onclick handlers
let profileManager;

function updateProfilePhoto(input) {
    profileManager.updateProfilePhoto(input);
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    profileManager = new ProfileManager();
});
</script>
@endpush
