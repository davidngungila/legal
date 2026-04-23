<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LegalHR - Tanzania HR Management System')</title>
    <meta name="client-title" content="@yield('title', 'LegalHR - Tanzania HR Management System')">
    <meta property="og:title" content="@yield('title', 'LegalHR - Tanzania HR Management System')">
    <meta name="original-title" content="LegalHR - Tanzania HR Management System">
    
    <!-- Custom CSS for client switching animations -->
    <style>
        .client-switching {
            pointer-events: none;
        }
        
        .client-switching * {
            transition: all 0.3s ease !important;
        }
        
        .client-switching .bg-white {
            transform: scale(0.98);
            opacity: 0.8;
        }
        
        @keyframes pulse-scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .pulse-on-switch {
            animation: pulse-scale 0.6s ease-in-out;
        }
        
        .data-transition {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .loader-backdrop {
            backdrop-filter: blur(2px);
        }
    </style>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Manrope:wght@200..800&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Global Functions -->
    <script>
        // Live client data from database
        @php
            $liveClientData = $currentClient ? [
                'id' => $currentClient->id,
                'name' => $currentClient->name,
                'email' => $currentClient->email,
                'industry' => $currentClient->industry,
                'employee_count' => $currentClient->employee_count,
                'status' => $currentClient->status
            ] : null;
        @endphp
        window.liveClientData = @json($liveClientData);

        // All available clients from database
        @php
            $allClients = \App\Models\Client::orderBy('name')->get()->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'industry' => $client->industry,
                    'employee_count' => $client->employee_count,
                    'status' => $client->status
                ];
            })->toArray();
        @endphp
        window.allClients = @json($allClients);

        // Helper function to get client name by ID from live data
        function getClientNameById(clientId) {
            if (!clientId) return 'No Client Selected';
            const client = window.allClients.find(c => c.id == clientId);
            return client ? client.name : 'Unknown Client';
        }

        // Helper function to get client object by ID from live data
        function getClientById(clientId) {
            if (!clientId) return null;
            return window.allClients.find(c => c.id == clientId);
        }

        // Client switching function (available on all pages)
        function switchClient(clientId) {
            if (typeof showNotification === 'undefined') {
                // Define showNotification if not available
                function showNotification(message, type = 'info') {
                    console.log('Notification:', message);
                }
            }
            
            // Prevent switching to the same client
            const currentClient = getCurrentClient();
            if (currentClient.id === clientId) {
                showNotification('Already viewing this client', 'info');
                return;
            }
            
            // Show loading state
            showClientSwitchingLoader(true);
            showNotification('Switching client...', 'info');
            
            // Add transition effects
            document.body.classList.add('client-switching');
            
            // Store selected client in session storage
            sessionStorage.setItem('selectedClientId', clientId);
            
            // Update UI elements
            updateClientUI(clientId);
            
            // Update all module data for the new client
            updateAllModuleData(clientId);
            
            // Force page reload after client switch to ensure server-side updates
            setTimeout(() => {
                // Clear any cached data and reload the page
                if (typeof localStorage !== 'undefined') {
                    localStorage.clear();
                }
                // Reload the page to ensure all server-side data is updated
                window.location.href = window.location.href;
            }, 500);
        }
        
        // Show/hide client switching loader
        function showClientSwitchingLoader(show) {
            let loader = document.getElementById('clientSwitchLoader');
            
            if (show) {
                if (!loader) {
                    loader = document.createElement('div');
                    loader.id = 'clientSwitchLoader';
                    loader.innerHTML = `
                        <div class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 transition-opacity duration-300">
                            <div class="bg-white rounded-lg shadow-xl p-6 flex items-center space-x-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">Switching Client</p>
                                    <p class="text-sm text-gray-600">Updating dashboard data...</p>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(loader);
                }
                loader.style.display = 'flex';
                setTimeout(() => {
                    loader.classList.add('opacity-100');
                }, 10);
            } else {
                if (loader) {
                    loader.classList.remove('opacity-100');
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 300);
                }
            }
        }
        
        // Add completion animation effect
        function addClientSwitchCompletionEffect() {
            const clientSelector = document.querySelector('select[onchange="switchClient(this.value)"]');
            if (clientSelector) {
                clientSelector.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
                setTimeout(() => {
                    clientSelector.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
                }, 2000);
            }
            
            // Animate dashboard cards
            const dashboardCards = document.querySelectorAll('.bg-white.rounded-xl');
            dashboardCards.forEach((card, index) => {
                card.style.transform = 'scale(0.98)';
                card.style.opacity = '0.7';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                    card.style.opacity = '1';
                }, 100 + (index * 50));
            });
        }
        
        // Update UI elements when client changes
        function updateClientUI(clientId) {
            console.log('updateClientUI called with clientId:', clientId);
            
            // Update client selector
            const select = document.querySelector('select[onchange="switchClient(this.value)"]');
            if (select) {
                select.value = clientId;
                console.log('Client selector updated to:', clientId);
            } else {
                console.warn('Client selector not found!');
            }
            
            // Update any client display elements
            const clientDisplays = document.querySelectorAll('[data-client-display]');
            if (clientDisplays.length > 0) {
                const clientName = getClientNameById(clientId);
                clientDisplays.forEach(element => {
                    element.textContent = clientName;
                });
                console.log('Updated', clientDisplays.length, 'client display elements to:', clientName);
            } else {
                console.warn('No client display elements found');
            }
            
            // Update client title if applicable
            const titleElement = document.querySelector('title');
            const clientTitleMeta = document.querySelector('meta[name="client-title"]');
            const ogTitleMeta = document.querySelector('meta[property="og:title"]');
            const originalTitleMeta = document.querySelector('meta[name="original-title"]');
            
            if (titleElement && clientId) {
                const originalTitle = originalTitleMeta ? originalTitleMeta.getAttribute('content') : 'LegalHR - Tanzania HR Management System';
                const clientName = getClientNameById(clientId);
                
                const newTitle = `${clientName} - ${originalTitle}`;
                
                // Update all title-related elements immediately and forcefully
                titleElement.textContent = newTitle;
                if (clientTitleMeta) {
                    clientTitleMeta.setAttribute('content', newTitle);
                }
                if (ogTitleMeta) {
                    ogTitleMeta.setAttribute('content', newTitle);
                }
                
                console.log('Title updated to:', newTitle);
                
                // Force title update in case of race conditions
                setTimeout(() => {
                    if (titleElement.textContent !== newTitle) {
                        titleElement.textContent = newTitle;
                        console.log('Forced title update to:', newTitle);
                    }
                }, 50);
            }
        }
        
        // Update client title (standalone function)
        function updateClientTitle(clientId) {
            const titleElement = document.querySelector('title');
            const clientTitleMeta = document.querySelector('meta[name="client-title"]');
            const ogTitleMeta = document.querySelector('meta[property="og:title"]');
            
            if (titleElement && clientId) {
                const originalTitle = clientTitleMeta ? clientTitleMeta.getAttribute('content').replace(/^[^ -]+ - /, '') : 'LegalHR - Tanzania HR Management System';
                const clientName = getClientNameById(clientId);
                
                const newTitle = `${clientName} - ${originalTitle}`;
                
                // Update all title-related elements
                titleElement.textContent = newTitle;
                if (clientTitleMeta) {
                    clientTitleMeta.setAttribute('content', newTitle);
                }
                if (ogTitleMeta) {
                    ogTitleMeta.setAttribute('content', newTitle);
                }
                
                console.log('Title updated to:', newTitle);
            }
            
            // Ensure all client-related UI elements are synchronized
            synchronizeClientElements(clientId);
        }
        
        // Synchronize all client-related elements across the page
        function synchronizeClientElements(clientId) {
            // Update all elements that should reflect current client
            const clientElements = [
                '[data-client-display]',
                '[data-org-name]',
                '.client-indicator',
                '.current-client'
            ];
            
            clientElements.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(element => {
                    if (element.hasAttribute('data-client-display')) {
                        element.textContent = getClientNameById(clientId);
                    }
                });
            });
            
            console.log('Client elements synchronized for:', clientId);
        }
        
        // Initialize client selection on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Check if sessionStorage is available
            if (typeof Storage === 'undefined') {
                console.error('SessionStorage not available - client persistence may not work');
                return;
            }
            
            // Use live current client data from server, fallback to saved storage, then default to first available client
            let savedClientId = sessionStorage.getItem('selectedClientId') || localStorage.getItem('selectedClientId');
            
            // If we have live client data from server, use it as priority
            if (window.liveClientData && window.liveClientData.id) {
                savedClientId = window.liveClientData.id;
                console.log('Using live client data from server:', savedClientId);
            } else if (savedClientId) {
                console.log('Using saved client ID from storage:', savedClientId);
            } else if (window.allClients && window.allClients.length > 0) {
                savedClientId = window.allClients[0].id;
                console.log('Using first available client:', savedClientId);
            } else {
                savedClientId = null;
                console.log('No client data available');
            }
            
            // Only restore if we have a valid client ID
            if (savedClientId) {
                // Restore saved client and update UI immediately
                updateClientUI(savedClientId);
                updateAllModuleData(savedClientId);
                console.log('Restored client:', savedClientId);
            }
            // Ensure client selector shows correct value
            const clientSelector = document.querySelector('select[onchange="switchClient(this.value)"]');
            if (clientSelector && savedClientId) {
                clientSelector.value = savedClientId;
                console.log('Client selector set to:', savedClientId);
            }
            
            // Update page title multiple times for persistence
            if (savedClientId) {
                updateClientTitle(savedClientId);
            }
            if (savedClientId) {
                setTimeout(() => updateClientTitle(savedClientId), 100);
                setTimeout(() => updateClientTitle(savedClientId), 500);
                setTimeout(() => updateClientTitle(savedClientId), 1000);
            }
            
            // Listen for client change events
            document.addEventListener('clientChanged', function(event) {
                console.log('Client changed to:', event.detail);
                // Additional logic can be added here for specific pages
            });
            
            // Add page visibility change listener to maintain client state
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    const currentClient = getCurrentClient();
                    console.log('Page became visible - current client:', currentClient.id);
                    updateClientUI(currentClient.id);
                    // Update title multiple times when page becomes visible
                    updateClientTitle(currentClient.id);
                    setTimeout(() => updateClientTitle(currentClient.id), 100);
                    setTimeout(() => updateClientTitle(currentClient.id), 500);
                }
            });
            
            // Add beforeunload listener to save state
            window.addEventListener('beforeunload', function() {
                const currentClient = getCurrentClient();
                console.log('Page unloading - saving client:', currentClient.id);
                sessionStorage.setItem('selectedClientId', currentClient.id);
                localStorage.setItem('selectedClientId', currentClient.id);
            });
        });
        
        // Get current selected client
        function getCurrentClient() {
            // Fallback chain for client ID - only use fallback if no saved client
            let clientId = sessionStorage.getItem('selectedClientId') || 
                          localStorage.getItem('selectedClientId');
            
            // Only set default if no client is saved - don't override existing selection
            if (!clientId) {
                console.log('No client saved, using system default');
                return { id: null, name: 'No Client Selected' };
            }
            
            // Return live client data
            return {
                id: clientId,
                name: getClientNameById(clientId)
            };
        }
        
        // Validate and maintain client persistence
        function validateClientPersistence() {
            const currentClient = getCurrentClient();
            
            // Ensure sessionStorage has the client
            sessionStorage.setItem('selectedClientId', currentClient.id);
            
            // Also keep localStorage as backup
            localStorage.setItem('selectedClientId', currentClient.id);
            
            // Update UI elements
            updateClientUI(currentClient.id);
            
            console.log('Client persistence validated - current client:', currentClient.id);
            return currentClient;
        }
        
        // Auto-validate client state every 30 seconds
        setInterval(validateClientPersistence, 30000);
        
        // Dynamic company data generation from live database
        function getCompanyData(clientId) {
            const client = getClientById(clientId);
            if (!client) return null;
            
            // Generate dynamic data based on client information
            const employeeCount = client.employee_count || 100;
            const baseSalary = client.industry === 'Mining' ? 250000 : 
                              client.industry === 'Construction' ? 150000 : 
                              client.industry === 'Manufacturing' ? 180000 : 120000;
            
            return {
                organization: {
                    name: client.name,
                    industry: client.industry,
                    employees: employeeCount,
                    departments: Math.max(3, Math.floor(employeeCount / 30)),
                    locations: Math.max(1, Math.floor(employeeCount / 50)),
                    founded: '2015' // Default or could be added to database
                },
                employeeManagement: {
                    total: employeeCount,
                    active: Math.floor(employeeCount * 0.95),
                    onLeave: Math.floor(employeeCount * 0.03),
                    probation: Math.floor(employeeCount * 0.02),
                    newHires: Math.floor(employeeCount * 0.05),
                    turnover: (Math.random() * 2 + 2).toFixed(1)
                },
                timeAttendance: {
                    todayPresent: Math.floor(employeeCount * 0.95),
                    todayAbsent: Math.floor(employeeCount * 0.03),
                    todayLate: Math.floor(employeeCount * 0.02),
                    weeklyHours: employeeCount * 40,
                    overtime: Math.floor(employeeCount * 0.4),
                    attendanceRate: (94 + Math.random() * 2).toFixed(1)
                },
                payroll: {
                    monthlyPayroll: `TZS ${(employeeCount * baseSalary / 1000000).toFixed(1)}M`,
                    lastProcessed: new Date().toLocaleDateString('en-US', { month: 'short', year: 'numeric' }),
                    pendingApprovals: Math.floor(Math.random() * 5) + 1,
                    avgSalary: `TZS ${Math.floor(baseSalary / 1000)}K`,
                    bonusBudget: `TZS ${(employeeCount * baseSalary * 0.05 / 1000000).toFixed(1)}M`
                },
                criticalModule: {
                    activeCases: Math.floor(Math.random() * 10) + 1,
                    pendingInvestigations: Math.floor(Math.random() * 3),
                    resolvedThisMonth: Math.floor(Math.random() * 8) + 1,
                    complianceScore: Math.floor(85 + Math.random() * 10),
                    riskLevel: client.industry === 'Construction' ? 'High' : 
                              client.industry === 'Mining' ? 'Medium' : 'Low'
                }
            };
        }
        
        // Update all module data when client changes
        function updateAllModuleData(clientId) {
            const data = getCompanyData(clientId);
            if (!data) return;
            
            // Update Organization section
            updateOrganizationData(data.organization);
            
            // Update Employee Management section
            updateEmployeeManagementData(data.employeeManagement);
            
            // Update Time & Attendance section
            updateTimeAttendanceData(data.timeAttendance);
            
            // Update Payroll section
            updatePayrollData(data.payroll);
            
            // Update Critical Module section
            updateCriticalModuleData(data.criticalModule);
            
            console.log('Updated all module data for client:', clientId);
        }
        
        // Update Organization data
        function updateOrganizationData(data) {
            const elements = {
                '[data-org-name]': data.name,
                '[data-org-industry]': data.industry,
                '[data-org-employees]': data.employees,
                '[data-org-departments]': data.departments,
                '[data-org-locations]': data.locations,
                '[data-org-founded]': data.founded
            };
            
            Object.entries(elements).forEach(([selector, value]) => {
                const element = document.querySelector(selector);
                if (element) {
                    // Add transition effect
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(0.95)';
                    element.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        element.textContent = value;
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                    }, 150);
                }
            });
        }
        
        // Update Employee Management data
        function updateEmployeeManagementData(data) {
            const elements = {
                '[data-emp-total]': data.total,
                '[data-emp-active]': data.active,
                '[data-emp-onleave]': data.onLeave,
                '[data-emp-probation]': data.probation,
                '[data-emp-newhires]': data.newHires,
                '[data-emp-turnover]': data.turnover + '%'
            };
            
            Object.entries(elements).forEach(([selector, value]) => {
                const element = document.querySelector(selector);
                if (element) {
                    // Add transition effect
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(0.95)';
                    element.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        element.textContent = value;
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                        
                        // Add pulse effect for important metrics
                        if (selector === '[data-emp-total]' || selector === '[data-emp-active]') {
                            element.parentElement.classList.add('animate-pulse');
                            setTimeout(() => {
                                element.parentElement.classList.remove('animate-pulse');
                            }, 1000);
                        }
                    }, 150);
                }
            });
        }
        
        // Update Time & Attendance data
        function updateTimeAttendanceData(data) {
            const elements = {
                '[data-att-present]': data.todayPresent,
                '[data-att-absent]': data.todayAbsent,
                '[data-att-late]': data.todayLate,
                '[data-att-hours]': data.weeklyHours.toLocaleString(),
                '[data-att-overtime]': data.overtime,
                '[data-att-rate]': data.attendanceRate + '%'
            };
            
            Object.entries(elements).forEach(([selector, value]) => {
                const element = document.querySelector(selector);
                if (element) {
                    // Add transition effect
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(0.95)';
                    element.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        element.textContent = value;
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                        
                        // Add color animation for attendance rate
                        if (selector === '[data-att-rate]') {
                            const rate = parseFloat(data.attendanceRate);
                            if (rate >= 95) {
                                element.classList.add('text-green-600');
                            } else if (rate >= 90) {
                                element.classList.add('text-yellow-600');
                            } else {
                                element.classList.add('text-red-600');
                            }
                        }
                    }, 150);
                }
            });
        }
        
        // Update Payroll data
        function updatePayrollData(data) {
            const elements = {
                '[data-pay-total]': data.monthlyPayroll,
                '[data-pay-processed]': data.lastProcessed,
                '[data-pay-pending]': data.pendingApprovals,
                '[data-pay-avg]': data.avgSalary,
                '[data-pay-budget]': data.bonusBudget
            };
            
            Object.entries(elements).forEach(([selector, value]) => {
                const element = document.querySelector(selector);
                if (element) {
                    // Add transition effect
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(0.95)';
                    element.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        element.textContent = value;
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                        
                        // Add highlight effect for payroll total
                        if (selector === '[data-pay-total]') {
                            element.parentElement.classList.add('bg-green-50', 'border-green-200');
                            setTimeout(() => {
                                element.parentElement.classList.remove('bg-green-50', 'border-green-200');
                            }, 1500);
                        }
                    }, 150);
                }
            });
        }
        
        // Update Critical Module data
        function updateCriticalModuleData(data) {
            const elements = {
                '[data-critical-cases]': data.activeCases,
                '[data-critical-pending]': data.pendingInvestigations,
                '[data-critical-resolved]': data.resolvedThisMonth,
                '[data-critical-score]': data.complianceScore + '%',
                '[data-critical-risk]': data.riskLevel
            };
            
            Object.entries(elements).forEach(([selector, value]) => {
                const element = document.querySelector(selector);
                if (element) {
                    // Add transition effect
                    element.style.transition = 'all 0.3s ease';
                    element.style.transform = 'scale(0.95)';
                    element.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        element.textContent = value;
                        element.style.transform = 'scale(1)';
                        element.style.opacity = '1';
                    }, 150);
                }
            });
            
            // Update risk level styling with animation
            const riskElement = document.querySelector('[data-critical-risk]');
            if (riskElement) {
                riskElement.style.transition = 'all 0.3s ease';
                riskElement.style.transform = 'scale(0.9)';
                
                setTimeout(() => {
                    // Remove existing risk classes
                    riskElement.className = riskElement.className.replace(/bg-\w+-100|text-\w+-800/g, '');
                    
                    // Add new risk classes with animation
                    if (data.riskLevel === 'High') {
                        riskElement.classList.add('bg-red-100', 'text-red-800');
                    } else if (data.riskLevel === 'Medium') {
                        riskElement.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else {
                        riskElement.classList.add('bg-green-100', 'text-green-800');
                    }
                    
                    riskElement.style.transform = 'scale(1)';
                    
                    // Add pulse effect for high risk
                    if (data.riskLevel === 'High') {
                        riskElement.classList.add('animate-pulse');
                        setTimeout(() => {
                            riskElement.classList.remove('animate-pulse');
                        }, 2000);
                    }
                }, 200);
            }
        }
        
        // Filter data based on current client
        function filterByClient(data) {
            const currentClient = getCurrentClient();
            // For now, return all data. In a real app, this would filter based on client
            // This is a placeholder for client-specific data filtering
            return data;
        }
    </script>
</head>
<body class="font-lato bg-gray-50">
    @if(isset($currentUser))
        <!-- Main Application Layout -->
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')
            
            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Header -->
                @include('layouts.header')
                
                <!-- Main Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                    @yield('content')
                    
                    <!-- Footer inside scrollable content -->
                    @include('layouts.footer')
                </main>
            </div>
        </div>
    @else
        <!-- Auth Layout -->
        @yield('content')
    @endif
    
    <script>
        // Initialize Feather Icons
        feather.replace();
        
        // Update client-specific data throughout the application
        function updateClientData(clientId) {
            const client = getClientById(clientId);
            if (client) {
                // Generate dynamic payroll data based on client info
                const baseSalary = client.industry === 'Mining' ? 250000 : 
                                 client.industry === 'Construction' ? 150000 : 
                                 client.industry === 'Manufacturing' ? 180000 : 120000;
                const monthlyPayroll = `TZS ${(client.employee_count * baseSalary / 1000000).toFixed(1)}M`;
                const complianceScore = Math.floor(85 + Math.random() * 10);
                
                // Update dashboard stats
                const employeeCountElements = document.querySelectorAll('.text-2xl');
                employeeCountElements.forEach(element => {
                    if (element.textContent.includes('248') || element.textContent.includes('employees')) {
                        element.textContent = client.employee_count;
                    }
                });
                
                // Update payroll amount
                const payrollAmountElements = document.querySelectorAll('.text-2xl');
                payrollAmountElements.forEach(element => {
                    if (element.textContent.includes('45.2M') || element.textContent.includes('TZS') && element.textContent.includes('M')) {
                        element.textContent = monthlyPayroll;
                    }
                });
                
                // Update compliance score
                const complianceScoreElements = document.querySelectorAll('.text-5xl, .text-4xl');
                complianceScoreElements.forEach(element => {
                    if (element.textContent.includes('94') || element.textContent.includes('%')) {
                        element.textContent = complianceScore + '%';
                    }
                });
                
                // Update company name in headers
                const companyNames = document.querySelectorAll('.font-manrope, .company-name');
                companyNames.forEach(element => {
                    if (element.textContent.includes('ABC Manufacturing Ltd') || 
                        element.textContent.includes('XYZ Construction') ||
                        element.textContent.includes('Tanzania Mining') ||
                        element.textContent.includes('East Africa Logistics')) {
                        element.textContent = client.name;
                    }
                });
            }
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-600' : 
                type === 'error' ? 'bg-red-600' : 
                type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
            }`;
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }
        
        // Toggle User Dropdown
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userButton = document.getElementById('userButton');
            
            if (!userButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
