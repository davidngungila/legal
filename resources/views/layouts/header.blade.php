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
            <!-- Client Switcher Dropdown (only for super admins) -->
            @if($currentUser && $currentUser->hasRole('super_admin'))
            <div class="relative">
                <button id="clientSwitcherButton" onclick="toggleClientSwitcher()" class="flex items-center space-x-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                    <i data-feather="briefcase" class="w-4 h-4 text-green-600"></i>
                    <div class="text-sm">
                        <span class="text-xs text-gray-500">Client:</span>
                        <span class="font-medium text-green-800">{{ $currentClient ? $currentClient->name : 'Select Client' }}</span>
                    </div>
                    <i data-feather="chevron-down" class="w-4 h-4 text-green-600"></i>
                </button>
                
                <!-- Client Dropdown -->
                <div id="clientSwitcherDropdown" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 z-50 max-h-96 overflow-y-auto">
                    <div class="p-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900">Select Client</h3>
                    </div>
                    <div class="py-1">
                        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
                        <button onclick="switchClient({{ $client->id }})" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition-colors flex items-center space-x-3 {{ $currentClient && $currentClient->id == $client->id ? 'bg-green-50 text-green-800' : '' }}">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="building" class="w-4 h-4 text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $client->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $client->email }}</p>
                            </div>
                            @if($currentClient && $currentClient->id == $client->id)
                            <i data-feather="check" class="w-4 h-4 text-green-600 flex-shrink-0"></i>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @elseif($currentClient)
            <!-- Current Client Display (non-admins) -->
            <div class="hidden md:flex items-center space-x-2 px-3 py-2 bg-green-50 border border-green-200 rounded-lg">
                <i data-feather="briefcase" class="w-4 h-4 text-green-600"></i>
                <div class="text-sm">
                    <span class="font-medium text-green-800">{{ $currentClient->name }}</span>
                </div>
            </div>
            @endif
            
            <!-- Notifications -->
            <div class="relative">
                <button id="notificationButton" onclick="toggleNotifications()" class="relative p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i data-feather="bell" class="w-5 h-5 text-gray-600"></i>
                    @if($notificationCount > 0)
                    <span id="notificationBadge" class="absolute -top-1 -right-1 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1">{{ $notificationCount }}</span>
                    @else
                    <span id="notificationBadge" style="display:none" class="absolute -top-1 -right-1 min-w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center px-1">0</span>
                    @endif
                </button>
                
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                            @if($notificationCount > 0)
                            <button onclick="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">Mark all as read</button>
                            @endif
                        </div>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($notifications as $notification)
                        <a href="{{ $notification['link'] }}" class="block notification-item p-4 hover:bg-gray-50 border-b border-gray-100" data-id="{{ $loop->iteration }}">
                            <div class="flex items-start space-x-3">
                                <div class="w-8 h-8 bg-{{ $notification['color'] }}-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="{{ $notification['icon'] }}" class="w-4 h-4 text-{{ $notification['color'] }}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $notification['message'] }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification['time'] }}</p>
                                </div>
                                <button onclick="event.preventDefault(); event.stopPropagation(); removeNotification({{ $loop->iteration }})" class="text-gray-400 hover:text-gray-600">
                                    <i data-feather="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center">
                            <i data-feather="bell-off" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                            <p class="text-sm font-medium text-gray-900">You're all caught up</p>
                            <p class="text-xs text-gray-500 mt-1">No notifications for the selected client.</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="p-3 border-t border-gray-200">
                        <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View all notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- User Menu -->
            <div class="relative">
                <button id="userButton" onclick="toggleUserDropdown()" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    @if(is_object($currentUser) && $currentUser->profile_photo)
                        <div class="w-8 h-8 rounded-full overflow-hidden shadow-sm">
                            <img src="{{ Storage::url($currentUser->profile_photo) }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                            <span class="text-sm font-bold text-white">{{ substr(is_object($currentUser) ? $currentUser->name : $currentUser['name'], 0, 1) }}</span>
                        </div>
                    @endif
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
                            @if(is_object($currentUser) && $currentUser->profile_photo)
                                <div class="w-10 h-10 rounded-full overflow-hidden shadow-sm">
                                    <img src="{{ Storage::url($currentUser->profile_photo) }}" alt="{{ $currentUser->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-sm">
                                    <span class="text-sm font-bold text-white">{{ substr(is_object($currentUser) ? $currentUser->name : $currentUser['name'], 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ is_object($currentUser) ? $currentUser->name : $currentUser['name'] }}</p>
                                <p class="text-sm text-gray-600">{{ is_object($currentUser) ? $currentUser->email : $currentUser['email'] }}</p>
                                <div class="flex items-center mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ ucfirst(str_replace('_', ' ', is_object($currentUser) ? (string) ($currentUser->roles->first()->name ?? '') : (string) ($currentUser['role'] ?? ''))) }}
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
                        
                        <a href="{{ route('settings.index') }}" class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 transition-colors group">
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
