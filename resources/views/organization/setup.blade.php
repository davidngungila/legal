@extends('layouts.app')

@section('title', 'Organization Setup - LegalHR Tanzania')

@push('styles')
<style>
    /* Custom animations and transitions */
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    @keyframes shimmer {
        0% { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    
    .slide-in-right {
        animation: slideInRight 0.5s ease-out;
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 1000px 100%;
        animation: shimmer 2s infinite;
    }
    
    .step-indicator {
        transition: all 0.3s ease;
    }
    
    .step-indicator.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transform: scale(1.1);
    }
    
    .step-indicator.completed {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
    }
    
    .form-section {
        transition: all 0.5s ease;
        transform-origin: top left;
    }
    
    .form-section.hidden {
        opacity: 0;
        transform: translateX(-20px);
        display: none;
    }
    
    .form-section.active {
        opacity: 1;
        transform: translateX(0);
        display: block;
    }
    
    .upload-area {
        border: 2px dashed #cbd5e0;
        transition: all 0.3s ease;
    }
    
    .upload-area:hover {
        border-color: #667eea;
        background: #f7fafc;
    }
    
    .upload-area.dragover {
        border-color: #667eea;
        background: #edf2f7;
        transform: scale(1.02);
    }
    
    .progress-ring {
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    
    .floating-label {
        transition: all 0.2s ease;
    }
    
    .input-group:focus-within .floating-label,
    .input-group input:not(:placeholder-shown) ~ .floating-label,
    .input-group textarea:not(:placeholder-shown) ~ .floating-label,
    .input-group select:focus ~ .floating-label {
        transform: translateY(-1.5rem) scale(0.85);
        color: #667eea;
    }
    
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }
    
    .gradient-border {
        position: relative;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #667eea 0%, #764ba2 100%) border-box;
        border: 3px solid transparent;
    }
    
    .tooltip {
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .tooltip-trigger:hover .tooltip {
        visibility: visible;
        opacity: 1;
    }
    
    .auto-save-indicator {
        transition: all 0.3s ease;
    }
    
    .auto-save-indicator.saving {
        color: #f59e0b;
    }
    
    .auto-save-indicator.saved {
        color: #10b981;
    }
    
    .validation-error {
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .feature-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .feature-card.selected {
        border-color: #667eea;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Advanced Header with Progress -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-4 lg:mb-0">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                            <i data-feather="building" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Organization Setup</h1>
                            <p class="text-gray-600 mt-1">Complete your organization profile in 4 simple steps</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Indicator -->
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-2">
                        <div class="flex items-center">
                            <div id="progress-circle" class="relative">
                                <svg class="w-16 h-16">
                                    <circle cx="32" cy="32" r="28" stroke="#e5e7eb" stroke-width="4" fill="none"></circle>
                                    <circle id="progress-ring" class="progress-ring" cx="32" cy="32" r="28" stroke="#667eea" stroke-width="4" fill="none" stroke-dasharray="176" stroke-dashoffset="176"></circle>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span id="progress-percentage" class="text-sm font-bold text-gray-700">0%</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Setup Progress</p>
                                <p id="progress-text" class="text-xs text-gray-500">Just getting started</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button id="auto-save-indicator" class="auto-save-indicator px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            <i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i>
                            Auto-saved
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Step Navigation -->
    <div class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-2 md:space-x-4 overflow-x-auto">
                    <button onclick="goToStep(1)" class="step-btn flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50" data-step="1">
                        <div class="step-indicator w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                            1
                        </div>
                        <span class="hidden md:inline text-sm font-medium text-gray-700">Basic Info</span>
                    </button>
                    
                    <div class="hidden md:block w-8 h-0.5 bg-gray-300"></div>
                    <button onclick="goToStep(2)" class="step-btn flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50" data-step="2">
                        <div class="step-indicator w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                            2
                        </div>
                        <span class="hidden md:inline text-sm font-medium text-gray-700">Legal & Compliance</span>
                    </button>
                    
                    <div class="hidden md:block w-8 h-0.5 bg-gray-300"></div>
                    <button onclick="goToStep(3)" class="step-btn flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50" data-step="3">
                        <div class="step-indicator w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                            3
                        </div>
                        <span class="hidden md:inline text-sm font-medium text-gray-700">Banking</span>
                    </button>
                    
                    <div class="hidden md:block w-8 h-0.5 bg-gray-300"></div>
                    <button onclick="goToStep(4)" class="step-btn flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-200 hover:bg-gray-50" data-step="4">
                        <div class="step-indicator w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                            4
                        </div>
                        <span class="hidden md:inline text-sm font-medium text-gray-700">Documents</span>
                    </button>
                </div>
                
                <div class="flex items-center space-x-2">
                    <button onclick="saveDraft()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center text-sm">
                        <i data-feather="save" class="w-4 h-4 mr-2"></i>
                        Save Draft
                    </button>
                    <button onclick="previewOrganization()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center text-sm">
                        <i data-feather="eye" class="w-4 h-4 mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form id="organizationForm" class="space-y-8">
            
            <!-- Step 1: Basic Information -->
            <div id="step-1" class="form-section active">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-white/20 backdrop-blur rounded-xl">
                                    <i data-feather="building" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Basic Information</h2>
                                    <p class="text-white/80 mt-1">Tell us about your organization</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-2 text-white/60">
                                <i data-feather="info" class="w-4 h-4"></i>
                                <span class="text-sm">Step 1 of 4</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Company Details -->
                            <div class="space-y-6">
                                <div class="input-group relative">
                                    <input type="text" id="company-name" name="company_name" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer" required>
                                    <label for="company-name" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 peer-valid:-top-6 peer-valid:text-sm peer-valid:text-green-500 transition-all duration-200">
                                        Company Name *
                                    </label>
                                    <div class="validation-message text-xs mt-1 text-red-500 hidden">Company name is required</div>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="registration-number" name="registration_number" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer">
                                    <label for="registration-number" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 transition-all duration-200">
                                        Registration Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="tin-number" name="tin_number" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer">
                                    <label for="tin-number" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 transition-all duration-200">
                                        TIN Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <select id="industry" name="industry" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer">
                                        <option value="">Select Industry</option>
                                        <option value="manufacturing">Manufacturing</option>
                                        <option value="services">Services</option>
                                        <option value="construction">Construction</option>
                                        <option value="retail">Retail</option>
                                        <option value="technology">Technology</option>
                                        <option value="healthcare">Healthcare</option>
                                        <option value="education">Education</option>
                                        <option value="government">Government</option>
                                        <option value="agriculture">Agriculture</option>
                                        <option value="tourism">Tourism</option>
                                    </select>
                                    <label for="industry" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 transition-all duration-200">
                                        Industry Sector *
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Contact Information -->
                            <div class="space-y-6">
                                <div class="input-group relative">
                                    <input type="tel" id="phone" name="phone" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer" required>
                                    <label for="phone" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 peer-valid:-top-6 peer-valid:text-sm peer-valid:text-green-500 transition-all duration-200">
                                        Phone Number *
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="email" id="email" name="email" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer" required>
                                    <label for="email" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 peer-valid:-top-6 peer-valid:text-sm peer-valid:text-green-500 transition-all duration-200">
                                        Email Address *
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="url" id="website" name="website" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer">
                                    <label for="website" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 transition-all duration-200">
                                        Website
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <textarea id="address" name="address" placeholder=" " rows="3" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 peer resize-none"></textarea>
                                    <label for="address" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-indigo-500 transition-all duration-200">
                                        Company Address
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Features -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Organization Features</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="feature-card p-4 bg-white rounded-lg border-2 border-gray-200 text-center" onclick="toggleFeature(this, 'multiple_locations')">
                                    <i data-feather="map-pin" class="w-8 h-8 mx-auto mb-2 text-indigo-600"></i>
                                    <h4 class="font-medium text-gray-900">Multiple Locations</h4>
                                    <p class="text-xs text-gray-600 mt-1">Manage multiple office locations</p>
                                </div>
                                
                                <div class="feature-card p-4 bg-white rounded-lg border-2 border-gray-200 text-center" onclick="toggleFeature(this, 'remote_work')">
                                    <i data-feather="home" class="w-8 h-8 mx-auto mb-2 text-indigo-600"></i>
                                    <h4 class="font-medium text-gray-900">Remote Work</h4>
                                    <p class="text-xs text-gray-600 mt-1">Support remote workforce</p>
                                </div>
                                
                                <div class="feature-card p-4 bg-white rounded-lg border-2 border-gray-200 text-center" onclick="toggleFeature(this, 'shift_work')">
                                    <i data-feather="clock" class="w-8 h-8 mx-auto mb-2 text-indigo-600"></i>
                                    <h4 class="font-medium text-gray-900">Shift Work</h4>
                                    <p class="text-xs text-gray-600 mt-1">Manage shift-based schedules</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="flex justify-end mt-8">
                            <button type="button" onclick="nextStep()" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 flex items-center shadow-lg">
                                Next Step
                                <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Legal & Compliance -->
            <div id="step-2" class="form-section hidden">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-500 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-white/20 backdrop-blur rounded-xl">
                                    <i data-feather="shield" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Legal & Compliance</h2>
                                    <p class="text-white/80 mt-1">Legal documents and compliance information</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-2 text-white/60">
                                <i data-feather="info" class="w-4 h-4"></i>
                                <span class="text-sm">Step 2 of 4</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Regulatory Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Regulatory Compliance</h3>
                                
                                <div class="input-group relative">
                                    <input type="text" id="business-license" name="business_license" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="business-license" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        Business License Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="date" id="license-expiry" name="license_expiry" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <label for="license-expiry" class="block text-sm font-medium text-gray-700 mb-2">License Expiry Date</label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="vat-registration" name="vat_registration" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="vat-registration" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        VAT Registration Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="trade-license" name="trade_license" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="trade-license" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        Trade License Number
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Statutory Compliance -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statutory Compliance</h3>
                                
                                <div class="input-group relative">
                                    <input type="text" id="nssf-employer" name="nssf_employer" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="nssf-employer" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        NSSF Employer Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="wcf-policy" name="wcf_policy" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="wcf-policy" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        WCF Policy Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="heslb-registration" name="heslb_registration" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="heslb-registration" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        HESLB Registration Number
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="workmen-comp" name="workmen_comp" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 peer">
                                    <label for="workmen-comp" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-green-500 transition-all duration-200">
                                        Workmen's Compensation Policy
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Compliance Status -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Compliance Status</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">PAYE Registration</span>
                                    <div class="tooltip-trigger relative">
                                        <span class="w-3 h-3 bg-yellow-400 rounded-full"></span>
                                        <div class="tooltip absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg z-10">
                                            Registration pending verification
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">VAT Compliance</span>
                                    <div class="tooltip-trigger relative">
                                        <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                        <div class="tooltip absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg z-10">
                                            Fully compliant
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">NSSF Compliance</span>
                                    <div class="tooltip-trigger relative">
                                        <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                        <div class="tooltip absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg z-10">
                                            Up to date
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="text-sm font-medium text-gray-700">WCF Compliance</span>
                                    <div class="tooltip-trigger relative">
                                        <span class="w-3 h-3 bg-green-400 rounded-full"></span>
                                        <div class="tooltip absolute right-0 top-6 w-48 p-2 bg-gray-800 text-white text-xs rounded shadow-lg z-10">
                                            Policy active
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button type="button" onclick="previousStep()" class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center">
                                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep()" class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 flex items-center shadow-lg">
                                Next Step
                                <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 3: Banking Information -->
            <div id="step-3" class="form-section hidden">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 bg-gradient-to-r from-blue-500 via-cyan-500 to-teal-500 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-white/20 backdrop-blur rounded-xl">
                                    <i data-feather="credit-card" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Banking Information</h2>
                                    <p class="text-white/80 mt-1">Bank account details for payroll and transactions</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-2 text-white/60">
                                <i data-feather="info" class="w-4 h-4"></i>
                                <span class="text-sm">Step 3 of 4</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Primary Bank Account -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Primary Bank Account</h3>
                                
                                <div class="input-group relative">
                                    <input type="text" id="bank-name" name="bank_name" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                    <label for="bank-name" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Bank Name *
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="account-number" name="account_number" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                    <label for="account-number" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Account Number *
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="branch-name" name="branch_name" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                    <label for="branch-name" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Branch Name
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="text" id="swift-code" name="swift_code" placeholder=" " class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                    <label for="swift-code" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Swift Code
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Payroll Configuration -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payroll Configuration</h3>
                                
                                <div class="input-group relative">
                                    <select id="payroll-frequency" name="payroll_frequency" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                        <option value="">Select Frequency</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="bi-monthly">Bi-Monthly</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="bi-weekly">Bi-Weekly</option>
                                    </select>
                                    <label for="payroll-frequency" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Payroll Frequency *
                                    </label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="date" id="payroll-date" name="payroll_date" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <label for="payroll-date" class="block text-sm font-medium text-gray-700 mb-2">Next Payroll Date</label>
                                </div>
                                
                                <div class="input-group relative">
                                    <input type="number" id="processing-days" name="processing_days" placeholder=" " min="1" max="30" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 peer">
                                    <label for="processing-days" class="floating-label absolute left-4 top-3 text-gray-600 pointer-events-none peer-placeholder-shown:top-3 peer-focus:-top-6 peer-focus:text-sm peer-focus:text-blue-500 transition-all duration-200">
                                        Processing Days (before payroll)
                                    </label>
                                </div>
                                
                                <div class="flex items-center space-x-3 p-4 bg-blue-50 rounded-lg">
                                    <input type="checkbox" id="auto-approval" name="auto_approval" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                                    <label for="auto-approval" class="text-sm font-medium text-gray-700">Enable automatic payroll approval</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Bank Account Preview -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Preview</h3>
                            <div class="bg-white rounded-lg p-4 border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-600">Account Holder</p>
                                        <p id="preview-account-holder" class="font-semibold text-gray-900">-</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Account Number</p>
                                        <p id="preview-account-number" class="font-semibold text-gray-900">-</p>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">Bank</p>
                                            <p id="preview-bank-name" class="font-semibold text-gray-900">-</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">Branch</p>
                                            <p id="preview-branch-name" class="font-semibold text-gray-900">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="flex justify-between mt-8">
                            <button type="button" onclick="previousStep()" class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center">
                                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                                Previous
                            </button>
                            <button type="button" onclick="nextStep()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all duration-200 flex items-center shadow-lg">
                                Next Step
                                <i data-feather="arrow-right" class="w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Step 4: Document Upload -->
            <div id="step-4" class="form-section hidden">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                    <div class="px-8 py-6 bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-white/20 backdrop-blur rounded-xl">
                                    <i data-feather="file-text" class="w-6 h-6 text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-white">Document Upload</h2>
                                    <p class="text-white/80 mt-1">Upload required documents and certificates</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center space-x-2 text-white/60">
                                <i data-feather="info" class="w-4 h-4"></i>
                                <span class="text-sm">Step 4 of 4</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Required Documents -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Documents</h3>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('business-license-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Business License</p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</p>
                                    <input type="file" id="business-license-file" name="business_license_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <div id="business-license-preview" class="mt-3 hidden">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded">
                                            <span class="text-sm text-green-700">File uploaded</span>
                                            <button type="button" onclick="removeFile('business-license')" class="text-red-500 hover:text-red-700">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('tin-certificate-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">TIN Certificate</p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</p>
                                    <input type="file" id="tin-certificate-file" name="tin_certificate_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <div id="tin-certificate-preview" class="mt-3 hidden">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded">
                                            <span class="text-sm text-green-700">File uploaded</span>
                                            <button type="button" onclick="removeFile('tin-certificate')" class="text-red-500 hover:text-red-700">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('nssf-certificate-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">NSSF Certificate</p>
                                    <p class="text-xs text-gray-500">PDF, JPG, PNG (Max 5MB)</p>
                                    <input type="file" id="nssf-certificate-file" name="nssf_certificate_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                                    <div id="nssf-certificate-preview" class="mt-3 hidden">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded">
                                            <span class="text-sm text-green-700">File uploaded</span>
                                            <button type="button" onclick="removeFile('nssf-certificate')" class="text-red-500 hover:text-red-700">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Optional Documents -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Optional Documents</h3>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('company-profile-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Company Profile</p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX (Max 10MB)</p>
                                    <input type="file" id="company-profile-file" name="company_profile_file" accept=".pdf,.doc,.docx" class="hidden">
                                    <div id="company-profile-preview" class="mt-3 hidden">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded">
                                            <span class="text-sm text-green-700">File uploaded</span>
                                            <button type="button" onclick="removeFile('company-profile')" class="text-red-500 hover:text-red-700">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('organizational-chart-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Organizational Chart</p>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG (Max 5MB)</p>
                                    <input type="file" id="organizational-chart-file" name="organizational_chart_file" accept=".pdf,.png,.jpg,.jpeg" class="hidden">
                                    <div id="organizational-chart-preview" class="mt-3 hidden">
                                        <div class="flex items-center justify-between bg-green-50 p-2 rounded">
                                            <span class="text-sm text-green-700">File uploaded</span>
                                            <button type="button" onclick="removeFile('organizational-chart')" class="text-red-500 hover:text-red-700">
                                                <i data-feather="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="upload-area p-6 rounded-xl text-center cursor-pointer" onclick="document.getElementById('other-documents-file').click()">
                                    <i data-feather="upload-cloud" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Other Documents</p>
                                    <p class="text-xs text-gray-500">Any format (Max 10MB)</p>
                                    <input type="file" id="other-documents-file" name="other_documents_file" multiple class="hidden">
                                    <div id="other-documents-preview" class="mt-3 hidden">
                                        <div class="space-y-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Document Summary -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Document Summary</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600" id="required-count">0</div>
                                    <p class="text-sm text-gray-600">Required Documents</p>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-pink-600" id="optional-count">0</div>
                                    <p class="text-sm text-gray-600">Optional Documents</p>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-rose-600" id="total-size">0 MB</div>
                                    <p class="text-sm text-gray-600">Total Size</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Final Actions -->
                        <div class="flex justify-between mt-8">
                            <button type="button" onclick="previousStep()" class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center">
                                <i data-feather="arrow-left" class="w-4 h-4 mr-2"></i>
                                Previous
                            </button>
                            <div class="flex space-x-3">
                                <button type="button" onclick="saveDraft()" class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-200 flex items-center">
                                    <i data-feather="save" class="w-4 h-4 mr-2"></i>
                                    Save Draft
                                </button>
                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-200 flex items-center shadow-lg">
                                    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                                    Complete Setup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Advanced Organization Setup System
class OrganizationSetup {
    constructor() {
        this.currentStep = 1;
        this.totalSteps = 4;
        this.formData = {};
        this.features = [];
        this.uploadedFiles = new Map();
        this.autoSaveTimer = null;
        this.validationRules = {
            company_name: { required: true, minLength: 2 },
            email: { required: true, email: true },
            phone: { required: true, phone: true },
            industry: { required: true }
        };
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupFileUpload();
        this.setupAutoSave();
        this.setupRealTimeValidation();
        this.updateProgress();
        this.loadSavedData();
        
        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
    
    setupEventListeners() {
        // Form submission
        document.getElementById('organizationForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.completeSetup();
        });
        
        // Input change listeners for auto-save
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', () => this.handleInputChange(input));
            input.addEventListener('blur', () => this.validateField(input));
        });
        
        // Banking preview updates
        const bankInputs = ['bank-name', 'account-number', 'branch-name'];
        bankInputs.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', () => this.updateBankPreview());
            }
        });
        
        // File upload drag and drop
        this.setupDragAndDrop();
    }
    
    setupFileUpload() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', (e) => this.handleFileUpload(e));
        });
    }
    
    setupDragAndDrop() {
        const uploadAreas = document.querySelectorAll('.upload-area');
        
        uploadAreas.forEach(area => {
            area.addEventListener('dragover', (e) => {
                e.preventDefault();
                area.classList.add('dragover');
            });
            
            area.addEventListener('dragleave', () => {
                area.classList.remove('dragover');
            });
            
            area.addEventListener('drop', (e) => {
                e.preventDefault();
                area.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const input = area.querySelector('input[type="file"]');
                    if (input) {
                        input.files = files;
                        this.handleFileUpload({ target: input });
                    }
                }
            });
        });
    }
    
    handleFileUpload(event) {
        const input = event.target;
        const file = input.files[0];
        
        if (!file) return;
        
        // Validate file size
        const maxSize = input.getAttribute('data-max-size') || 10 * 1024 * 1024; // 10MB default
        if (file.size > maxSize) {
            this.showNotification('File size exceeds maximum limit', 'error');
            input.value = '';
            return;
        }
        
        // Store file info
        const fileId = input.id.replace('-file', '');
        this.uploadedFiles.set(fileId, {
            file: file,
            name: file.name,
            size: file.size,
            type: file.type
        });
        
        // Update preview
        this.updateFilePreview(fileId, file);
        this.updateDocumentSummary();
        
        // Auto-save
        this.autoSave();
    }
    
    updateFilePreview(fileId, file) {
        const preview = document.getElementById(`${fileId}-preview`);
        if (preview) {
            preview.classList.remove('hidden');
            const fileName = preview.querySelector('span');
            if (fileName) {
                fileName.textContent = `${file.name} (${this.formatFileSize(file.size)})`;
            }
        }
    }
    
    removeFile(fileId) {
        const input = document.getElementById(`${fileId}-file`);
        const preview = document.getElementById(`${fileId}-preview`);
        
        if (input) input.value = '';
        if (preview) preview.classList.add('hidden');
        
        this.uploadedFiles.delete(fileId);
        this.updateDocumentSummary();
        this.autoSave();
    }
    
    updateDocumentSummary() {
        const requiredDocs = ['business-license', 'tin-certificate', 'nssf-certificate'];
        const optionalDocs = ['company-profile', 'organizational-chart', 'other-documents'];
        
        let requiredCount = 0;
        let optionalCount = 0;
        let totalSize = 0;
        
        this.uploadedFiles.forEach((fileInfo, fileId) => {
            totalSize += fileInfo.size;
            if (requiredDocs.includes(fileId)) {
                requiredCount++;
            } else if (optionalDocs.includes(fileId)) {
                optionalCount++;
            }
        });
        
        document.getElementById('required-count').textContent = requiredCount;
        document.getElementById('optional-count').textContent = optionalCount;
        document.getElementById('total-size').textContent = this.formatFileSize(totalSize);
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 MB';
        const mb = bytes / (1024 * 1024);
        return `${mb.toFixed(2)} MB`;
    }
    
    setupAutoSave() {
        // Auto-save every 30 seconds
        this.autoSaveTimer = setInterval(() => {
            this.autoSave();
        }, 30000);
    }
    
    autoSave() {
        this.collectFormData();
        
        // Show saving indicator
        const indicator = document.getElementById('auto-save-indicator');
        if (indicator) {
            indicator.classList.add('saving');
            indicator.innerHTML = '<i data-feather="loader" class="w-3 h-3 inline mr-1 animate-spin"></i> Saving...';
            feather.replace();
        }
        
        // Simulate API call
        setTimeout(() => {
            localStorage.setItem('organizationSetupDraft', JSON.stringify({
                step: this.currentStep,
                data: this.formData,
                features: this.features,
                files: Array.from(this.uploadedFiles.entries()).map(([id, info]) => ({
                    id,
                    name: info.name,
                    size: info.size
                }))
            }));
            
            if (indicator) {
                indicator.classList.remove('saving');
                indicator.classList.add('saved');
                indicator.innerHTML = '<i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i> Auto-saved';
                feather.replace();
                
                setTimeout(() => {
                    indicator.classList.remove('saved');
                    indicator.innerHTML = '<i data-feather="check-circle" class="w-3 h-3 inline mr-1"></i> Auto-saved';
                }, 2000);
            }
        }, 1000);
    }
    
    loadSavedData() {
        const saved = localStorage.getItem('organizationSetupDraft');
        if (saved) {
            const data = JSON.parse(saved);
            this.formData = data.data || {};
            this.features = data.features || [];
            this.currentStep = data.step || 1;
            
            // Populate form fields
            Object.keys(this.formData).forEach(key => {
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    field.value = this.formData[key];
                }
            });
            
            // Restore selected features
            this.features.forEach(feature => {
                const card = document.querySelector(`[data-feature="${feature}"]`);
                if (card) {
                    card.classList.add('selected');
                }
            });
            
            // Go to saved step
            this.goToStep(this.currentStep);
        }
    }
    
    setupRealTimeValidation() {
        const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                // Clear error on input
                this.clearFieldError(input);
            });
        });
    }
    
    validateField(field) {
        const rules = this.validationRules[field.name];
        if (!rules) return true;
        
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        if (rules.required && !value) {
            isValid = false;
            errorMessage = 'This field is required';
        } else if (rules.minLength && value.length < rules.minLength) {
            isValid = false;
            errorMessage = `Minimum ${rules.minLength} characters required`;
        } else if (rules.email && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        } else if (rules.phone && !this.isValidPhone(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
        
        if (!isValid) {
            this.showFieldError(field, errorMessage);
        } else {
            this.clearFieldError(field);
        }
        
        return isValid;
    }
    
    showFieldError(field, message) {
        field.classList.add('border-red-500', 'validation-error');
        
        let errorElement = field.parentNode.querySelector('.validation-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'validation-message text-xs mt-1 text-red-500';
            field.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    clearFieldError(field) {
        field.classList.remove('border-red-500', 'validation-error');
        
        const errorElement = field.parentNode.querySelector('.validation-message');
        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }
    
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    isValidPhone(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
    handleInputChange(input) {
        this.formData[input.name] = input.value;
        this.updateProgress();
    }
    
    updateProgress() {
        const totalFields = document.querySelectorAll('input, select, textarea').length;
        const filledFields = document.querySelectorAll('input:not([value=""]), select:not([value=""]), textarea:not([value=""])').length;
        const progress = Math.round((filledFields / totalFields) * 100);
        
        // Update progress ring
        const progressRing = document.getElementById('progress-ring');
        const progressPercentage = document.getElementById('progress-percentage');
        const progressText = document.getElementById('progress-text');
        
        if (progressRing) {
            const circumference = 2 * Math.PI * 28;
            const offset = circumference - (progress / 100) * circumference;
            progressRing.style.strokeDashoffset = offset;
        }
        
        if (progressPercentage) {
            progressPercentage.textContent = `${progress}%`;
        }
        
        if (progressText) {
            if (progress < 25) {
                progressText.textContent = 'Just getting started';
            } else if (progress < 50) {
                progressText.textContent = 'Making progress';
            } else if (progress < 75) {
                progressText.textContent = 'Almost there';
            } else if (progress < 100) {
                progressText.textContent = 'Nearly complete';
            } else {
                progressText.textContent = 'Ready to submit';
            }
        }
    }
    
    updateBankPreview() {
        const companyName = document.getElementById('company-name')?.value || 'Company Name';
        const accountNumber = document.getElementById('account-number')?.value || '-';
        const bankName = document.getElementById('bank-name')?.value || '-';
        const branchName = document.getElementById('branch-name')?.value || '-';
        
        document.getElementById('preview-account-holder').textContent = companyName;
        document.getElementById('preview-account-number').textContent = accountNumber;
        document.getElementById('preview-bank-name').textContent = bankName;
        document.getElementById('preview-branch-name').textContent = branchName;
    }
    
    collectFormData() {
        const form = document.getElementById('organizationForm');
        const formData = new FormData(form);
        
        this.formData = {};
        for (let [key, value] of formData.entries()) {
            this.formData[key] = value;
        }
    }
    
    goToStep(stepNumber) {
        // Validate current step before moving
        if (stepNumber > this.currentStep && !this.validateCurrentStep()) {
            this.showNotification('Please complete required fields before proceeding', 'error');
            return;
        }
        
        // Hide current step
        document.getElementById(`step-${this.currentStep}`).classList.remove('active');
        document.getElementById(`step-${this.currentStep}`).classList.add('hidden');
        
        // Show new step
        this.currentStep = stepNumber;
        document.getElementById(`step-${this.currentStep}`).classList.remove('hidden');
        document.getElementById(`step-${this.currentStep}`).classList.add('active');
        
        // Update step indicators
        this.updateStepIndicators();
        
        // Update progress
        this.updateProgress();
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    validateCurrentStep() {
        const currentStepElement = document.getElementById(`step-${this.currentStep}`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        
        let isValid = true;
        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    updateStepIndicators() {
        document.querySelectorAll('.step-btn').forEach(btn => {
            const step = parseInt(btn.dataset.step);
            const indicator = btn.querySelector('.step-indicator');
            
            if (step < this.currentStep) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
                indicator.innerHTML = '<i data-feather="check" class="w-4 h-4"></i>';
            } else if (step === this.currentStep) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
                indicator.textContent = step;
            } else {
                indicator.classList.remove('active', 'completed');
                indicator.textContent = step;
            }
        });
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
    
    toggleFeature(element, feature) {
        element.classList.toggle('selected');
        
        if (element.classList.contains('selected')) {
            this.features.push(feature);
            element.setAttribute('data-feature', feature);
        } else {
            const index = this.features.indexOf(feature);
            if (index > -1) {
                this.features.splice(index, 1);
            }
            element.removeAttribute('data-feature');
        }
        
        this.autoSave();
    }
    
    completeSetup() {
        if (!this.validateCurrentStep()) {
            this.showNotification('Please complete all required fields', 'error');
            return;
        }
        
        this.collectFormData();
        
        // Show loading
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = '<div class="flex items-center justify-center"><i data-feather="loader" class="w-5 h-5 mr-2 animate-spin"></i> Processing...</div>';
        submitBtn.disabled = true;
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Simulate API call
        setTimeout(() => {
            // Clear draft
            localStorage.removeItem('organizationSetupDraft');
            
            // Show success
            this.showNotification('Organization setup completed successfully!', 'success');
            
            // Redirect or show completion message
            setTimeout(() => {
                window.location.href = '/dashboard';
            }, 2000);
        }, 3000);
    }
    
    saveDraft() {
        this.autoSave();
        this.showNotification('Draft saved successfully', 'success');
    }
    
    previewOrganization() {
        this.collectFormData();
        
        // Create preview modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 modal-backdrop-blur z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Organization Preview</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">Company Name</h3>
                            <p class="text-gray-600">${this.formData.company_name || 'Not provided'}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Industry</h3>
                            <p class="text-gray-600">${this.formData.industry || 'Not provided'}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Contact</h3>
                            <p class="text-gray-600">${this.formData.email || 'Not provided'}</p>
                            <p class="text-gray-600">${this.formData.phone || 'Not provided'}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Features</h3>
                            <p class="text-gray-600">${this.features.length > 0 ? this.features.join(', ') : 'No features selected'}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 flex justify-end">
                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
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
}

// Global functions for onclick handlers
let orgSetup;

function nextStep() {
    orgSetup.goToStep(orgSetup.currentStep + 1);
}

function previousStep() {
    orgSetup.goToStep(orgSetup.currentStep - 1);
}

function goToStep(step) {
    orgSetup.goToStep(step);
}

function toggleFeature(element, feature) {
    orgSetup.toggleFeature(element, feature);
}

function saveDraft() {
    orgSetup.saveDraft();
}

function previewOrganization() {
    orgSetup.previewOrganization();
}

function removeFile(fileId) {
    orgSetup.removeFile(fileId);
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    orgSetup = new OrganizationSetup();
});
</script>
@endpush

@endsection
