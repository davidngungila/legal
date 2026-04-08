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
            
            // Trigger data refresh for components that need client-specific data
            setTimeout(() => {
                const clientNames = {
                    '1': 'ABC Manufacturing Ltd',
                    '2': 'XYZ Construction Co',
                    '3': 'Tanzania Mining Corp',
                    '4': 'East Africa Logistics'
                };
                
                // Show success notification with more details
                showNotification(`Successfully switched to ${clientNames[clientId]}`, 'success');
                
                // Show additional context notification
                setTimeout(() => {
                    const data = companyData[clientId];
                    showNotification(`Now viewing ${data.organization.employees} employees, ${data.organization.departments} departments`, 'info', 4000);
                }, 1000);
                
                // Trigger custom event for other components to listen to
                document.dispatchEvent(new CustomEvent('clientChanged', {
                    detail: {
                        clientId: clientId,
                        clientName: clientNames[clientId],
                        organization: companyData[clientId].organization
                    }
                }));
                
                // Reload data if there are data tables on the page
                if (typeof loadEmployees === 'function') {
                    loadEmployees();
                }
                if (typeof loadPermissions === 'function') {
                    loadPermissions();
                }
                if (typeof filterEmployees === 'function') {
                    filterEmployees();
                }
                
                // Remove loading state and transitions
                setTimeout(() => {
                    showClientSwitchingLoader(false);
                    document.body.classList.remove('client-switching');
                    
                    // Add completion animation
                    addClientSwitchCompletionEffect();
                }, 800);
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
                const clientNames = {
                    '1': 'ABC Manufacturing Ltd',
                    '2': 'XYZ Construction Co',
                    '3': 'Tanzania Mining Corp',
                    '4': 'East Africa Logistics'
                };
                clientDisplays.forEach(element => {
                    element.textContent = clientNames[clientId] || 'No Client Selected';
                });
                console.log('Updated', clientDisplays.length, 'client display elements to:', clientNames[clientId] || 'No Client Selected');
            } else {
                console.warn('No client display elements found');
            }
            
            // Update client title if applicable
            const titleElement = document.querySelector('title');
            const clientTitleMeta = document.querySelector('meta[name="client-title"]');
            const ogTitleMeta = document.querySelector('meta[property="og:title"]');
            const originalTitleMeta = document.querySelector('meta[name="original-title"]');
            
            if (titleElement && clientId) {
                const clientNames = {
                    '1': 'ABC Manufacturing Ltd',
                    '2': 'XYZ Construction Co',
                    '3': 'Tanzania Mining Corp',
                    '4': 'East Africa Logistics'
                };
                const originalTitle = originalTitleMeta ? originalTitleMeta.getAttribute('content') : 'LegalHR - Tanzania HR Management System';
                
                const newTitle = `${clientNames[clientId]} - ${originalTitle}`;
                
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
                const clientNames = {
                    '1': 'ABC Manufacturing Ltd',
                    '2': 'XYZ Construction Co',
                    '3': 'Tanzania Mining Corp',
                    '4': 'East Africa Logistics'
                };
                const originalTitle = clientTitleMeta ? clientTitleMeta.getAttribute('content').replace(/^[^ -]+ - /, '') : 'LegalHR - Tanzania HR Management System';
                
                const newTitle = `${clientNames[clientId]} - ${originalTitle}`;
                
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
                    const clientNames = {
                        '1': 'ABC Manufacturing Ltd',
                        '2': 'XYZ Construction Co',
                        '3': 'Tanzania Mining Corp',
                        '4': 'East Africa Logistics'
                    };
                    
                    if (element.hasAttribute('data-client-display')) {
                        element.textContent = clientNames[clientId];
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
            
            const savedClientId = sessionStorage.getItem('selectedClientId') || localStorage.getItem('selectedClientId') || '1';
            console.log('Page loaded - saved client ID:', savedClientId);
            
            // Restore saved client and update UI immediately
            updateClientUI(savedClientId);
            updateAllModuleData(savedClientId);
            console.log('Restored client:', savedClientId);
            
            // Ensure client selector shows correct value
            const clientSelector = document.querySelector('select[onchange="switchClient(this.value)"]');
            if (clientSelector) {
                clientSelector.value = savedClientId;
                console.log('Client selector set to:', savedClientId);
            }
            
            // Update page title multiple times for persistence
            updateClientTitle(savedClientId);
            setTimeout(() => updateClientTitle(savedClientId), 100);
            setTimeout(() => updateClientTitle(savedClientId), 500);
            setTimeout(() => updateClientTitle(savedClientId), 1000);
            
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
            
            // Don't validate or reset - let the user's choice persist
            const clientNames = {
                '1': 'ABC Manufacturing Ltd',
                '2': 'XYZ Construction Co',
                '3': 'Tanzania Mining Corp',
                '4': 'East Africa Logistics'
            };
            
            return {
                id: clientId,
                name: clientNames[clientId]
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
        
        // Company-specific data structures
        const companyData = {
            '1': { // ABC Manufacturing Ltd
                organization: {
                    name: 'ABC Manufacturing Ltd',
                    industry: 'Manufacturing',
                    employees: 248,
                    departments: 8,
                    locations: 3,
                    founded: '2015'
                },
                employeeManagement: {
                    total: 248,
                    active: 235,
                    onLeave: 8,
                    probation: 5,
                    newHires: 12,
                    turnover: 3.2
                },
                timeAttendance: {
                    todayPresent: 235,
                    todayAbsent: 8,
                    todayLate: 5,
                    weeklyHours: 9800,
                    overtime: 120,
                    attendanceRate: 94.8
                },
                payroll: {
                    monthlyPayroll: 'TZS 45.2M',
                    lastProcessed: 'Nov 2024',
                    pendingApprovals: 3,
                    avgSalary: 'TZS 182K',
                    bonusBudget: 'TZS 2.3M'
                },
                criticalModule: {
                    activeCases: 7,
                    pendingInvestigations: 2,
                    resolvedThisMonth: 5,
                    complianceScore: 87,
                    riskLevel: 'Medium'
                }
            },
            '2': { // XYZ Construction Co
                organization: {
                    name: 'XYZ Construction Co',
                    industry: 'Construction',
                    employees: 186,
                    departments: 6,
                    locations: 5,
                    founded: '2012'
                },
                employeeManagement: {
                    total: 186,
                    active: 172,
                    onLeave: 9,
                    probation: 5,
                    newHires: 8,
                    turnover: 4.1
                },
                timeAttendance: {
                    todayPresent: 172,
                    todayAbsent: 9,
                    todayLate: 7,
                    weeklyHours: 7200,
                    overtime: 180,
                    attendanceRate: 92.5
                },
                payroll: {
                    monthlyPayroll: 'TZS 38.7M',
                    lastProcessed: 'Nov 2024',
                    pendingApprovals: 5,
                    avgSalary: 'TZS 208K',
                    bonusBudget: 'TZS 1.9M'
                },
                criticalModule: {
                    activeCases: 12,
                    pendingInvestigations: 4,
                    resolvedThisMonth: 3,
                    complianceScore: 79,
                    riskLevel: 'High'
                }
            },
            '3': { // Tanzania Mining Corp
                organization: {
                    name: 'Tanzania Mining Corp',
                    industry: 'Mining',
                    employees: 312,
                    departments: 10,
                    locations: 4,
                    founded: '2008'
                },
                employeeManagement: {
                    total: 312,
                    active: 298,
                    onLeave: 11,
                    probation: 3,
                    newHires: 15,
                    turnover: 2.8
                },
                timeAttendance: {
                    todayPresent: 298,
                    todayAbsent: 11,
                    todayLate: 3,
                    weeklyHours: 12400,
                    overtime: 95,
                    attendanceRate: 95.5
                },
                payroll: {
                    monthlyPayroll: 'TZS 62.1M',
                    lastProcessed: 'Nov 2024',
                    pendingApprovals: 2,
                    avgSalary: 'TZS 199K',
                    bonusBudget: 'TZS 3.1M'
                },
                criticalModule: {
                    activeCases: 5,
                    pendingInvestigations: 1,
                    resolvedThisMonth: 8,
                    complianceScore: 91,
                    riskLevel: 'Low'
                }
            },
            '4': { // East Africa Logistics
                organization: {
                    name: 'East Africa Logistics',
                    industry: 'Logistics',
                    employees: 156,
                    departments: 5,
                    locations: 8,
                    founded: '2016'
                },
                employeeManagement: {
                    total: 156,
                    active: 148,
                    onLeave: 6,
                    probation: 2,
                    newHires: 6,
                    turnover: 3.5
                },
                timeAttendance: {
                    todayPresent: 148,
                    todayAbsent: 6,
                    todayLate: 4,
                    weeklyHours: 6200,
                    overtime: 85,
                    attendanceRate: 94.9
                },
                payroll: {
                    monthlyPayroll: 'TZS 31.4M',
                    lastProcessed: 'Nov 2024',
                    pendingApprovals: 1,
                    avgSalary: 'TZS 201K',
                    bonusBudget: 'TZS 1.6M'
                },
                criticalModule: {
                    activeCases: 4,
                    pendingInvestigations: 2,
                    resolvedThisMonth: 6,
                    complianceScore: 85,
                    riskLevel: 'Medium'
                }
            }
        };
        
        // Update all module data when client changes
        function updateAllModuleData(clientId) {
            const data = companyData[clientId];
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
                // Update dashboard stats
                const employeeCount = document.querySelector('.text-2xl');
                if (employeeCount && employeeCount.textContent.includes('248')) {
                    employeeCount.textContent = client.employees;
                }
                
                // Update payroll amount
                const payrollAmount = document.querySelector('.text-2xl');
                if (payrollAmount && payrollAmount.textContent.includes('45.2M')) {
                    payrollAmount.textContent = client.payroll;
                }
                
                // Update compliance score
                const complianceScore = document.querySelector('.text-5xl');
                if (complianceScore && complianceScore.textContent.includes('94')) {
                    complianceScore.textContent = client.compliance + '%';
                }
                
                // Update company name in headers
                const companyNames = document.querySelectorAll('.font-manrope');
                companyNames.forEach(element => {
                    if (element.textContent.includes('ABC Manufacturing Ltd')) {
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
