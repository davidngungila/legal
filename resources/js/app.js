import './bootstrap';

// LegalHR JavaScript Application

// Initialize Feather Icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Initialize tooltips and popovers
    initializeTooltips();
    
    // Initialize date pickers
    initializeDatePickers();
    
    // Initialize form validations
    initializeFormValidations();
    
    // Load saved client preference
    loadSavedClient();
});

// Client Management
function switchClient(clientId) {
    showClientSwitchModal(clientId);
}

function showClientSwitchModal(clientId) {
    // Add blur effect to background
    document.body.classList.add('backdrop-blur-sm');
    
    // Create modal overlay with transparent background
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'fixed inset-0 z-50 flex items-center justify-center';
    modalOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.1)'; // Very light transparent overlay
    modalOverlay.id = 'clientSwitchModal';
    
    // Get client name
    const clients = {
        '1': 'ABC Manufacturing Ltd',
        '2': 'XYZ Construction Co',
        '3': 'Tanzania Mining Corp',
        '4': 'East Africa Logistics'
    };
    const clientName = clients[clientId] || 'Unknown Client';
    
    // Create modal content
    modalOverlay.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full mx-4 transform transition-all">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-feather="briefcase" class="w-8 h-8 text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Switch Client</h3>
                <p class="text-gray-600">Are you sure you want to switch to <strong>${clientName}</strong>?</p>
                <p class="text-sm text-gray-500 mt-2">All data will be refreshed and updated.</p>
            </div>
            
            <div class="flex space-x-3">
                <button onclick="closeClientSwitchModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmClientSwitch('${clientId}', '${clientName}')" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Switch Client
                </button>
            </div>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(modalOverlay);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Add animation
    setTimeout(() => {
        modalOverlay.querySelector('.transform').classList.add('scale-100');
    }, 10);
}

function closeClientSwitchModal() {
    const modal = document.getElementById('clientSwitchModal');
    if (modal) {
        modal.querySelector('.transform').classList.remove('scale-100');
        
        // Remove blur effect from background
        document.body.classList.remove('backdrop-blur-sm');
        
        setTimeout(() => {
            document.body.removeChild(modal);
        }, 200);
    }
}

function confirmClientSwitch(clientId, clientName) {
    // Close modal
    closeClientSwitchModal();
    
    // Show splash screen
    showClientSwitchSplash(clientName);
    
    // Simulate switching process
    setTimeout(() => {
        // Store selected client
        localStorage.setItem('selectedClient', clientId);
        
        // Update all client-specific data displays
        updateClientData(clientId);
        
        // Hide splash and show success
        hideClientSwitchSplash(clientName);
    }, 2000);
}

function showClientSwitchSplash(clientName) {
    // Create splash overlay
    const splashOverlay = document.createElement('div');
    splashOverlay.className = 'fixed inset-0 bg-gradient-to-br from-indigo-600 to-purple-700 z-50 flex items-center justify-center';
    splashOverlay.id = 'clientSwitchSplash';
    
    splashOverlay.innerHTML = `
        <div class="text-center text-white">
            <div class="mb-8">
                <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                    <i data-feather="briefcase" class="w-12 h-12 text-white"></i>
                </div>
                <h2 class="text-3xl font-bold mb-4">Switching Client</h2>
                <p class="text-xl opacity-90">Now working in <strong>${clientName}</strong></p>
            </div>
            
            <div class="flex justify-center space-x-2 mb-8">
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                <div class="w-3 h-3 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
            </div>
            
            <div class="text-sm opacity-75">
                <p>Refreshing all data...</p>
                <p class="mt-1">Please wait a moment</p>
            </div>
        </div>
    `;
    
    // Add to body
    document.body.appendChild(splashOverlay);
    
    // Re-initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function hideClientSwitchSplash(clientName) {
    // Show success message
    const splash = document.getElementById('clientSwitchSplash');
    if (splash) {
        splash.innerHTML = `
            <div class="text-center text-white">
                <div class="mb-8">
                    <div class="w-24 h-24 bg-green-400 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                        <i data-feather="check-circle" class="w-12 h-12 text-white"></i>
                    </div>
                    <h2 class="text-3xl font-bold mb-4">Client Switched!</h2>
                    <p class="text-xl opacity-90">You are now working in <strong>${clientName}</strong></p>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-6">
                    <p class="text-sm mb-2">✅ Dashboard data updated</p>
                    <p class="text-sm mb-2">✅ Employee records refreshed</p>
                    <p class="text-sm mb-2">✅ Analytics data synchronized</p>
                    <p class="text-sm">✅ Company information loaded</p>
                </div>
                
                <button onclick="closeClientSwitchSplash()" class="px-6 py-3 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-opacity-90 transition-colors">
                    Continue to Dashboard
                </button>
            </div>
        `;
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

function closeClientSwitchSplash() {
    const splash = document.getElementById('clientSwitchSplash');
    if (splash) {
        splash.style.opacity = '0';
        splash.style.transition = 'opacity 0.5s ease-out';
        setTimeout(() => {
            document.body.removeChild(splash);
        }, 500);
    }
}

function updateClientData(clientId) {
    const clients = {
        '1': {
            name: 'ABC Manufacturing Ltd',
            employees: 248,
            payroll: 'TZS 45.2M',
            compliance: 94
        },
        '2': {
            name: 'XYZ Construction Co',
            employees: 156,
            payroll: 'TZS 28.7M',
            compliance: 91
        },
        '3': {
            name: 'Tanzania Mining Corp',
            employees: 412,
            payroll: 'TZS 78.9M',
            compliance: 88
        },
        '4': {
            name: 'East Africa Logistics',
            employees: 89,
            payroll: 'TZS 15.3M',
            compliance: 96
        }
    };
    
    const client = clients[clientId];
    if (client) {
        // Update dashboard stats dynamically
        updateDashboardStats(client);
        
        // Update company names throughout the page
        updateCompanyNames(client.name);
        
        // Update charts if they exist
        updateCharts(client);
    }
}

function updateDashboardStats(client) {
    // Update employee count
    const employeeElements = document.querySelectorAll('.employee-count');
    employeeElements.forEach(el => {
        el.textContent = client.employees;
    });
    
    // Update payroll amount
    const payrollElements = document.querySelectorAll('.payroll-amount');
    payrollElements.forEach(el => {
        el.textContent = client.payroll;
    });
    
    // Update compliance score
    const complianceElements = document.querySelectorAll('.compliance-score');
    complianceElements.forEach(el => {
        el.textContent = client.compliance + '%';
    });
}

function updateCompanyNames(companyName) {
    const companyElements = document.querySelectorAll('.company-name');
    companyElements.forEach(el => {
        el.textContent = companyName;
    });
}

function updateCharts(client) {
    // Update Chart.js charts if they exist
    if (typeof Chart !== 'undefined') {
        Chart.helpers.each(Chart.instances, function(instance) {
            if (instance.config.type === 'doughnut' && instance.data.labels.includes('Employees')) {
                instance.data.datasets[0].data[0] = client.employees;
                instance.update();
            }
        });
    }
}

function loadSavedClient() {
    const savedClient = localStorage.getItem('selectedClient');
    if (savedClient) {
        const select = document.querySelector('select[onchange="switchClient(this.value)"]');
        if (select) {
            select.value = savedClient;
            updateClientData(savedClient);
        }
    }
}

// UI Components
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobile-menu-overlay');
    
    if (sidebar) {
        sidebar.classList.toggle('-translate-x-full');
        
        // Toggle overlay for mobile
        if (window.innerWidth < 1024) {
            if (overlay) {
                overlay.classList.toggle('hidden');
            } else {
                createMobileOverlay();
            }
        }
    }
}

function createMobileOverlay() {
    const overlay = document.createElement('div');
    overlay.id = 'mobile-menu-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-40 hidden';
    overlay.onclick = toggleSidebar;
    document.body.appendChild(overlay);
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

// Notifications
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('animate-fadeIn');
    }, 10);
    
    // Remove after duration
    setTimeout(() => {
        notification.classList.remove('animate-fadeIn');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, duration);
}

// Form Utilities
function initializeFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        } else {
            clearError(input);
        }
    });
    
    return isValid;
}

function showError(input, message) {
    clearError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-xs mt-1';
    errorDiv.textContent = message;
    
    input.parentNode.appendChild(errorDiv);
    input.classList.add('border-red-500');
}

function clearError(input) {
    const errorDiv = input.parentNode.querySelector('.text-red-500');
    if (errorDiv) {
        errorDiv.remove();
    }
    input.classList.remove('border-red-500');
}

// Date Pickers
function initializeDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Set min date to today
        const today = new Date().toISOString().split('T')[0];
        input.min = today;
    });
}

// Tooltips
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            showTooltip(e.target, e.target.dataset.tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element, text) {
    hideTooltip(); // Remove any existing tooltip
    
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute bg-gray-800 text-white text-xs rounded px-2 py-1 z-50';
    tooltip.textContent = text;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
}

function hideTooltip() {
    const tooltip = document.querySelector('.absolute.bg-gray-800');
    if (tooltip) {
        tooltip.remove();
    }
}

// Chart Utilities
function initializeCharts() {
    // Employee Distribution Chart
    const employeeCtx = document.getElementById('employeeChart');
    if (employeeCtx && typeof Chart !== 'undefined') {
        new Chart(employeeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Permanent', 'Contract', 'Probation', 'Intern'],
                datasets: [{
                    data: [180, 45, 18, 5],
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    // Attendance Trend Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx && typeof Chart !== 'undefined') {
        new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                datasets: [{
                    label: 'Attendance Rate %',
                    data: [95, 94, 96, 93, 95, 97, 96, 94, 95, 93, 94],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 85,
                        max: 100
                    }
                }
            }
        });
    }
}

// Print functionality
function printPage() {
    window.print();
}

// Export functionality
function exportTable(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            rowData.push(col.textContent.trim());
        });
        
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'export.csv';
    a.click();
    
    window.URL.revokeObjectURL(url);
}

// Search functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll('[data-search]');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const targetSelector = e.target.dataset.search;
            const targets = document.querySelectorAll(targetSelector);
            
            targets.forEach(target => {
                const text = target.textContent.toLowerCase();
                target.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    });
}

// Modal functionality
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    // Close user dropdown
    const userDropdown = document.getElementById('userDropdown');
    const userButton = document.getElementById('userButton');
    
    if (userButton && !userButton.contains(event.target) && userDropdown && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
    }
    
    // Close notification dropdown
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationButton = event.target.closest('button[onclick="toggleNotifications()"], button[onclick*="toggleNotifications"]');
    
    if (notificationDropdown && !notificationDropdown.contains(event.target) && !notificationButton) {
        notificationDropdown.classList.add('hidden');
    }
});

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeSearch();
    initializeTooltips();
});

// Notification System
function toggleNotifications() {
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationDropdown) {
        notificationDropdown.classList.toggle('hidden');
    }
}

function removeNotification(id) {
    const notification = document.querySelector(`.notification-item[data-id="${id}"]`);
    if (notification) {
        notification.remove();
        updateNotificationBadge();
        showNotification('Notification removed', 'info', 2000);
    }
}

function markAllAsRead() {
    const notifications = document.querySelectorAll('.notification-item');
    notifications.forEach(notification => {
        notification.remove();
    });
    updateNotificationBadge();
    showNotification('All notifications marked as read', 'success', 2000);
    toggleNotifications();
}

function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    const notifications = document.querySelectorAll('.notification-item');
    if (badge) {
        const count = notifications.length;
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

function addNotification(title, message, type = 'info') {
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;
    
    const notificationList = dropdown.querySelector('.max-h-96');
    if (!notificationList) return;
    
    const id = Date.now();
    const iconClass = type === 'critical' ? 'alert-triangle text-red-600 bg-red-100' : 
                      type === 'warning' ? 'alert-circle text-yellow-600 bg-yellow-100' : 
                      type === 'success' ? 'check-circle text-green-600 bg-green-100' : 
                      'info text-blue-600 bg-blue-100';
    
    const notificationHTML = `
        <div class="notification-item p-4 hover:bg-gray-50 border-b border-gray-100" data-id="${id}">
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 ${iconClass.split(' ')[1]} rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-feather="${iconClass.split(' ')[0]}" class="w-4 h-4 ${iconClass.split(' ')[2]}"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${title}</p>
                    <p class="text-xs text-gray-500">${message}</p>
                    <p class="text-xs text-gray-400 mt-1">Just now</p>
                </div>
                <button onclick="removeNotification(${id})" class="text-gray-400 hover:text-gray-600">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    `;
    
    notificationList.insertAdjacentHTML('afterbegin', notificationHTML);
    
    // Re-initialize feather icons for the new notification
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    updateNotificationBadge();
}

// Close notification dropdown when clicking outside
document.addEventListener('click', function(event) {
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationButton = event.target.closest('button[onclick="toggleNotifications()"]');
    
    if (notificationDropdown && !notificationDropdown.contains(event.target) && !notificationButton) {
        notificationDropdown.classList.add('hidden');
    }
});

// Export functions for global use
window.LegalHR = {
    switchClient,
    toggleSidebar,
    toggleUserDropdown,
    showNotification,
    showModal,
    hideModal,
    printPage,
    exportTable,
    validateForm,
    toggleNotifications,
    removeNotification,
    markAllAsRead,
    addNotification,
    updateNotificationBadge
};
