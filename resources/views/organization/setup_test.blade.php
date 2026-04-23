@extends('layouts.app')

@section('title', 'Organization Setup Test - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Organization Setup</h1>
            <p class="text-gray-600 mt-2">Configure your organization details and company information</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                Reset
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="save" class="w-4 h-4 mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Test Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Organization Setup Test</h2>
        <p class="text-gray-600">If you can see this page, the basic routing and layout are working correctly.</p>
        
        <div class="mt-6">
            <button onclick="alert('JavaScript is working!')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Test JavaScript
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Organization setup test page loaded successfully!');
    
    // Initialize Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>
@endpush
@endsection
