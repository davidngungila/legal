@extends('layouts.app')

@section('title', 'Help & Support - LegalHR Tanzania')

@section('content')
<div class="max-w-7xl mx-auto p-6">
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
                <input type="text" placeholder="Search for help articles, tutorials, and more..." 
                       class="w-full px-6 py-4 text-lg rounded-lg border-0 focus:outline-none focus:ring-4 focus:ring-white/30">
                <i data-feather="search" class="w-6 h-6 text-gray-400 absolute left-4 top-4"></i>
                <button class="absolute right-2 top-2 px-6 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Search
                </button>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 justify-center">
                <span class="text-sm text-indigo-100">Popular searches:</span>
                <button class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Payroll setup</button>
                <button class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Leave management</button>
                <button class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Employee onboarding</button>
                <button class="px-3 py-1 bg-white/20 text-white rounded-full text-sm hover:bg-white/30 transition-colors">Compliance reports</button>
            </div>
        </div>
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
