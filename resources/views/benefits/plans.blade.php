@extends('layouts.app')

@section('title', 'Benefits Plans')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Benefits Plans</h1>
            <p class="text-gray-600 mt-2">Manage company benefits offerings</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            Create Plan
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Health Plans -->
        <div class="bg-gradient-to-br from-green-50 to-teal-50 rounded-xl border border-green-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="heart" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-gray-600">2 plans</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Health Insurance</h3>
            <div class="space-y-3">
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">Comprehensive Plan</h4>
                    <p class="text-sm text-gray-600 mb-2">TZS 25,000/month</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">Basic Plan</h4>
                    <p class="text-sm text-gray-600 mb-2">TZS 10,000/month</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
            </div>
            <button class="mt-4 text-sm text-indigo-600 font-medium hover:underline">Manage Plans</button>
        </div>

        <!-- Retirement Plans -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-gray-600">2 plans</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Retirement</h3>
            <div class="space-y-3">
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">NSSF</h4>
                    <p class="text-sm text-gray-600 mb-2">Mandatory</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">Voluntary Savings</h4>
                    <p class="text-sm text-gray-600 mb-2">Optional</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
            </div>
            <button class="mt-4 text-sm text-indigo-600 font-medium hover:underline">Manage Plans</button>
        </div>

        <!-- Wellness Programs -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border border-purple-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="activity" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-gray-600">3 programs</span>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Wellness</h3>
            <div class="space-y-3">
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">Gym Membership</h4>
                    <p class="text-sm text-gray-600 mb-2">TZS 50,000/year</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
                <div class="p-3 bg-white rounded-lg border border-gray-200">
                    <h4 class="font-medium text-gray-900 mb-1">Annual Checkup</h4>
                    <p class="text-sm text-gray-600 mb-2">Free</p>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Active</span>
                </div>
            </div>
            <button class="mt-4 text-sm text-indigo-600 font-medium hover:underline">Manage Plans</button>
        </div>
    </div>

    <!-- Enrollment Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Enrollment Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900">45</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Health Insurance</p>
                <p class="text-2xl font-bold text-gray-900">42</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Retirement Plan</p>
                <p class="text-2xl font-bold text-gray-900">45</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">Wellness Programs</p>
                <p class="text-2xl font-bold text-gray-900">38</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
