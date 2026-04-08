@extends('layouts.app')

@section('title', 'Settings - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Settings</h1>
                <p class="text-gray-600 mt-2">Manage your account preferences and system configuration</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                    <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                    Reset to Default
                </button>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                    Save All Changes
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Settings Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                <div class="p-4 bg-gradient-to-r from-indigo-500 to-indigo-600">
                    <h3 class="text-white font-semibold">Settings Menu</h3>
                </div>
                <nav class="p-2">
                    <a href="#general" class="flex items-center px-4 py-3 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg mb-1">
                        <i data-feather="settings" class="w-4 h-4 mr-3"></i>
                        General
                    </a>
                    <a href="#notifications" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="bell" class="w-4 h-4 mr-3"></i>
                        Notifications
                    </a>
                    <a href="#privacy" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="lock" class="w-4 h-4 mr-3"></i>
                        Privacy
                    </a>
                    <a href="#appearance" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="palette" class="w-4 h-4 mr-3"></i>
                        Appearance
                    </a>
                    <a href="#language" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="globe" class="w-4 h-4 mr-3"></i>
                        Language & Region
                    </a>
                    <a href="#security" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="shield" class="w-4 h-4 mr-3"></i>
                        Security
                    </a>
                    <a href="#data" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="database" class="w-4 h-4 mr-3"></i>
                        Data & Storage
                    </a>
                    <a href="#integrations" class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-lg mb-1">
                        <i data-feather="link" class="w-4 h-4 mr-3"></i>
                        Integrations
                    </a>
                </nav>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- General Settings -->
            <div id="general" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">General Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Basic account configuration and preferences</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name</label>
                            <input type="text" name="display_name" value="{{ $user->first_name }} {{ $user->last_name }}" class="form-input" data-setting-field="general">
                            <p class="text-xs text-gray-500 mt-1">This name will be displayed throughout the system</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                            <select name="language" class="form-select" data-setting-field="general">
                                <option value="en" {{ $settings->language === 'en' ? 'selected' : '' }}>English</option>
                                <option value="sw" {{ $settings->language === 'sw' ? 'selected' : '' }}>Swahili</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                            <select name="timezone" class="form-select" data-setting-field="general">
                                <option value="Africa/Dar_es_Salaam" {{ $settings->timezone === 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>East Africa Time (EAT) - UTC+3</option>
                                <option value="Africa/Lagos" {{ $settings->timezone === 'Africa/Lagos' ? 'selected' : '' }}>Central Africa Time (CAT) - UTC+2</option>
                                <option value="Africa/Abidjan" {{ $settings->timezone === 'Africa/Abidjan' ? 'selected' : '' }}>West Africa Time (WAT) - UTC+1</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                            <select class="form-select">
                                <option selected>DD/MM/YYYY</option>
                                <option>MM/DD/YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select class="form-select">
                                <option selected>Tanzanian Shilling (TZS)</option>
                                <option>US Dollar (USD)</option>
                                <option>Euro (EUR)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Settings -->
            <div id="notifications" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Notification Preferences</h3>
                    <p class="text-sm text-gray-600 mt-1">Control how and when you receive notifications</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="mail" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Email Notifications
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Task Assignments</p>
                                        <p class="text-xs text-gray-500">Get notified when tasks are assigned to you</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">System Updates</p>
                                        <p class="text-xs text-gray-500">Receive updates about system changes</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Security Alerts</p>
                                        <p class="text-xs text-gray-500">Important security notifications</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Weekly Reports</p>
                                        <p class="text-xs text-gray-500">Summary of weekly activities</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="bell" class="w-4 h-4 mr-2 text-indigo-600"></i>
                                In-App Notifications
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Real-time Notifications</p>
                                        <p class="text-xs text-gray-500">Show notifications in real-time</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Sound Effects</p>
                                        <p class="text-xs text-gray-500">Play sound for new notifications</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Desktop Notifications</p>
                                        <p class="text-xs text-gray-500">Show desktop notifications</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy Settings -->
            <div id="privacy" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Privacy Settings</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage your privacy and data sharing preferences</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="eye" class="w-4 h-4 mr-2 text-purple-600"></i>
                                Profile Visibility
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Show Profile to Others</p>
                                        <p class="text-xs text-gray-500">Allow other users to see your profile</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Show Online Status</p>
                                        <p class="text-xs text-gray-500">Display when you are online</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Show Last Login</p>
                                        <p class="text-xs text-gray-500">Display your last login time</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="share-2" class="w-4 h-4 mr-2 text-pink-600"></i>
                                Data Sharing
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Share Analytics Data</p>
                                        <p class="text-xs text-gray-500">Help improve system with anonymous data</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Share Usage Statistics</p>
                                        <p class="text-xs text-gray-500">Share how you use the system</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div id="appearance" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Appearance</h3>
                    <p class="text-sm text-gray-600 mt-1">Customize the look and feel of your interface</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Theme</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 border-indigo-500 rounded-lg cursor-pointer bg-indigo-50">
                                    <input type="radio" name="theme" value="light" checked class="mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Light</p>
                                        <p class="text-xs text-gray-500">Default theme</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300">
                                    <input type="radio" name="theme" value="dark" class="mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dark</p>
                                        <p class="text-xs text-gray-500">Dark theme</p>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300">
                                    <input type="radio" name="theme" value="auto" class="mr-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Auto</p>
                                        <p class="text-xs text-gray-500">System default</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Font Size</label>
                            <select class="form-select">
                                <option selected>Medium</option>
                                <option>Small</option>
                                <option>Large</option>
                                <option>Extra Large</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Sidebar Position</label>
                            <select class="form-select">
                                <option selected>Left</option>
                                <option>Right</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Language & Region Settings -->
            <div id="language" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Language & Region</h3>
                    <p class="text-sm text-gray-600 mt-1">Configure regional preferences and language settings</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Language</label>
                            <select class="form-select">
                                <option selected>English</option>
                                <option>Swahili</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                            <select name="date_format" class="form-select" data-setting-field="general">
                                <option value="d/m/Y" {{ $settings->date_format === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ $settings->date_format === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="Y-m-d" {{ $settings->date_format === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Format</label>
                            <select name="time_format" class="form-select" data-setting-field="general">
                                <option value="24" {{ $settings->time_format === '24' ? 'selected' : '' }}>24-hour</option>
                                <option value="12" {{ $settings->time_format === '12' ? 'selected' : '' }}>12-hour</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                            <select name="currency" class="form-select" data-setting-field="general">
                                <option value="TZS" {{ $settings->currency === 'TZS' ? 'selected' : '' }}>TZS - Tanzanian Shilling</option>
                                <option value="USD" {{ $settings->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ $settings->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Day of Week</label>
                            <select class="form-select">
                                <option selected>Monday</option>
                                <option>Sunday</option>
                                <option>Saturday</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Measurement System</label>
                            <select class="form-select">
                                <option selected>Metric</option>
                                <option>Imperial</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div id="security" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-orange-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage your account security and authentication</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="clock" class="w-4 h-4 mr-2 text-red-600"></i>
                                Session Management
                            </h4>
                            <div class="space-y-4">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Auto-logout</p>
                                        <p class="text-xs text-gray-500">Automatically logout after inactivity</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Session Timeout</label>
                                <select class="form-select">
                                    <option selected>30 minutes</option>
                                    <option>1 hour</option>
                                    <option>2 hours</option>
                                    <option>4 hours</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="shield" class="w-4 h-4 mr-2 text-orange-600"></i>
                                Login Security
                            </h4>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Two-Factor Authentication</p>
                                        <p class="text-xs text-gray-500">Add an extra layer of security</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox">
                                </label>
                                <label class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Login Notifications</p>
                                        <p class="text-xs text-gray-500">Get notified of new login attempts</p>
                                    </div>
                                    <input type="checkbox" class="form-checkbox" checked>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data & Storage Settings -->
            <div id="data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-cyan-50 to-blue-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Data & Storage</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage your data storage and export options</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="hard-drive" class="w-4 h-4 mr-2 text-cyan-600"></i>
                                Storage Usage
                            </h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Used Space</span>
                                    <span class="text-sm font-medium text-gray-900">2.3 GB / 5 GB</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 46%"></div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    <p>Documents: 1.2 GB</p>
                                    <p>Media: 0.8 GB</p>
                                    <p>Other: 0.3 GB</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="download" class="w-4 h-4 mr-2 text-blue-600"></i>
                                Data Management
                            </h4>
                            <div class="space-y-3">
                                <button class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-left flex items-center">
                                    <i data-feather="download" class="w-4 h-4 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Download My Data</p>
                                        <p class="text-xs text-gray-500">Export all your data</p>
                                    </div>
                                </button>
                                <button class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-left flex items-center">
                                    <i data-feather="trash-2" class="w-4 h-4 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Clear Cache</p>
                                        <p class="text-xs text-gray-500">Free up temporary space</p>
                                    </div>
                                </button>
                                <button class="w-full px-4 py-3 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors text-left flex items-center">
                                    <i data-feather="alert-triangle" class="w-4 h-4 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-medium text-red-600">Delete Account</p>
                                        <p class="text-xs text-red-500">Permanently remove account</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integrations Settings -->
            <div id="integrations" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Integrations</h3>
                    <p class="text-sm text-gray-600 mt-1">Connect with third-party services and applications</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="link" class="w-4 h-4 mr-2 text-indigo-600"></i>
                                Connected Services
                            </h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <i data-feather="mail" class="w-5 h-5 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Email Integration</p>
                                            <p class="text-sm text-gray-600">Connected to john.doe@legalhr.co.tz</p>
                                        </div>
                                    </div>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Disconnect</button>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                            <i data-feather="calendar" class="w-5 h-5 text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Calendar Sync</p>
                                            <p class="text-sm text-gray-600">Sync with Google Calendar</p>
                                        </div>
                                    </div>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Disconnect</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 mb-4 flex items-center">
                                <i data-feather="plus-circle" class="w-4 h-4 mr-2 text-purple-600"></i>
                                Available Integrations
                            </h4>
                            <div class="space-y-3">
                                <button class="w-full px-4 py-3 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors text-left flex items-center">
                                    <i data-feather="slack" class="w-4 h-4 mr-3 text-indigo-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Slack</p>
                                        <p class="text-xs text-gray-500">Connect to Slack workspace</p>
                                    </div>
                                </button>
                                <button class="w-full px-4 py-3 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors text-left flex items-center">
                                    <i data-feather="github" class="w-4 h-4 mr-3 text-indigo-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">GitHub</p>
                                        <p class="text-xs text-gray-500">Connect to GitHub repositories</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Update active navigation
window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('[id^="#"]');
    const navLinks = document.querySelectorAll('nav a[href^="#"]');
    
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.clientHeight;
        if (pageYOffset >= sectionTop - 100) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('text-indigo-600', 'bg-indigo-50');
        link.classList.add('text-gray-700');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.remove('text-gray-700');
            link.classList.add('text-indigo-600', 'bg-indigo-50');
        }
    });
});
</script>

@push('scripts')
<script>
// Settings Management System
class SettingsManager {
    constructor() {
        this.settings = {};
        this.originalSettings = {};
        this.autoSaveTimer = null;
        this.init();
    }

    init() {
        this.loadSettings();
        this.setupEventListeners();
        this.setupAutoSave();
        this.initializeFeather();
    }

    setupEventListeners() {
        // Save all changes button
        const saveBtn = document.querySelector('button:has(.feather-save)');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveAllSettings());
        }

        // Reset to default button
        const resetBtn = document.querySelector('button:has(.feather-refresh-cw)');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetToDefault());
        }

        // Settings field changes
        document.querySelectorAll('[data-setting-field]').forEach(field => {
            field.addEventListener('input', () => this.markAsChanged(field));
            field.addEventListener('change', () => this.markAsChanged(field));
        });

        // Section-specific save buttons
        this.setupSectionSaveButtons();
    }

    setupSectionSaveButtons() {
        const sections = ['general', 'notifications', 'privacy', 'appearance', 'security', 'data', 'integrations'];
        
        sections.forEach(section => {
            const saveBtn = document.querySelector(`#${section} button:has-text("Save")`);
            if (saveBtn) {
                saveBtn.addEventListener('click', () => this.saveSection(section));
            }
        });
    }

    setupAutoSave() {
        document.querySelectorAll('[data-setting-field]').forEach(field => {
            field.addEventListener('input', () => {
                clearTimeout(this.autoSaveTimer);
                this.autoSaveTimer = setTimeout(() => this.autoSave(), 3000);
            });
        });
    }

    async loadSettings() {
        try {
            const response = await fetch('/settings/data', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();
            if (result.success) {
                this.settings = result.settings;
                this.originalSettings = JSON.parse(JSON.stringify(result.settings));
                this.populateFormFields();
            }
        } catch (error) {
            console.error('Failed to load settings:', error);
        }
    }

    populateFormFields() {
        // General settings
        this.populateField('display_name', this.settings.display_name);
        this.populateField('language', this.settings.language);
        this.populateField('timezone', this.settings.timezone);
        this.populateField('date_format', this.settings.date_format);
        this.populateField('time_format', this.settings.time_format);
        this.populateField('currency', this.settings.currency);

        // Notification settings
        this.populateCheckbox('notification_email', this.settings.notification_email);
        this.populateCheckbox('notification_push', this.settings.notification_push);
        this.populateCheckbox('notification_sms', this.settings.notification_sms);

        // Privacy settings
        this.populateField('privacy_profile_visibility', this.settings.privacy_profile_visibility);
        this.populateField('privacy_activity_visibility', this.settings.privacy_activity_visibility);

        // Appearance settings
        this.populateField('theme', this.settings.theme);
        this.populateField('dashboard_layout', this.settings.dashboard_layout);

        // Security settings
        this.populateCheckbox('two_factor_enabled', this.settings.two_factor_enabled);
        this.populateField('session_timeout', this.settings.session_timeout);
        this.populateCheckbox('auto_logout', this.settings.auto_logout);

        // Data settings
        this.populateField('data_export_format', this.settings.data_export_format);

        // Preferences (JSON field)
        if (this.settings.preferences) {
            Object.keys(this.settings.preferences).forEach(key => {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = this.settings.preferences[key];
                    } else {
                        field.value = this.settings.preferences[key];
                    }
                }
            });
        }
    }

    populateField(fieldName, value) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && value !== undefined) {
            field.value = value;
        }
    }

    populateCheckbox(fieldName, value) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field && value !== undefined) {
            field.checked = value;
        }
    }

    async saveAllSettings() {
        const allSettings = this.collectAllSettings();
        
        try {
            this.showLoading('Saving all settings...');
            
            // Save each section
            await this.saveSection('general');
            await this.saveSection('notifications');
            await this.saveSection('privacy');
            await this.saveSection('appearance');
            await this.saveSection('security');
            await this.saveSection('data');
            await this.saveSection('integrations');
            
            this.showNotification('All settings saved successfully!', 'success');
            this.clearChangedFields();
        } catch (error) {
            console.error('Save settings error:', error);
            this.showNotification('Failed to save settings', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async saveSection(section) {
        const sectionData = this.collectSectionData(section);
        
        const response = await fetch(`/settings/${section}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(sectionData)
        });

        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.message || 'Failed to save section');
        }

        return result;
    }

    collectSectionData(section) {
        const sectionFields = document.querySelectorAll(`#${section} [data-setting-field="${section}"]`);
        const data = {};
        
        sectionFields.forEach(field => {
            if (field.type === 'checkbox') {
                data[field.name] = field.checked;
            } else if (field.type === 'radio') {
                if (field.checked) {
                    data[field.name] = field.value;
                }
            } else {
                data[field.name] = field.value;
            }
        });

        // Collect preferences for this section
        const preferenceFields = document.querySelectorAll(`#${section} [data-preference]`);
        if (preferenceFields.length > 0) {
            data.preferences = {};
            preferenceFields.forEach(field => {
                if (field.type === 'checkbox') {
                    data.preferences[field.name] = field.checked;
                } else {
                    data.preferences[field.name] = field.value;
                }
            });
        }

        return data;
    }

    collectAllSettings() {
        const allFields = document.querySelectorAll('[data-setting-field]');
        const data = {};
        
        allFields.forEach(field => {
            if (field.type === 'checkbox') {
                data[field.name] = field.checked;
            } else if (field.type === 'radio') {
                if (field.checked) {
                    data[field.name] = field.value;
                }
            } else {
                data[field.name] = field.value;
            }
        });

        return data;
    }

    async resetToDefault() {
        if (!confirm('Are you sure you want to reset all settings to their default values? This action cannot be undone.')) {
            return;
        }

        try {
            this.showLoading('Resetting to default...');
            
            const response = await fetch('/settings/reset', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Settings reset to default successfully!', 'success');
                await this.loadSettings(); // Reload settings
                this.clearChangedFields();
            } else {
                this.showNotification(result.message || 'Failed to reset settings', 'error');
            }
        } catch (error) {
            console.error('Reset settings error:', error);
            this.showNotification('An error occurred while resetting settings', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async exportSettings() {
        try {
            this.showLoading('Exporting settings...');
            
            const response = await fetch('/settings/export', {
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
                
                const exportFileDefaultName = `settings-export-${new Date().toISOString().split('T')[0]}.json`;
                
                const linkElement = document.createElement('a');
                linkElement.setAttribute('href', dataUri);
                linkElement.setAttribute('download', exportFileDefaultName);
                linkElement.click();
                
                this.showNotification('Settings exported successfully!', 'success');
            } else {
                this.showNotification(result.message || 'Failed to export settings', 'error');
            }
        } catch (error) {
            console.error('Export settings error:', error);
            this.showNotification('An error occurred while exporting settings', 'error');
        } finally {
            this.hideLoading();
        }
    }

    markAsChanged(field) {
        field.classList.add('border-yellow-400');
        field.classList.add('ring-2', 'ring-yellow-200');
        
        // Show save indicator
        this.showSaveIndicator();
    }

    clearChangedFields() {
        document.querySelectorAll('[data-setting-field]').forEach(field => {
            field.classList.remove('border-yellow-400', 'ring-2', 'ring-yellow-200');
        });
        
        this.hideSaveIndicator();
    }

    showSaveIndicator() {
        let indicator = document.getElementById('save-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'save-indicator';
            indicator.className = 'fixed bottom-4 right-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            indicator.innerHTML = '<i data-feather="alert-triangle" class="w-4 h-4 inline mr-2"></i>Unsaved changes';
            document.body.appendChild(indicator);
            feather.replace();
        }
    }

    hideSaveIndicator() {
        const indicator = document.getElementById('save-indicator');
        if (indicator) {
            indicator.remove();
        }
    }

    async autoSave() {
        const changedFields = document.querySelectorAll('[data-setting-field].border-yellow-400');
        if (changedFields.length === 0) return;

        try {
            // Collect data from changed fields
            const data = {};
            changedFields.forEach(field => {
                if (field.type === 'checkbox') {
                    data[field.name] = field.checked;
                } else {
                    data[field.name] = field.value;
                }
            });

            // Determine section from first changed field
            const section = changedFields[0].closest('[id^="general"], [id^="notifications"], [id^="privacy"], [id^="appearance"], [id^="security"], [id^="data"], [id^="integrations"]').id;
            
            if (section) {
                const sectionName = section.replace('-', '');
                await this.saveSection(sectionName);
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
            btn.innerHTML = '<i data-feather="save" class="w-4 h-4 mr-2"></i> Save All Changes';
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

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    window.settingsManager = new SettingsManager();
});
</script>
@endpush

@endsection
