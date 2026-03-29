<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LegalHR - Tanzania HR Management System')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Manrope:wght@200..800&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
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
