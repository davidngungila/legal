<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200 z-20">
    <div class="flex items-center justify-between px-6 py-4">
        <!-- Left Section -->
        <div class="flex items-center space-x-4">
            <!-- Mobile Menu Toggle -->
            <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i data-feather="menu" class="w-6 h-6"></i>
            </button>
            
            <!-- Search Bar -->
            <div class="hidden md:flex items-center space-x-2 bg-gray-100 rounded-lg px-4 py-2 w-96">
                <i data-feather="search" class="w-5 h-5 text-gray-400"></i>
                <input type="text" placeholder="Search employees, documents, cases..." class="bg-transparent outline-none flex-1 text-sm">
            </div>
        </div>
        
        <!-- Right Section -->
        <div class="flex items-center space-x-4">
            <!-- Current Client Display -->
            @if($currentClient)
            <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                <i data-feather="briefcase" class="w-4 h-4 text-green-600"></i>
                <div class="text-sm">
                    <span class="text-xs text-gray-500">Current:</span>
                    <span class="font-medium text-green-800">{{ $currentClient->name }}</span>
                </div>
            </div>
            @endif
            
            <!-- Notifications -->
            <div class="relative">
                <button onclick="toggleNotifications()" class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-feather="bell" class="w-5 h-5 text-gray-600"></i>
                    <span id="notificationBadge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                </button>
                
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            <button onclick="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all as read</button>
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <!-- Notification Items -->
                        <div class="notification-item p-4 hover:bg-gray-50 border-b border-gray-100" data-id="1">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="alert-triangle" class="w-4 h-4 text-red-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Critical Case Alert</p>
                                    <p class="text-xs text-gray-500">Disciplinary case requires immediate attention</p>
                                    <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                                </div>
                                <button onclick="removeNotification(1)" class="text-gray-400 hover:text-gray-600">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="notification-item p-4 hover:bg-gray-50 border-b border-gray-100" data-id="2">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="alert-circle" class="w-4 h-4 text-yellow-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Pending Approval</p>
                                    <p class="text-xs text-gray-500">Termination case requires HR Admin approval</p>
                                    <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
                                </div>
                                <button onclick="removeNotification(2)" class="text-gray-400 hover:text-gray-600">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="notification-item p-4 hover:bg-gray-50 border-b border-gray-100" data-id="3">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="check-circle" class="w-4 h-4 text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Payroll Processed</p>
                                    <p class="text-xs text-gray-500">November 2024 payroll completed successfully</p>
                                    <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                                </div>
                                <button onclick="removeNotification(3)" class="text-gray-400 hover:text-gray-600">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 border-t border-gray-200">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">View all notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- User Menu -->
            <div class="relative">
                <button id="userButton" onclick="toggleUserDropdown()" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                        <span class="text-sm font-bold text-white">{{ substr(is_object($currentUser) ? $currentUser->name : $currentUser['name'], 0, 1) }}</span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-900">{{ is_object($currentUser) ? $currentUser->name : $currentUser['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ is_object($currentUser) ? $currentUser->email : $currentUser['email'] }}</p>
                    </div>
                    <i data-feather="chevron-down" class="w-4 h-4 text-gray-400"></i>
                </button>
                
                <!-- User Dropdown -->
                <div id="userDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50">
                    <!-- User Info Header -->
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-blue-50">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-sm font-bold text-white">{{ substr(is_object($currentUser) ? $currentUser->name : $currentUser['name'], 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ is_object($currentUser) ? $currentUser->name : $currentUser['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ is_object($currentUser) ? $currentUser->email : $currentUser['email'] }}</p>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ ucfirst(str_replace('_', ' ', is_object($currentUser) ? $currentUser->role : $currentUser['role'])) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Items -->
                    <div class="py-2">
                        <a href="{{ route('profile') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                <i data-feather="user" class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition-colors"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900">My Profile</span>
                                <p class="text-xs text-gray-500">Manage your account settings</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('settings') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                <i data-feather="settings" class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition-colors"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900">Settings</span>
                                <p class="text-xs text-gray-500">Preferences and privacy</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </a>
                        
                        <a href="{{ route('help') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 transition-colors group">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors">
                                <i data-feather="help-circle" class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition-colors"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900">Help & Support</span>
                                <p class="text-xs text-gray-500">Get help and documentation</p>
                            </div>
                            <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        </a>
                    </div>
                    
                    <!-- Divider -->
                    <div class="border-t border-gray-200"></div>
                    
                    <!-- Sign Out -->
                    <div class="py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center space-x-3 px-4 py-3 hover:bg-red-50 transition-colors w-full text-left group">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-red-100 transition-colors">
                                    <i data-feather="log-out" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition-colors"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-900 group-hover:text-red-600 transition-colors">Sign Out</span>
                                    <p class="text-xs text-gray-500">Logout from your account</p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Breadcrumb -->
    <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Home</a>
            @if(request()->segment(1) !== 'dashboard')
                <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                <span class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', request()->segment(1))) }}</span>
            @endif
        </nav>
    </div>
</header>
