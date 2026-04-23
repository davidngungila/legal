@extends('layouts.app')

@section('title', 'Help & Support - LegalHR Tanzania')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Help & Support</h1>
                <p class="text-gray-600 mt-2">Get comprehensive help and support for LegalHR Tanzania</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                    <i data-feather="phone" class="w-4 h-4 mr-2"></i>
                    Call Support
                </button>
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i data-feather="message-circle" class="w-4 h-4 mr-2"></i>
                    Live Chat
                </button>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-8 mb-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">How can we help you today?</h2>
                <p class="text-indigo-100">Search for help articles, tutorials, and documentation</p>
            </div>
            <div class="relative">
                <input type="text" id="help-search" placeholder="Search for help articles, tutorials, and more..." 
                       class="w-full px-6 py-4 text-lg rounded-lg border-0 focus:outline-none focus:ring-4 focus:ring-white/30">
                <i data-feather="search" class="w-6 h-6 text-gray-400 absolute left-4 top-4"></i>
                <button onclick="searchHelp()" class="absolute right-2 top-2 px-6 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Search
                </button>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 justify-center">
                <span class="text-sm text-indigo-100">Popular searches:</span>
                <button onclick="quickSearch('Payroll setup')" class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Payroll setup</button>
                <button onclick="quickSearch('Leave management')" class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Leave management</button>
                <button onclick="quickSearch('Employee onboarding')" class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Employee onboarding</button>
                <button onclick="quickSearch('Compliance reports')" class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Compliance reports</button>
            </div>
        </div>
    </div>

    <!-- Support Tickets Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Tickets</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $ticketStats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-feather="help-circle" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Open Tickets</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $ticketStats['open'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $ticketStats['in_progress'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i data-feather="loader" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Resolved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $ticketStats['resolved'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Support Tickets -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Support Tickets</h3>
            <button onclick="showCreateTicketModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                Create Ticket
            </button>
        </div>
        @if($tickets->count() > 0)
        <div class="space-y-4">
            @foreach($tickets as $ticket)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="viewTicket('{{ $ticket->ticket_number }}')">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="px-2 py-1 bg-{{ $ticket->getStatusColor() }}-100 text-{{ $ticket->getStatusColor() }}-800 text-xs font-semibold rounded">
                                {{ $ticket->status }}
                            </span>
                            <span class="px-2 py-1 bg-{{ $ticket->getPriorityColor() }}-100 text-{{ $ticket->getPriorityColor() }}-800 text-xs font-semibold rounded">
                                {{ $ticket->priority }}
                            </span>
                            <span class="text-sm text-gray-500">#{{ $ticket->ticket_number }}</span>
                        </div>
                        <h4 class="font-medium text-gray-900 mb-1">{{ $ticket->subject }}</h4>
                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($ticket->description, 100) }}</p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span>{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
                            <span>{{ $ticket->category }}</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <i data-feather="chevron-right" class="w-5 h-5 text-gray-400"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i data-feather="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <p class="text-gray-600">No support tickets yet. Create your first ticket to get help!</p>
        </div>
        @endif
    </div>

    <!-- Quick Help Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer group">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-feather="users" class="w-8 h-8 text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2 text-lg">Employee Management</h3>
            <p class="text-sm text-gray-600 mb-4">Add, edit, and manage employee records efficiently</p>
            <div class="space-y-2">
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Adding new employees</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Employee profiles</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Department management</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer group">
            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-feather="credit-card" class="w-8 h-8 text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2 text-lg">Payroll</h3>
            <p class="text-sm text-gray-600 mb-4">Process payroll and manage compensation with ease</p>
            <div class="space-y-2">
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Running payroll</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• PAYE calculations</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Payslip generation</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer group">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-feather="shield" class="w-8 h-8 text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2 text-lg">Compliance</h3>
            <p class="text-sm text-gray-600 mb-4">Stay compliant with Tanzania labor laws</p>
            <div class="space-y-2">
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Labour Act compliance</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• NSSF contributions</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Work permits</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer group">
            <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i data-feather="settings" class="w-8 h-8 text-white"></i>
            </div>
            <h3 class="font-bold text-gray-900 mb-2 text-lg">System Setup</h3>
            <p class="text-sm text-gray-600 mb-4">Configure system settings and preferences</p>
            <div class="space-y-2">
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Company setup</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• User management</a>
                <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800 font-medium">• Role permissions</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Help Articles -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Popular Help Articles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Popular Help Articles</h3>
                    <p class="text-sm text-gray-600 mt-1">Most viewed and helpful articles</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach([
                            ['title' => 'Getting Started with LegalHR Tanzania', 'category' => 'Getting Started', 'views' => 1245, 'time' => '5 min read', 'icon' => 'book-open', 'color' => 'blue'],
                            ['title' => 'Complete Payroll Processing Guide', 'category' => 'Payroll', 'views' => 892, 'time' => '8 min read', 'icon' => 'credit-card', 'color' => 'green'],
                            ['title' => 'Tanzania Labour Act Compliance Checklist', 'category' => 'Compliance', 'views' => 756, 'time' => '6 min read', 'icon' => 'shield', 'color' => 'purple'],
                            ['title' => 'Employee Onboarding Process', 'category' => 'Employee Management', 'views' => 634, 'time' => '4 min read', 'icon' => 'user-plus', 'color' => 'indigo'],
                            ['title' => 'Setting Up Company Structure', 'category' => 'System Setup', 'views' => 523, 'time' => '7 min read', 'icon' => 'settings', 'color' => 'yellow'],
                            ['title' => 'Managing Leave Requests', 'category' => 'Leave Management', 'views' => 412, 'time' => '5 min read', 'icon' => 'calendar', 'color' => 'orange']
                        ] as $article)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200 cursor-pointer group">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-{{ $article['color'] }}-100 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i data-feather="{{ $article['icon'] }}" class="w-6 h-6 text-{{ $article['color'] }}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $article['title'] }}</h4>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                        <span class="px-2 py-1 bg-{{ $article['color'] }}-100 text-{{ $article['color'] }}-800 text-xs font-semibold rounded-full">{{ $article['category'] }}</span>
                                        <span>{{ $article['views'] }} views</span>
                                        <span>{{ $article['time'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors"></i>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Video Tutorials -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Video Tutorials</h3>
                    <p class="text-sm text-gray-600 mt-1">Step-by-step video guides for common tasks</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach([
                            ['title' => 'System Overview', 'duration' => '10:23', 'thumbnail' => 'overview', 'level' => 'Beginner'],
                            ['title' => 'Payroll Setup', 'duration' => '15:45', 'thumbnail' => 'payroll', 'level' => 'Intermediate'],
                            ['title' => 'Employee Management', 'duration' => '12:30', 'thumbnail' => 'employees', 'level' => 'Beginner'],
                            ['title' => 'Reporting Features', 'duration' => '8:15', 'thumbnail' => 'reports', 'level' => 'Advanced']
                        ] as $video)
                        <div class="group cursor-pointer">
                            <div class="relative bg-gray-200 rounded-xl overflow-hidden mb-4 aspect-w-16 aspect-h-9">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                    <i data-feather="play-circle" class="w-16 h-16 text-white group-hover:scale-110 transition-transform"></i>
                                </div>
                                <div class="absolute bottom-3 right-3 bg-black bg-opacity-75 text-white px-3 py-1 rounded-lg text-sm font-medium">
                                    {{ $video['duration'] }}
                                </div>
                                <div class="absolute top-3 left-3">
                                    <span class="px-2 py-1 bg-white/90 text-gray-800 text-xs font-semibold rounded-full">
                                        {{ $video['level'] }}
                                    </span>
                                </div>
                            </div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $video['title'] }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $video['duration'] }} • {{ $video['level'] }} level</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Options -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Contact Support -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Support</h3>
                    <p class="text-sm text-gray-600 mt-1">Multiple ways to get help</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i data-feather="phone" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Phone Support</p>
                            <p class="text-sm text-gray-600">+255 22 123 4567</p>
                            <p class="text-xs text-gray-500">Mon-Fri, 8AM-5PM EAT</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i data-feather="mail" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Email Support</p>
                            <p class="text-sm text-gray-600">support@legalhr.co.tz</p>
                            <p class="text-xs text-gray-500">24-48 hour response</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i data-feather="message-circle" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Live Chat</p>
                            <p class="text-sm text-gray-600">Available now</p>
                            <p class="text-xs text-gray-500">Average wait: 2 min</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                    <p class="text-sm text-gray-600 mt-1">Real-time system health monitoring</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">All Systems</span>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Operational</span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Web Application</span>
                                </div>
                                <span class="text-xs text-green-600">Healthy</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Database</span>
                                </div>
                                <span class="text-xs text-green-600">Healthy</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">API Services</span>
                                </div>
                                <span class="text-xs text-green-600">Healthy</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm text-gray-700">Email Services</span>
                                </div>
                                <span class="text-xs text-green-600">Healthy</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">Last updated: 2 minutes ago</p>
                    </div>
                </div>
            </div>

            <!-- FAQ -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Frequently Asked Questions</h3>
                    <p class="text-sm text-gray-600 mt-1">Quick answers to common questions</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach([
                            ['q' => 'How do I reset my password?', 'a' => 'Click on "Forgot Password" on the login page and follow the instructions.'],
                            ['q' => 'What browsers are supported?', 'a' => 'We support Chrome, Firefox, Safari, and Edge latest versions.'],
                            ['q' => 'How often is data backed up?', 'a' => 'Your data is backed up automatically every 24 hours.'],
                            ['q' => 'Can I export my data?', 'a' => 'Yes, you can export your data from the Settings page at any time.']
                        ] as $faq)
                        <div class="border-b border-gray-200 pb-3 last:border-b-0">
                            <button class="w-full text-left flex items-center justify-between" onclick="toggleFAQ(this)">
                                <span class="text-sm font-medium text-gray-900">{{ $faq['q'] }}</span>
                                <i data-feather="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </button>
                            <div class="hidden mt-2 text-sm text-gray-600 p-3 bg-gray-50 rounded">
                                {{ $faq['a'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Training & Resources -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mt-8">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Training & Resources</h3>
            <p class="text-sm text-gray-600 mt-1">Enhance your skills with our learning resources</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <i data-feather="book-open" class="w-8 h-8 text-white"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">User Manual</h4>
                    <p class="text-sm text-gray-600 mb-4">Complete guide to using LegalHR Tanzania</p>
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Download PDF →</button>
                </div>

                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mb-4">
                        <i data-feather="calendar" class="w-8 h-8 text-white"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">Training Sessions</h4>
                    <p class="text-sm text-gray-600 mb-4">Join our weekly training webinars</p>
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Schedule →</button>
                </div>

                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 cursor-pointer">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4">
                        <i data-feather="users" class="w-8 h-8 text-white"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 mb-2">Community Forum</h4>
                    <p class="text-sm text-gray-600 mb-4">Connect with other LegalHR users</p>
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Join Forum →</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
@endsection

@push('scripts')
<script>
// Help & Support Management System
class HelpManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('help-search');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.searchHelp();
                }
            });
        }

        // Live chat button
        const liveChatBtn = document.querySelector('button:has(.feather-message-circle)');
        if (liveChatBtn) {
            liveChatBtn.addEventListener('click', () => this.startLiveChat());
        }

        // Call support button
        const callSupportBtn = document.querySelector('button:has(.feather-phone)');
        if (callSupportBtn) {
            callSupportBtn.addEventListener('click', () => this.callSupport());
        }
    }

    async searchHelp() {
        const query = document.getElementById('help-search').value.trim();
        
        if (!query) {
            this.showNotification('Please enter a search term', 'warning');
            return;
        }

        try {
            this.showLoading('Searching...');
            
            const response = await fetch('/help/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ q: query })
            });

            const result = await response.json();

            if (result.success) {
                this.displaySearchResults(result.articles, result.query);
            } else {
                this.showNotification(result.message || 'Search failed', 'error');
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showNotification('An error occurred while searching', 'error');
        } finally {
            this.hideLoading();
        }
    }

    quickSearch(query) {
        document.getElementById('help-search').value = query;
        this.searchHelp();
    }

    displaySearchResults(articles, query) {
        // Create search results modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Search Results</h3>
                            <p class="text-sm text-gray-600 mt-1">Found ${articles.length} results for "${query}"</p>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    ${articles.length > 0 ? this.renderSearchResults(articles) : this.renderNoResults(query)}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        feather.replace();
    }

    renderSearchResults(articles) {
        return `
            <div class="space-y-4">
                ${articles.map(article => `
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="helpManager.viewArticle(${article.id})">
                        <div class="flex items-start space-x-4">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <i data-feather="file-text" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-1">${article.title}</h4>
                                <p class="text-sm text-gray-600 mb-2">${article.description}</p>
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="px-2 py-1 bg-gray-100 rounded">${article.category}</span>
                                    <span>${article.views} views</span>
                                    <span>Updated ${article.last_updated}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    renderNoResults(query) {
        return `
            <div class="text-center py-8">
                <i data-feather="search" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600 mb-4">No results found for "${query}"</p>
                <p class="text-sm text-gray-500 mb-4">Try searching with different keywords or browse our help categories below.</p>
                <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Browse Help Articles
                </button>
            </div>
        `;
    }

    async viewArticle(articleId) {
        try {
            const response = await fetch(`/help/article/${articleId}`);
            const result = await response.json();

            if (result.success) {
                this.displayArticle(result.article);
            } else {
                this.showNotification('Article not found', 'error');
            }
        } catch (error) {
            console.error('Article view error:', error);
            this.showNotification('Failed to load article', 'error');
        }
    }

    displayArticle(article) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">${article.title}</h3>
                            <p class="text-sm text-gray-600 mt-1">${article.category} · ${article.views} views</p>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        <p>${article.content}</p>
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-500">Last updated: ${article.last_updated}</p>
                            <div class="flex space-x-2">
                                <button class="px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm">
                                    <i data-feather="thumbs-up" class="w-4 h-4 mr-1"></i>
                                    Helpful
                                </button>
                                <button class="px-3 py-1 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm">
                                    <i data-feather="share" class="w-4 h-4 mr-1"></i>
                                    Share
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        feather.replace();
    }

    showCreateTicketModal() {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">Create Support Ticket</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                <form id="create-ticket-form" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                            <input type="text" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select category</option>
                                    <option value="technical">Technical Issues</option>
                                    <option value="billing">Billing & Payments</option>
                                    <option value="payroll">Payroll Issues</option>
                                    <option value="compliance">Compliance Questions</option>
                                    <option value="feature_request">Feature Request</option>
                                    <option value="bug_report">Bug Report</option>
                                    <option value="account">Account Issues</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                                <select name="priority" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                            <textarea name="description" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Please describe your issue in detail..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Attachments (optional)</label>
                            <input type="file" name="attachments" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Maximum 5 files, 2MB each</p>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Create Ticket
                        </button>
                    </div>
                </form>
            </div>
        `;
        
        document.body.appendChild(modal);
        feather.replace();
        
        // Handle form submission
        modal.querySelector('#create-ticket-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.createTicket(new FormData(e.target));
        });
    }

    async createTicket(formData) {
        try {
            this.showLoading('Creating ticket...');
            
            const response = await fetch('/help/ticket', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Support ticket created successfully!', 'success');
                document.querySelector('.fixed').remove(); // Close modal
                // Refresh the page to show the new ticket
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Failed to create ticket', 'error');
            }
        } catch (error) {
            console.error('Create ticket error:', error);
            this.showNotification('An error occurred while creating ticket', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async viewTicket(ticketNumber) {
        try {
            const response = await fetch(`/help/ticket/${ticketNumber}`);
            const result = await response.json();

            if (result.success) {
                this.displayTicket(result.ticket);
            } else {
                this.showNotification('Ticket not found', 'error');
            }
        } catch (error) {
            console.error('Ticket view error:', error);
            this.showNotification('Failed to load ticket', 'error');
        }
    }

    displayTicket(ticket) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">${ticket.subject}</h3>
                            <p class="text-sm text-gray-600 mt-1">Ticket #${ticket.ticket_number}</p>
                        </div>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <span class="px-2 py-1 bg-${ticket.getStatusColor()}-100 text-${ticket.getStatusColor()}-800 text-xs font-semibold rounded">
                                ${ticket.status}
                            </span>
                            <span class="px-2 py-1 bg-${ticket.getPriorityColor()}-100 text-${ticket.getPriorityColor()}-800 text-xs font-semibold rounded">
                                ${ticket.priority}
                            </span>
                            <span class="text-sm text-gray-500">${ticket.category}</span>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900">${ticket.description}</p>
                        </div>
                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Responses</h4>
                            ${ticket.responses.length > 0 ? this.renderTicketResponses(ticket.responses) : '<p class="text-gray-500">No responses yet.</p>'}
                        </div>
                    </div>
                    <div class="mt-6 pt-6 border-t">
                        <form id="add-response-form" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Add Response</label>
                                <textarea name="message" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Type your response..."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Add Response
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        feather.replace();
        
        // Handle response form submission
        modal.querySelector('#add-response-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.addResponse(ticket.ticket_number, new FormData(e.target));
        });
    }

    renderTicketResponses(responses) {
        return `
            <div class="space-y-3">
                ${responses.map(response => `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">${response.user.name}</span>
                            <span class="text-xs text-gray-500">${response.getFormattedTime()}</span>
                        </div>
                        <p class="text-gray-700 text-sm">${response.message}</p>
                        <span class="text-xs text-gray-500 mt-1">${response.getResponseType()}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    async addResponse(ticketNumber, formData) {
        try {
            this.showLoading('Adding response...');
            
            const response = await fetch(`/help/ticket/${ticketNumber}/response`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Response added successfully!', 'success');
                // Refresh the ticket view
                this.viewTicket(ticketNumber);
            } else {
                this.showNotification(result.message || 'Failed to add response', 'error');
            }
        } catch (error) {
            console.error('Add response error:', error);
            this.showNotification('An error occurred while adding response', 'error');
        } finally {
            this.hideLoading();
        }
    }

    startLiveChat() {
        this.showNotification('Live chat is coming soon! For now, please create a support ticket.', 'info');
    }

    callSupport() {
        // Create contact modal
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-xl max-w-md w-full">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900">Contact Support</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <i data-feather="x" class="w-6 h-6"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <i data-feather="phone" class="w-5 h-5 text-indigo-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Phone Support</p>
                                <p class="text-sm text-gray-600">+255 22 123 4567</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i data-feather="mail" class="w-5 h-5 text-indigo-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">Email Support</p>
                                <p class="text-sm text-gray-600">support@legalhr.co.tz</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <i data-feather="message-circle" class="w-5 h-5 text-indigo-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">WhatsApp Support</p>
                                <p class="text-sm text-gray-600">+255 754 123 456</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t">
                            <p class="text-sm text-gray-600"><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 6:00 PM EAT</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        feather.replace();
    }

    showLoading(message) {
        const btn = document.querySelector('button:has(.feather-plus)');
        if (btn) {
            btn.innerHTML = `<i data-feather="loader" class="w-4 h-4 mr-2 animate-spin"></i> ${message}`;
            btn.disabled = true;
            feather.replace();
        }
    }

    hideLoading() {
        const btn = document.querySelector('button:has(.feather-loader)');
        if (btn) {
            btn.innerHTML = '<i data-feather="plus" class="w-4 h-4 mr-2"></i> Create Ticket';
            btn.disabled = false;
            feather.replace();
        }
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification-toast').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;
        
        const styles = {
            success: 'bg-gradient-to-r from-green-500 to-emerald-600 text-white',
            error: 'bg-gradient-to-r from-red-500 to-pink-600 text-white',
            warning: 'bg-gradient-to-r from-yellow-500 to-orange-600 text-white',
            info: 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white'
        };
        
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info'
        };
        
        notification.className += ' ' + styles[type] || styles.info;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <i data-feather="${icons[type] || 'info'}" class="w-6 h-6"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold">${message}</p>
                    <p class="text-xs opacity-75 mt-1">${new Date().toLocaleTimeString()}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 500);
        }, 4000);
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

// Global functions for onclick handlers
let helpManager;

function searchHelp() {
    helpManager.searchHelp();
}

function quickSearch(query) {
    helpManager.quickSearch(query);
}

function showCreateTicketModal() {
    helpManager.showCreateTicketModal();
}

function viewTicket(ticketNumber) {
    helpManager.viewTicket(ticketNumber);
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    helpManager = new HelpManager();
});
</script>
@endpush
