@extends('layouts.app')

@section('title', 'Client Management - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Client Management</h1>
            <p class="text-gray-600 mt-2">Manage multi-tenant clients and their HR systems</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="exportClients()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Export Clients
            </button>
            <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="plus" class="w-4 h-4 inline mr-2"></i>
                Add New Client
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-blue-600 font-medium">Total</span>
            </div>
            <p class="text-2xl font-bold text-gray-900" id="totalClients">0</p>
            <p class="text-sm text-gray-500">Total Clients</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">Active</span>
            </div>
            <p class="text-2xl font-bold text-gray-900" id="activeClients">0</p>
            <p class="text-sm text-gray-500">Active Clients</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-feather="pause-circle" class="w-6 h-6 text-gray-600"></i>
                </div>
                <span class="text-sm text-gray-600 font-medium">Inactive</span>
            </div>
            <p class="text-2xl font-bold text-gray-900" id="inactiveClients">0</p>
            <p class="text-sm text-gray-500">Inactive Clients</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="alert-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">Suspended</span>
            </div>
            <p class="text-2xl font-bold text-gray-900" id="suspendedClients">0</p>
            <p class="text-sm text-gray-500">Suspended Clients</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Filters</h2>
            <button onclick="resetFilters()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Reset Filters</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="searchInput" placeholder="Search clients..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Industry</label>
                <select id="industryFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Industries</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select id="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="created_at">Created Date</option>
                    <option value="name">Name</option>
                    <option value="industry">Industry</option>
                    <option value="employee_count">Employee Count</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Clients</h2>
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                    <select id="bulkAction" class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Bulk Actions</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="delete">Delete</option>
                        <option value="export">Export Selected</option>
                    </select>
                    <button onclick="performBulkAction()" class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition-colors">
                        Apply
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Industry</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="clientsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Clients will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalResults">0</span> results
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="previousPage()" class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Previous</button>
                    <div id="paginationNumbers" class="flex items-center space-x-1">
                        <!-- Pagination numbers will be loaded here -->
                    </div>
                    <button onclick="nextPage()" class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition-colors">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// API endpoints
const API_BASE = '/api/clients';

// Global variables
let clients = [];
let filteredClients = [];
let currentPage = 1;
let totalPages = 1;
let selectedClients = [];

// Load clients from API
async function loadClients() {
    console.log('Loading clients...');
    try {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const industry = document.getElementById('industryFilter').value;
        const sortBy = document.getElementById('sortBy').value;
        const sortOrder = 'desc';
        
        // Create URL parameters
        const params = new URLSearchParams({
            search: search || '',
            status: status || '',
            industry: industry || '',
            sort_by: sortBy || 'created_at',
            sort_order: sortOrder
        });
        
        console.log('Fetching:', `${API_BASE}?${params}`);
        const response = await fetch(`${API_BASE}?${params}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        console.log('Response status:', response.status);
        console.log('Response data:', data);
        console.log('Data structure:', JSON.stringify(data, null, 2));
        
        if (data.success) {
            // Handle simple array structure (like permissions)
            clients = data.clients || [];
            filteredClients = [...clients];
            totalPages = 1;
            
            console.log('Clients data:', data.clients);
            console.log('Clients data type:', typeof data.clients);
            console.log('Clients array:', clients);
            console.log('Filtered clients:', filteredClients);
            console.log('Total pages:', totalPages);
            
            renderClients();
            updateStats();
            updatePagination(data.clients);
        } else {
            console.log('API returned success=false');
            showNotification('Failed to load clients', 'error');
        }
    } catch (error) {
        console.error('Error loading clients:', error);
        showNotification('Error loading clients: ' + error.message, 'error');
    }
}

// Update statistics
function updateStatistics(stats) {
    document.getElementById('totalClients').textContent = stats.total;
    document.getElementById('activeClients').textContent = stats.active;
    document.getElementById('inactiveClients').textContent = stats.inactive;
    document.getElementById('suspendedClients').textContent = stats.suspended;
}

// Update statistics function
function updateStats() {
    // Stats will be updated from API response
    // This function can be used for real-time updates if needed
    const stats = {
        total: clients.length,
        active: clients.filter(client => client.status === 'active').length,
        inactive: clients.filter(client => client.status === 'inactive').length,
        suspended: clients.filter(client => client.status === 'suspended').length
    };
    updateStatistics(stats);
}

// Update industry filter
function updateIndustryFilter(industries) {
    const select = document.getElementById('industryFilter');
    const currentValue = select.value;
    
    select.innerHTML = '<option value="">All Industries</option>';
    industries.forEach(industry => {
        select.innerHTML += `<option value="${industry}">${industry}</option>`;
    });
    
    select.value = currentValue;
}

// Render clients table
function renderClients() {
    console.log('renderClients() called');
    console.log('filteredClients:', filteredClients);
    console.log('filteredClients length:', filteredClients.length);
    
    const tbody = document.getElementById('clientsTableBody');
    console.log('tbody element:', tbody);
    
    if (!tbody) {
        console.error('clientsTableBody element not found!');
        return;
    }
    
    tbody.innerHTML = '';
    console.log('tbody cleared');
    
    if (filteredClients.length === 0) {
        console.log('No clients to display');
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4 text-gray-500">No clients found</td></tr>';
        return;
    }
    
    filteredClients.forEach((client, index) => {
        console.log(`Rendering client ${index}:`, client);
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="checkbox" class="client-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded" value="${client.id}">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-indigo-600 font-semibold">${client.name.charAt(0)}</span>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">${client.name}</div>
                        <div class="text-sm text-gray-500">${client.email}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-900">${client.industry}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${client.city}, ${client.country}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${client.contact_person}</div>
                <div class="text-sm text-gray-500">${client.contact_phone}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getSubscriptionBadge(client.subscription_plan)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${client.employee_count}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(client.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('clients.edit') }}?id=${client.id}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <button onclick="deleteClient(${client.id})" class="text-red-600 hover:text-red-900">Delete</button>
                </td>
        `;
        tbody.appendChild(row);
    });
    
    console.log('Finished rendering clients');
    console.log('Rows added:', filteredClients.length);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Add checkbox event listeners
    addCheckboxListeners();
}

// Get status badge HTML
function getStatusBadge(status) {
    const badges = {
        active: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>',
        inactive: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>',
        suspended: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Suspended</span>'
    };
    return badges[status] || badges.inactive;
}

// Get subscription badge HTML
function getSubscriptionBadge(plan) {
    const badges = {
        basic: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Basic</span>',
        premium: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Premium</span>',
        enterprise: '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Enterprise</span>'
    };
    return badges[plan] || badges.basic;
}

// Add checkbox listeners
function addCheckboxListeners() {
    const checkboxes = document.querySelectorAll('.client-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const clientId = parseInt(this.value);
            if (this.checked) {
                selectedClients.push(clientId);
            } else {
                selectedClients = selectedClients.filter(id => id !== clientId);
            }
            updateBulkActionButton();
        });
    });
}

// Update bulk action button
function updateBulkActionButton() {
    const bulkAction = document.getElementById('bulkAction');
    const applyButton = bulkAction.nextElementSibling;
    
    if (selectedClients.length > 0) {
        bulkAction.disabled = false;
        applyButton.disabled = false;
    } else {
        bulkAction.disabled = true;
        applyButton.disabled = true;
    }
}

// Delete client
async function deleteClient(clientId) {
    if (!confirm('Are you sure you want to delete this client?')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/${clientId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Client deleted successfully', 'success');
            loadClients();
        } else {
            showNotification('Failed to delete client', 'error');
        }
    } catch (error) {
        showNotification('Error deleting client: ' + error.message, 'error');
    }
}

// Export clients
async function exportClients() {
    try {
        const status = document.getElementById('statusFilter').value;
        const industry = document.getElementById('industryFilter').value;
        
        const params = new URLSearchParams({
            status: status,
            industry: industry
        });
        
        const response = await fetch(`${API_BASE}/export?${params}`);
        const data = await response.json();
        
        if (data.success) {
            // Create and download CSV
            const blob = new Blob([data.csv_data], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `clients_export_${new Date().toISOString().split('T')[0]}.csv`;
            a.click();
            window.URL.revokeObjectURL(url);
            
            showNotification(`Exported ${data.count} clients successfully`, 'success');
        } else {
            showNotification('Failed to export clients', 'error');
        }
    } catch (error) {
        showNotification('Error exporting clients: ' + error.message, 'error');
    }
}

// Bulk operations
async function performBulkAction() {
    const action = document.getElementById('bulkAction').value;
    
    if (!action || selectedClients.length === 0) {
        showNotification('Please select clients and an action', 'warning');
        return;
    }
    
    if (action === 'delete' && !confirm(`Are you sure you want to delete ${selectedClients.length} clients?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}/bulk`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                client_ids: selectedClients
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Handle export
            if (action === 'export') {
                const blob = new Blob([data.csv_data], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `selected_clients_export_${new Date().toISOString().split('T')[0]}.csv`;
                a.click();
                window.URL.revokeObjectURL(url);
            }
            
            // Reset selection
            selectedClients = [];
            document.getElementById('selectAll').checked = false;
            updateBulkActionButton();
            
            loadClients();
        } else {
            showNotification(data.message || 'Bulk operation failed', 'error');
        }
    } catch (error) {
        showNotification('Error performing bulk operation: ' + error.message, 'error');
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('industryFilter').value = '';
    document.getElementById('sortBy').value = 'created_at';
    currentPage = 1;
    loadClients();
}

// Pagination
function updatePagination(paginationData) {
    document.getElementById('showingFrom').textContent = paginationData.from || 0;
    document.getElementById('showingTo').textContent = paginationData.to || 0;
    document.getElementById('totalResults').textContent = paginationData.total || 0;
    
    // Update pagination numbers
    const paginationNumbers = document.getElementById('paginationNumbers');
    paginationNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.className = `px-3 py-1 border rounded-lg text-sm ${i === currentPage ? 'bg-indigo-600 text-white' : 'border-gray-300 hover:bg-gray-50'}`;
        button.onclick = () => goToPage(i);
        paginationNumbers.appendChild(button);
    }
}

function goToPage(page) {
    currentPage = page;
    loadClients();
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        loadClients();
    }
}

function nextPage() {
    if (currentPage < totalPages) {
        currentPage++;
        loadClients();
    }
}

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.client-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        const clientId = parseInt(checkbox.value);
        if (this.checked) {
            if (!selectedClients.includes(clientId)) {
                selectedClients.push(clientId);
            }
        } else {
            selectedClients = selectedClients.filter(id => id !== clientId);
        }
    });
    updateBulkActionButton();
});

// Filter and search event listeners
document.getElementById('searchInput').addEventListener('input', debounce(() => {
    currentPage = 1;
    loadClients();
}, 300));

document.getElementById('statusFilter').addEventListener('change', () => {
    currentPage = 1;
    loadClients();
});

document.getElementById('industryFilter').addEventListener('change', () => {
    currentPage = 1;
    loadClients();
});

document.getElementById('sortBy').addEventListener('change', () => {
    currentPage = 1;
    loadClients();
});

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ' ' + colors[type] || colors.info;
    notification.innerHTML = `
        <div class="flex items-center">
            <i data-feather="${type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5 mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadClients();
    
    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});

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
