@extends('layouts.app')

@section('title', 'Organization Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Organization Management</h1>
            <p class="text-gray-600 mt-2">Manage company structure, departments, and organizational hierarchy</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <a href="{{ route('organization.setup') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="settings" class="w-4 h-4 inline mr-2"></i>
                Organization Setup
            </a>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add Department
            </button>
        </div>
    </div>

    <!-- Organization Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="building" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">12</p>
            <p class="text-sm text-gray-500">Departments</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Active</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">10</p>
            <p class="text-sm text-gray-500">Active Departments</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-feather="briefcase" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-sm text-purple-600 font-medium">Positions</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">45</p>
            <p class="text-sm text-gray-500">Job Positions</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i data-feather="git-branch" class="w-6 h-6 text-orange-600"></i>
                </div>
                <span class="text-sm text-orange-600 font-medium">Levels</span>
            </div>
            <p class="text-2xl font-bold text-gray-900">8</p>
            <p class="text-sm text-gray-500">Hierarchy Levels</p>
        </div>
    </div>

    <!-- Organization Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Organization Chart</h2>
            <div class="flex space-x-3">
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-feather="maximize-2" class="w-4 h-4 inline mr-1"></i>
                    Full Screen
                </button>
                <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i data-feather="download" class="w-4 h-4 inline mr-1"></i>
                    Export
                </button>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="building" class="w-8 h-8 text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Organization Chart</h3>
            <p class="text-gray-600 mb-4">Interactive organization hierarchy visualization</p>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="eye" class="w-4 h-4 inline mr-2"></i>
                View Full Chart
            </button>
        </div>
    </div>

    <!-- Departments List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Departments</h2>
                <div class="flex items-center space-x-3">
                    <input type="text" placeholder="Search departments..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <select class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Head of Department</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-feather="briefcase" class="w-5 h-5 text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Human Resources</div>
                                    <div class="text-sm text-gray-500">HR & Administration</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Sarah Williams</div>
                            <div class="text-sm text-gray-500">HR Director</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            15
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS 45M
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-feather="dollar-sign" class="w-5 h-5 text-green-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Finance & Accounting</div>
                                    <div class="text-sm text-gray-500">Financial Management</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">John Mwangi</div>
                            <div class="text-sm text-gray-500">CFO</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            12
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS 38M
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-feather="code" class="w-5 h-5 text-purple-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Information Technology</div>
                                    <div class="text-sm text-gray-500">IT & Systems</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">David Kimario</div>
                            <div class="text-sm text-gray-500">IT Manager</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            8
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS 25M
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                    <i data-feather="shopping-cart" class="w-5 h-5 text-orange-600"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">Sales & Marketing</div>
                                    <div class="text-sm text-gray-500">Business Development</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Grace Kileo</div>
                            <div class="text-sm text-gray-500">Sales Director</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            20
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            TZS 52M
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <button class="text-gray-600 hover:text-gray-900">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing 1 to 4 of 12 departments
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Previous</button>
                    <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm">1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">2</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">3</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize feather icons
if (typeof feather !== 'undefined') {
    feather.replace();
}

// Client switching function (required by sidebar)
function switchClient(clientId) {
    showNotification('Switching to client...', 'info');
    
    setTimeout(() => {
        const select = document.querySelector('select[onchange="switchClient(this.value)"]');
        if (select) {
            select.value = clientId;
        }
        
        const clientNames = {
            '1': 'ABC Manufacturing Ltd',
            '2': 'XYZ Construction Co',
            '3': 'Tanzania Mining Corp',
            '4': 'East Africa Logistics'
        };
        
        showNotification(`Switched to ${clientNames[clientId]}`, 'success');
    }, 500);
}

// Notification functions (required by header)
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

function removeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (notification) {
        notification.remove();
        updateNotificationBadge();
    }
}

function markAllAsRead() {
    const unreadNotifications = document.querySelectorAll('.notification-item:not(.read)');
    unreadNotifications.forEach(notification => {
        notification.classList.add('read');
    });
    updateNotificationBadge();
}

function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    const unreadCount = document.querySelectorAll('.notification-item:not(.read)').length;
    if (badge) {
        if (unreadCount > 0) {
            badge.textContent = unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}
</script>
@endpush

@endsection
