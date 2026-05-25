
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
            <label class="text-[10px] uppercase tracking-tighter text-indigo-300 block mb-1">Current Client:</label>
            <select id="clientSelector" class="w-full bg-indigo-700/50 text-white border border-white/10 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-green-500" onchange="switchClient(this.value)">
                <option value="">Select Client...</option>
            </select>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="flex-1 p-3 overflow-y-auto">
        <ul class="space-y-0.5">
            <!-- General -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="home" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Organization -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="globe" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Organization</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('clients.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="users" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Clients</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Talent Acquisition -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="briefcase" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Talent Acquisition</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('job-vacancy.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('job-vacancy.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="search" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Recruitment</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hr-interview.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('hr-interview.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="message-square" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">HR Interview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('technical-interview.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('technical-interview.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="cpu" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Technical Interview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('onboarding.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('onboarding.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="user-check" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Onboarding</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Human Resources -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="users" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Human Resources</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('hris.dashboard') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('hris.dashboard') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="grid" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">HRIS Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('employees.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="user" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Employee Master</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee-registration.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('employee-registration.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="user-plus" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Employee Registration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employee-document.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('employee-document.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="file-text" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Employee Documents</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('social-records.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('social-records.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="book-open" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Social Records</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('induction-training.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('induction-training.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="clipboard" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Induction Training</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('personnel-id.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('personnel-id.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="credit-card" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Personnel ID</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('workflow.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('workflow.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="activity" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Workflow</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Contracts -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Contracts</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('contract-management.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('contract-management.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="layers" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Contract Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employment-contracts.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('employment-contracts.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="file-text" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Employment Contracts</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Time & Payroll -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="clock" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Time & Payroll</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('attendance.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="check-square" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payroll.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('payroll.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="credit-card" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Payroll Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('compensation.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('compensation.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="dollar-sign" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Compensation</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Performance & Training -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="trending-up" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Performance & Training</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('performance.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('performance.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="bar-chart" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Performance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('training.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('training.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="award" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Training</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Legal & Compliance -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="shield" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Legal & Compliance</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('compliance.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('compliance.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="shield" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Compliance & Legal</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('casemanagement.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('casemanagement.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="folder" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Case Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('discipline.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('discipline.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="alert-circle" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Employee Relations</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Analytics -->
            <li>
                <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('analytics.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="bar-chart-2" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Analytics</span>
                </a>
            </li>

            <!-- Self Service -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="user" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Self Service</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('selfservice.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.index') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="grid" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.leave') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.leave') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="calendar" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Leave</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.payslip') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.payslip') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="file" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Payslip</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.contract') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.contract') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="file-text" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Contract</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.complaint') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.complaint') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Complaint</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('selfservice.profile') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('selfservice.profile') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="settings" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Profile</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Documents -->
            <li>
                <a href="{{ route('documents.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 {{ request()->routeIs('documents.*') ? 'bg-white/10 backdrop-blur-sm' : '' }}">
                    <i data-feather="book" class="w-4 h-4"></i>
                    <span class="text-sm font-medium">Documents & Policies</span>
                </a>
            </li>
            
            <!-- Administration -->
            <li class="sidebar-dropdown">
                <button type="button" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-white/10 hover:backdrop-blur-sm transition-all duration-300 group">
                    <div class="flex items-center space-x-3">
                        <i data-feather="settings" class="w-4 h-4"></i>
                        <span class="text-sm font-medium">Administration</span>
                    </div>
                    <i data-feather="chevron-down" class="w-3.5 h-3.5 transition-transform duration-300 dropdown-arrow"></i>
                </button>
                <ul class="dropdown-menu mt-0.5 space-y-0.5 overflow-hidden transition-all duration-300 max-h-0 opacity-0 ml-4">
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('users.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="users" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user-registration.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('user-registration.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="user-plus" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">User Registration</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('roles.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="shield" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('permissions.index') }}" class="flex items-center space-x-3 px-3 py-1.5 rounded-lg hover:bg-white/10 transition-all duration-300 {{ request()->routeIs('permissions.*') ? 'text-white font-medium bg-white/5' : 'text-indigo-200' }}">
                            <i data-feather="key" class="w-3.5 h-3.5"></i>
                            <span class="text-xs">Permissions</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<script>
// Sidebar Dropdown Logic
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.sidebar-dropdown');
    
    dropdowns.forEach(dropdown => {
        const button = dropdown.querySelector('button');
        const menu = dropdown.querySelector('.dropdown-menu');
        const arrow = dropdown.querySelector('.dropdown-arrow');
        
        // Check if any child link is active
        const hasActiveChild = menu.querySelector('.text-white.font-medium') !== null;
        
        if (hasActiveChild) {
            menu.style.maxHeight = menu.scrollHeight + 'px';
            menu.style.opacity = '1';
            arrow.style.transform = 'rotate(180deg)';
            button.classList.add('bg-white/5');
        }

        button.addEventListener('click', () => {
            const isOpen = menu.style.maxHeight !== '0px' && menu.style.maxHeight !== '';
            
            // Close other dropdowns (optional - comment out if you want multiple open)
            /*
            dropdowns.forEach(other => {
                if (other !== dropdown) {
                    const otherMenu = other.querySelector('.dropdown-menu');
                    const otherArrow = other.querySelector('.dropdown-arrow');
                    const otherButton = other.querySelector('button');
                    otherMenu.style.maxHeight = '0px';
                    otherMenu.style.opacity = '0';
                    otherArrow.style.transform = 'rotate(0deg)';
                    otherButton.classList.remove('bg-white/5');
                }
            });
            */

            if (isOpen) {
                menu.style.maxHeight = '0px';
                menu.style.opacity = '0';
                arrow.style.transform = 'rotate(0deg)';
                button.classList.remove('bg-white/5');
            } else {
                menu.style.maxHeight = menu.scrollHeight + 'px';
                menu.style.opacity = '1';
                arrow.style.transform = 'rotate(180deg)';
                button.classList.add('bg-white/5');
            }
        });
    });
});

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
let availableClients = [];

// Load clients immediately from global data if available
(function() {
    function initClients() {
        if (window.allClients && window.allClients.length > 0) {
            availableClients = window.allClients;
            updateClientSelector();
        }
    }

    // Initialize immediately if data is already there
    initClients();

    // Also listen for DOMContentLoaded to ensure elements are ready
    document.addEventListener('DOMContentLoaded', function() {
        initClients();
    });
})();

// Update client selector dropdown
function updateClientSelector() {
    const selector = document.getElementById('clientSelector');
    if (!selector) return;
    
    // Get current client from global state
    const currentClient = typeof getCurrentClient === 'function' ? getCurrentClient() : null;
    const currentId = currentClient ? currentClient.id : null;
    
    selector.innerHTML = '';
    
    availableClients.forEach(client => {
        const option = document.createElement('option');
        option.value = client.id;
        option.textContent = client.name;
        option.selected = client.id == currentId;
        
        // Add visual indicator for current client
        if (client.id == currentId) {
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
