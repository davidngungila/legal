@extends('layouts.app')

@section('title', 'Client Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Client Management</h1>
            <p class="text-gray-600 mt-2">Manage client organizations and their settings</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Clients
            </button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add New Client
            </button>
        </div>
    </div>

    <!-- Client Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+1</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">4</h3>
            <p class="text-gray-600 text-sm">Total Clients</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">All</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">4</h3>
            <p class="text-gray-600 text-sm">Active Clients</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">248</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">905</h3>
            <p class="text-gray-600 text-sm">Total Employees</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="trending-up" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">+15%</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">TZS 168M</h3>
            <p class="text-gray-600 text-sm">Monthly Revenue</p>
        </div>
    </div>

    <!-- Client List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Client Organizations</h3>
            <div class="flex space-x-3">
                <div class="relative">
                    <input type="text" placeholder="Search clients..." class="form-input pl-10 pr-4 py-2">
                        <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                </div>
                <select class="form-select">
                    <option>All Industries</option>
                    <option>Manufacturing</option>
                    <option>Construction</option>
                    <option>Mining</option>
                    <option>Logistics</option>
                </select>
                <select class="form-select">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                    <option>Trial</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach([
                ['name' => 'ABC Manufacturing Ltd', 'industry' => 'Manufacturing', 'employees' => 248, 'status' => 'Active', 'revenue' => '45.2M', 'contact' => 'John Smith', 'email' => 'john.smith@abcmanufacturing.co.tz', 'phone' => '+255 22 123 4567', 'address' => 'Plot 123, Industrial Area, Dar es Salaam', 'subscription' => 'Enterprise', 'start' => '2023-01-15', 'renewal' => '2025-01-15'],
                ['name' => 'XYZ Construction Co', 'industry' => 'Construction', 'employees' => 156, 'status' => 'Active', 'revenue' => '28.7M', 'contact' => 'Sarah Johnson', 'email' => 'sarah.johnson@xyzconstruction.co.tz', 'phone' => '+255 22 234 5678', 'address' => 'Plot 456, Kigamboni, Dar es Salaam', 'subscription' => 'Professional', 'start' => '2023-03-01', 'renewal' => '2025-03-01'],
                ['name' => 'Tanzania Mining Corp', 'industry' => 'Mining', 'employees' => 412, 'status' => 'Active', 'revenue' => '78.9M', 'contact' => 'Michael Chen', 'email' => 'michael.chen@tzmining.co.tz', 'phone' => '+255 22 345 6789', 'address' => 'Plot 789, Mwanza', 'subscription' => 'Enterprise', 'start' => '2022-06-01', 'renewal' => '2024-06-01'],
                ['name' => 'East Africa Logistics', 'industry' => 'Logistics', 'employees' => 89, 'status' => 'Active', 'revenue' => '15.3M', 'contact' => 'Emily Davis', 'email' => 'emily.davis@eallogistics.co.tz', 'phone' => '+255 22 456 7890', 'address' => 'Plot 321, Port Area, Dar es Salaam', 'subscription' => 'Standard', 'start' => '2023-09-01', 'renewal' => '2025-09-01']
            ] as $client)
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <i data-feather="briefcase" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">{{ $client['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $client['industry'] }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">{{ $client['status'] }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-sm text-gray-600">Employees</p>
                        <p class="font-medium text-gray-900">{{ $client['employees'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Monthly Revenue</p>
                        <p class="font-medium text-gray-900">TZS {{ $client['revenue'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Subscription</p>
                        <p class="font-medium text-gray-900">{{ $client['subscription'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Renewal</p>
                        <p class="font-medium text-gray-900">{{ $client['renewal'] }}</p>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $client['contact'] }}</p>
                            <p class="text-sm text-gray-600">{{ $client['email'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-indigo-600 hover:text-indigo-800">
                                <i data-feather="mail" class="w-4 h-4"></i>
                            </button>
                            <button class="text-indigo-600 hover:text-indigo-800">
                                <i data-feather="phone" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600">{{ $client['address'] }}</p>
                        <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage →</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Client Analytics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Revenue by Client</h3>
            <div class="space-y-4">
                @foreach([
                    ['client' => 'Tanzania Mining Corp', 'revenue' => 78900000, 'percentage' => 47],
                    ['client' => 'ABC Manufacturing Ltd', 'revenue' => 45200000, 'percentage' => 27],
                    ['client' => 'XYZ Construction Co', 'revenue' => 28700000, 'percentage' => 17],
                    ['client' => 'East Africa Logistics', 'revenue' => 15300000, 'percentage' => 9]
                ] as $revenue)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-900">{{ $revenue['client'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-900 mr-2">TZS {{ number_format($revenue['revenue'], 0) }}</span>
                        <span class="text-sm text-gray-500">({{ $revenue['percentage'] }}%)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Industry Distribution</h3>
            <div class="space-y-4">
                @foreach([
                    ['industry' => 'Manufacturing', 'clients' => 1, 'employees' => 248],
                    ['industry' => 'Construction', 'clients' => 1, 'employees' => 156],
                    ['industry' => 'Mining', 'clients' => 1, 'employees' => 412],
                    ['industry' => 'Logistics', 'clients' => 1, 'employees' => 89]
                ] as $industry)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-900">{{ $industry['industry'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm font-medium text-gray-900 mr-2">{{ $industry['employees'] }}</span>
                        <span class="text-sm text-gray-500">employees</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Subscription Plans -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Subscription Plans Overview</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Manage Plans</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach([
                ['plan' => 'Standard', 'clients' => 1, 'price' => 150000, 'features' => ['Basic HR Features', 'Up to 100 Employees', 'Email Support', 'Monthly Reports']],
                ['plan' => 'Professional', 'clients' => 1, 'price' => 300000, 'features' => ['Advanced HR Features', 'Up to 500 Employees', 'Priority Support', 'Custom Reports']],
                ['plan' => 'Enterprise', 'clients' => 2, 'price' => 600000, 'features' => ['All Features', 'Unlimited Employees', '24/7 Support', 'API Access', 'Custom Integrations']],
                ['plan' => 'Custom', 'clients' => 0, 'price' => 0, 'features' => ['Tailored Solutions', 'Custom Pricing', 'Dedicated Support', 'On-Premise Option']]
            ] as $plan)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">{{ $plan['plan'] }}</h4>
                    <span class="text-sm text-gray-500">{{ $plan['clients'] }} clients</span>
                </div>
                <div class="mb-3">
                    <p class="text-2xl font-bold text-gray-900">
                        @if($plan['price'] > 0)
                        TZS {{ number_format($plan['price'], 0) }}
                        @else
                        Custom
                        @endif
                    </p>
                    <p class="text-sm text-gray-600">per month</p>
                </div>
                <div class="space-y-2 mb-4">
                    @foreach($plan['features'] as $feature)
                    <div class="flex items-center">
                        <i data-feather="check" class="w-4 h-4 text-green-600 mr-2"></i>
                        <span class="text-sm text-gray-700">{{ $feature }}</span>
                    </div>
                    @endforeach
                </div>
                <button class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                    {{ $plan['clients'] > 0 ? 'View Clients' : 'Contact Sales' }}
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Client Activities</h3>
            <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</button>
        </div>
        <div class="space-y-4">
            @foreach([
                ['client' => 'ABC Manufacturing Ltd', 'action' => 'Subscription renewed', 'time' => '2 days ago', 'user' => 'System'],
                ['client' => 'XYZ Construction Co', 'action' => 'New user added', 'time' => '3 days ago', 'user' => 'Sarah Williams'],
                ['client' => 'Tanzania Mining Corp', 'action' => 'Support ticket resolved', 'time' => '5 days ago', 'user' => 'John Doe'],
                ['client' => 'East Africa Logistics', 'action' => 'Training session completed', 'time' => '1 week ago', 'user' => 'Michael Chen'],
                ['client' => 'ABC Manufacturing Ltd', 'action' => 'Report generated', 'time' => '2 weeks ago', 'user' => 'System']
            ] as $activity)
            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                    <i data-feather="activity" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity['action'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['client'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['user'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
