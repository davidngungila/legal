<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-gradient-to-b from-indigo-900 to-indigo-800 text-white flex-shrink-0 transition-transform duration-300 ease-in-out lg:translate-x-0 -translate-x-full fixed lg:relative h-full z-30 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-indigo-700 flex-shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                <i data-feather="briefcase" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-manrope">LegalHR</h1>
                <p class="text-xs text-indigo-300">Tanzania HR System</p>
            </div>
        </div>
    </div>
    
    <!-- Client Selector (for Super Admin and HR Admin) -->
    @if(isset($currentUser) && $currentUser && (
        (is_object($currentUser) && $currentUser->roles()->where('name', 'super_admin')->exists() || $currentUser->roles()->where('name', 'lead_hr_admin')->exists()) ||
        (is_array($currentUser) && isset($currentUser['roles']) && 
            (in_array('super_admin', array_column($currentUser['roles'], 'name')) || in_array('lead_hr_admin', array_column($currentUser['roles'], 'name'))))
    ))
    <div class="p-4 border-b border-indigo-700 flex-shrink-0">
        <label class="text-xs text-indigo-300 block mb-2">Current Client:</label>
        <select class="w-full bg-indigo-700 text-white rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" onchange="switchClient(this.value)">
            <option value="1">ABC Manufacturing Ltd</option>
            <option value="2">XYZ Construction Co</option>
            <option value="3">Tanzania Mining Corp</option>
            <option value="4">East Africa Logistics</option>
        </select>
    </div>
    @endif
    
    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="home" class="w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- Organization & Workforce -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Organization</div>
                <a href="{{ route('organization.setup') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('organization.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="briefcase" class="w-5 h-5"></i>
                    <span>Company Setup</span>
                </a>
            </li>
            
            <!-- Employee Management -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Employee Management</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('employees.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="users" class="w-4 h-4"></i>
                            <span class="text-sm">Employee Master</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('recruitment.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('recruitment.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="user-plus" class="w-4 h-4"></i>
                            <span class="text-sm">Recruitment</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('onboarding.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('onboarding.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="user-check" class="w-4 h-4"></i>
                            <span class="text-sm">Onboarding</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Time & Attendance -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Time & Attendance</div>
                <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('attendance.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="clock" class="w-5 h-5"></i>
                    <span>Attendance & Timesheet</span>
                </a>
            </li>
            
            <!-- Payroll -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Payroll</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('payroll.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('payroll.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="credit-card" class="w-4 h-4"></i>
                            <span class="text-sm">Payroll Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('compensation.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('compensation.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="dollar-sign" class="w-4 h-4"></i>
                            <span class="text-sm">Compensation & Benefits</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Performance -->
            <li>
                <a href="{{ route('performance.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('performance.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="trending-up" class="w-5 h-5"></i>
                    <span>Performance Management</span>
                </a>
            </li>
            
            <!-- Employee Relations (Critical) -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Critical Module</div>
                <a href="{{ route('discipline.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('discipline.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="alert-triangle" class="w-5 h-5"></i>
                    <span>Employee Relations & Discipline</span>
                </a>
            </li>
            
            <!-- Compliance -->
            <li>
                <a href="{{ route('compliance.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('compliance.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="shield" class="w-5 h-5"></i>
                    <span>Compliance & Legal</span>
                </a>
            </li>
            
            <!-- Training -->
            <li>
                <a href="{{ route('training.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('training.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="book-open" class="w-5 h-5"></i>
                    <span>Training & Development</span>
                </a>
            </li>
            
            <!-- Analytics -->
            <li>
                <a href="{{ route('analytics.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('analytics.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="bar-chart-2" class="w-5 h-5"></i>
                    <span>Workforce Analytics</span>
                </a>
            </li>
            
            <!-- Employee Self Service -->
            <li>
                <a href="{{ route('selfservice') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('selfservice.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="user" class="w-5 h-5"></i>
                    <span>Employee Self Service</span>
                </a>
            </li>
            
            <!-- Case Management -->
            <li>
                <a href="{{ route('casemanagement.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('casemanagement.*') ? 'bg-indigo-700' : '' }}">
                    <i data-feather="folder" class="w-5 h-5"></i>
                    <span>Case Management</span>
                </a>
            </li>
            
            <!-- Settings -->
            <li>
                <div class="px-4 py-2 text-xs text-indigo-300 font-semibold uppercase tracking-wider">Administration</div>
                <ul class="space-y-1 ml-4">
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="settings" class="w-4 h-4"></i>
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('roles.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('roles.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="shield" class="w-4 h-4"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('permissions.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('permissions.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="key" class="w-4 h-4"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}" class="flex items-center space-x-3 px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors {{ request()->routeIs('clients.*') ? 'bg-indigo-700' : '' }}">
                            <i data-feather="briefcase" class="w-4 h-4"></i>
                            <span>Clients</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
