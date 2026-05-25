
<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-gradient-to-b from-[#040344] to-[#040344] text-white flex-shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:relative h-full z-30 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-[#1a1a3a] flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg">
                <img src="{{ asset('Orvion.png') }}" alt="Orvion Logo" class="w-10 h-10 object-contain">
            </div>
            <div>
                <h1 class="text-xl font-bold font-manrope">Orvion</h1>
                <p class="text-xs text-[#a0a0c0]">HR Management System</p>
            </div>
        </div>
        
        <!-- Client Selector -->
        <div class="mt-4">
            <label class="text-xs text-indigo-300 block mb-2">Current Client:</label>
            <select id="clientSelector" class="w-full bg-indigo-700 text-white rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" onchange="switchClient(this.value)">
                <option value="">Select Client...</option>
            </select>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            <!-- General -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">General</div>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="home" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Organization -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Organization</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('clients.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="globe" class="w-4 h-4"></i>
                            <span class="text-sm">Clients</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Talent Acquisition -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Talent Acquisition</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('job-vacancy.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('job-vacancy.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="briefcase" class="w-4 h-4"></i>
                            <span class="text-sm">Recruitment</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hr-interview.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('hr-interview.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="message-square" class="w-4 h-4"></i>
                            <span class="text-sm">HR Interview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('technical-interview.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('technical-interview.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="cpu" class="w-4 h-4"></i>
                            <span class="text-sm">Technical Interview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('onboarding.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('onboarding.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="user-check" class="w-4 h-4"></i>
                            <span class="text-sm">Onboarding</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Human Resources -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Human Resources</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('hris.dashboard') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('hris.dashboard') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="grid" class="w-4 h-4"></i>
                            <span class="text-sm">HRIS Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('employees.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="users" class="w-4 h-4"></i>
                            <span class="text-sm">Employee Master</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee-registration.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('employee-registration.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                            <span class="text-sm">Employee Registration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee-document.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('employee-document.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="file" class="w-4 h-4"></i>
                            <span class="text-sm">Employee Documents</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('social-records.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('social-records.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="book" class="w-4 h-4"></i>
                            <span class="text-sm">Social Records</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('induction-training.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('induction-training.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="clipboard" class="w-4 h-4"></i>
                            <span class="text-sm">Induction Training</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('personnel-id.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('personnel-id.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="credit-card" class="w-4 h-4"></i>
                            <span class="text-sm">Personnel ID</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('workflow.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('workflow.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="repeat" class="w-4 h-4"></i>
                            <span class="text-sm">Workflow</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Contracts -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Contracts</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('contract-management.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('contract-management.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                            <span class="text-sm">Contract Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employment-contracts.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('employment-contracts.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="briefcase" class="w-4 h-4"></i>
                            <span class="text-sm">Employment Contracts</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Time & Payroll -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Time & Payroll</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('attendance.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="clock" class="w-4 h-4"></i>
                            <span class="text-sm">Attendance & Timesheet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payroll.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('payroll.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="credit-card" class="w-4 h-4"></i>
                            <span class="text-sm">Payroll Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('compensation.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('compensation.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="dollar-sign" class="w-4 h-4"></i>
                            <span class="text-sm">Compensation & Benefits</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Performance & Training -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Performance & Training</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('performance.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('performance.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="trending-up" class="w-4 h-4"></i>
                            <span class="text-sm">Performance Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('training.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('training.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="book-open" class="w-4 h-4"></i>
                            <span class="text-sm">Training</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Legal & Compliance -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Legal & Compliance</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('compliance.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('compliance.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="shield" class="w-4 h-4"></i>
                            <span class="text-sm">Compliance & Legal</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('casemanagement.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('casemanagement.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="folder" class="w-4 h-4"></i>
                            <span class="text-sm">Case Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('discipline.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('discipline.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="alert-triangle" class="w-4 h-4"></i>
                            <span class="text-sm">Employee Relations</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Analytics -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Insights</div>
                <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('analytics.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                    <span>Analytics</span>
                </a>
            </li>

            <!-- Self Service -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Self Service</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('selfservice.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.index') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="user" class="w-4 h-4"></i>
                            <span class="text-sm">Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.leave') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.leave') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                            <span class="text-sm">Leave</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.payslip') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.payslip') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="file" class="w-4 h-4"></i>
                            <span class="text-sm">Payslip</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.contract') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.contract') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                            <span class="text-sm">Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.complaint') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.complaint') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="message-circle" class="w-4 h-4"></i>
                            <span class="text-sm">Complaint</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.profile') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('selfservice.profile') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="settings" class="w-4 h-4"></i>
                            <span class="text-sm">Profile</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Resources -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Resources</div>
                <a href="{{ route('documents.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('documents.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="folder-open" class="w-5 h-5"></i>
                    <span>Documents & Policies</span>
                </a>
            </li>
            
            <!-- Administration -->
            <li>
                <div class="px-4 py-2 text-xs text-[#a0a0c0] font-semibold uppercase tracking-wider">Administration</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('users.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="users" class="w-4 h-4"></i>
                            <span class="text-sm">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user-registration.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('user-registration.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                            <span class="text-sm">User Registration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('roles.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="shield" class="w-4 h-4"></i>
                            <span class="text-sm">Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('permissions.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('permissions.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                            <i data-feather="key" class="w-4 h-4"></i>
                            <span class="text-sm">Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<script>
// Immediate icon replacement for sidebar to prevent flicker
if (typeof feather !== 'undefined') {
    feather.replace({ 'class': 'sidebar-icon' });
}
</script>

<script>
// Sidebar scroll position preservation
(function() {
    const SCROLL_KEY = 'sidebar-scroll';
    
    function restoreScroll() {
        const sidebarNav = document.querySelector('aside#sidebar nav');
        if (sidebarNav) {
            const scrollPosition = sessionStorage.getItem(SCROLL_KEY);
            if (scrollPosition) {
                sidebarNav.scrollTop = parseInt(scrollPosition, 10);
            }
        }
    }

    function saveScroll() {
        const sidebarNav = document.querySelector('aside#sidebar nav');
        if (sidebarNav) {
            sessionStorage.setItem(SCROLL_KEY, sidebarNav.scrollTop);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebarNav = document.querySelector('aside#sidebar nav');
        if (sidebarNav) {
            // Restore immediately
            restoreScroll();
            
            // Also restore after a short delay to account for icon loading or layout shifts
            setTimeout(restoreScroll, 100);
            setTimeout(restoreScroll, 500);

            // Save on scroll
            sidebarNav.addEventListener('scroll', saveScroll, { passive: true });
            
            // Save when clicking any link in the sidebar
            sidebarNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', saveScroll);
            });
        }
    });

    // Final save before page unload
    window.addEventListener('beforeunload', saveScroll);
})();

// Client switching functionality
let currentClientId = null;
let availableClients = [];

// Load clients immediately from global data if available
(function() {
    function initClients() {
        if (window.allClients && window.allClients.length > 0) {
            availableClients = window.allClients;
            
            // Try to get current client from live data or storage
            if (window.liveClientData) {
                currentClientId = window.liveClientData.id;
            } else {
                currentClientId = sessionStorage.getItem('selectedClientId') || 
                                localStorage.getItem('selectedClientId') || 
                                (availableClients.length > 0 ? availableClients[0].id : null);
            }
            
            updateClientSelector();
        }
    }

    // Initialize immediately if data is already there
    initClients();

    // Also listen for DOMContentLoaded to ensure elements are ready
    document.addEventListener('DOMContentLoaded', function() {
        initClients();
        
        // Background refresh if needed, but don't block
        loadAvailableClients();
        loadCurrentClient();
    });
})();

// Load available clients (background refresh)
async function loadAvailableClients() {
    try {
        const response = await fetch('/api/client-switch/available', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            availableClients = data.clients;
            updateClientSelector();
        }
    } catch (error) {
        console.error('Error loading clients:', error);
    }
}

// Load current client
async function loadCurrentClient() {
    try {
        const response = await fetch('/api/client-switch/current', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.client) {
            currentClientId = data.client.id;
            updateClientSelector();
        }
    } catch (error) {
        console.error('Error loading current client:', error);
    }
}

// Update client selector dropdown
function updateClientSelector() {
    const selector = document.getElementById('clientSelector');
    if (!selector) return;
    
    selector.innerHTML = '';
    
    availableClients.forEach(client => {
        const option = document.createElement('option');
        option.value = client.id;
        option.textContent = client.name;
        option.selected = client.id == currentClientId;
        
        // Add visual indicator for current client
        if (client.id == currentClientId) {
            option.textContent += ' (Current)';
        }
        
        // Add status indicator
        if (client.status !== 'active') {
            option.textContent += ` [${client.status}]`;
            option.disabled = client.status !== 'active';
        }
        
        selector.appendChild(option);
    });
}

// Switch client function
async function switchClient(clientId) {
    if (!clientId || clientId == currentClientId) {
        return;
    }
    
    // Show loading state
    showNotification('Switching client...', 'info');
    
    try {
        const response = await fetch('/api/client-switch/switch', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                client_id: clientId
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentClientId = clientId;
            showNotification(data.message, 'success');
            
            // Clear any cached data and reload the page to refresh data for the new client
            setTimeout(() => {
                if (typeof localStorage !== 'undefined') {
                    localStorage.clear();
                }
                window.location.href = window.location.href;
            }, 1000);
        } else {
            showNotification('Failed to switch client', 'error');
        }
    } catch (error) {
        console.error('Error switching client:', error);
        showNotification('Error switching client', 'error');
    }
}

// Notification helper function
function showNotification(message, type = 'info') {
    // Create notification element
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
</script>
