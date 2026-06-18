@extends('layouts.app')

@section('title', 'Benefits Enrollment')

@section('content')
<div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Benefits Enrollment</h1>
            <p class="text-gray-600 mt-2">Review and enroll in available benefits</p>
        </div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
            Save Changes
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Health Insurance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="heart" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Health Insurance</h3>
                    <p class="text-sm text-gray-600">Medical, dental, and vision coverage</p>
                </div>
            </div>
            <div class="space-y-3">
                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-200">
                    <input type="radio" name="health" class="mr-3" checked>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Comprehensive Plan</p>
                        <p class="text-sm text-gray-600">TZS 25,000/month • 80% coverage</p>
                    </div>
                </label>
                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-200">
                    <input type="radio" name="health" class="mr-3">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Basic Plan</p>
                        <p class="text-sm text-gray-600">TZS 10,000/month • 60% coverage</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Retirement Plan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="dollar-sign" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Retirement Plan</h3>
                    <p class="text-sm text-gray-600">NSSF and voluntary savings</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">NSSF Contribution</p>
                        <p class="text-sm text-gray-600">10% employee, 10% employer</p>
                    </div>
                    <span class="text-sm text-gray-600">Enrolled</span>
                </div>
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Voluntary Savings</p>
                        <div class="mt-2 flex items-center space-x-2">
                            <input type="number" class="w-24 px-2 py-1 border border-gray-300 rounded text-sm" placeholder="0" value="5">
                            <span class="text-sm text-gray-600">% of salary</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Off -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="calendar" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Time Off</h3>
                    <p class="text-sm text-gray-600">Annual leave and paid time off</p>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Annual Leave</p>
                        <p class="text-sm text-gray-600">21 days per year</p>
                    </div>
                    <span class="text-sm text-gray-600">12 days remaining</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Sick Leave</p>
                        <p class="text-sm text-gray-600">10 days per year</p>
                    </div>
                    <span class="text-sm text-gray-600">8 days remaining</span>
                </div>
            </div>
        </div>

        <!-- Additional Benefits -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="gift" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Additional Benefits</h3>
                    <p class="text-sm text-gray-600">Perks and allowances</p>
                </div>
            </div>
            <div class="space-y-3">
                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-200">
                    <input type="checkbox" class="mr-3" checked>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Transport Allowance</p>
                        <p class="text-sm text-gray-600">TZS 150,000/month</p>
                    </div>
                </label>
                <label class="flex items-center p-3 bg-gray-50 rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-200">
                    <input type="checkbox" class="mr-3" checked>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">Meal Allowance</p>
                        <p class="text-sm text-gray-600">TZS 75,000/month</p>
                    </div>
                </label>
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
