@extends('layouts.app')

@section('title', 'Settings - LegalHR Tanzania')

@section('content')
<div class="max-w-7xl mx-auto p-6">
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
                            <input type="text" value="John Doe" class="form-input">
                            <p class="text-xs text-gray-500 mt-1">This name will be displayed throughout the system</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Default Language</label>
                            <select class="form-select">
                                <option selected>English</option>
                                <option>Swahili</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Zone</label>
                            <select class="form-select">
                                <option selected>East Africa Time (EAT) - UTC+3</option>
                                <option>Central Africa Time (CAT) - UTC+2</option>
                                <option>West Africa Time (WAT) - UTC+1</option>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number Format</label>
                            <select class="form-select">
                                <option selected>1,234,567.89</option>
                                <option>1.234.567,89</option>
                                <option>1 234 567.89</option>
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
@endpush
@endsection
