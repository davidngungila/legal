<div class="p-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">My Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()?->name ?? 'User' }}! Here's your personal HR overview.</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Employer</p>
                        <p class="text-sm font-semibold text-gray-900" data-client-display>{{ $currentClient?->name ?? 'No Client Selected' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$employee)
    <!-- Missing employee record -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-center">
            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h3 class="text-yellow-800 font-semibold">Employee Record Not Found</h3>
                <p class="text-yellow-600 text-sm">Your employee record is not linked to the current employer. Please contact HR to resolve this.</p>
            </div>
        </div>
    </div>
    @else
    <!-- Employee Profile Card -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-8 text-white mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    @if($employee->profile_photo)
                        <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="{{ $employee->full_name }}" class="w-20 h-20 rounded-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-indigo-600">{{ strtoupper(substr($employee->first_name ?? 'E', 0, 1) . substr($employee->last_name ?? 'E', 0, 1)) }}</span>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-bold">{{ $employee->full_name }}</h2>
                    <p class="text-indigo-200">{{ $employee->position ?: 'Position not set' }}</p>
                    <p class="text-indigo-200 text-sm">{{ $employee->employee_id ?? '' }} {{ $employee->department ? '· ' . $employee->department : '' }}</p>
                    <div class="mt-2 flex items-center space-x-2">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $employee->status === 'active' ? 'bg-green-500/20 text-green-200 border border-green-400/30' : 'bg-yellow-500/20 text-yellow-200 border border-yellow-400/30' }}">
                            {{ ucfirst(str_replace('_', ' ', $employee->status ?? 'inactive')) }}
                        </span>
                        @if($employee->employment_type)
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-white/10 border border-white/20 capitalize">
                            {{ str_replace('_', ' ', $employee->employment_type) }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-3xl font-bold">{{ $employee->hire_date ? $employee->employment_duration : 0 }}</p>
                    <p class="text-sm text-indigo-200">Service</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ number_format($leave_balance, 1) }}</p>
                    <p class="text-sm text-indigo-200">Leave Days Left</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">{{ $attendance_summary['present'] + $attendance_summary['late'] }}</p>
                    <p class="text-sm text-indigo-200">Days This Month</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">TZS {{ $employee->salary ? number_format($employee->salary, 0) : '—' }}</p>
                    <p class="text-sm text-indigo-200">Salary</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Alerts -->
    @if(count($personal_alerts) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Personal Notices</h3>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ count($personal_alerts) ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ count($personal_alerts) ? count($personal_alerts) . ' open' : 'All clear' }}
            </span>
        </div>
        <div class="space-y-3">
            @foreach($personal_alerts as $alert)
            <div class="p-3 bg-{{ $alert['color'] }}-50 rounded-lg border border-{{ $alert['color'] }}-100">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start space-x-3">
                        <i data-feather="{{ $alert['icon'] }}" class="w-5 h-5 text-{{ $alert['color'] }}-600 mt-0.5"></i>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-medium text-gray-900">{{ $alert['title'] }}</p>
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $alert['severity'] === 'critical' ? 'bg-red-100 text-red-700' : ($alert['severity'] === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($alert['severity']) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">{{ $alert['description'] }}</p>
                        </div>
                    </div>
                    @if(!empty($alert['link']))
                        <a href="{{ $alert['link'] }}" class="text-xs font-medium text-{{ $alert['color'] }}-700 hover:text-{{ $alert['color'] }}-800 whitespace-nowrap">
                            {{ $alert['action_label'] ?? 'Open' }}
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        <a href="{{ route('selfservice.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Open Self Service →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @foreach($quickActions as $action)
        <a href="{{ $action['href'] }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md hover:bg-{{ $action['color'] }}-50 transition-all group">
            <div class="w-11 h-11 bg-{{ $action['color'] }}-100 rounded-lg flex items-center justify-center mb-3">
                <i data-feather="{{ $action['icon'] }}" class="w-5 h-5 text-{{ $action['color'] }}-600"></i>
            </div>
            <p class="font-medium text-gray-900 text-sm group-hover:text-{{ $action['color'] }}-700">{{ $action['label'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $action['description'] }}</p>
            <span class="inline-block mt-3 px-2 py-0.5 rounded-full text-[11px] font-medium bg-transparent text-{{ $action['color'] }}-700 border border-{{ $action['color'] }}-200">{{ $action['badge'] }}</span>
        </a>
        @endforeach
    </div>

    <!-- Employment & Profile Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Profile Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Profile</h3>
                <a href="{{ route('selfservice.profile') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Employee ID:</span>
                    <span class="text-sm font-medium">{{ $employee->employee_id ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Department:</span>
                    <span class="text-sm font-medium">{{ $employee->department ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Position:</span>
                    <span class="text-sm font-medium">{{ $employee->position ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Manager:</span>
                    <span class="text-sm font-medium">{{ $employee->manager?->full_name ?: $employee->reporting_to ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Hire Date:</span>
                    <span class="text-sm font-medium">{{ $employee->hire_date?->format('d M Y') ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Work Schedule:</span>
                    <span class="text-sm font-medium">{{ $employee->work_schedule ?: '—' }}</span>
                </div>
                <div class="pt-3 mt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">Contact</p>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-medium">{{ $employee->email ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-medium">{{ $employee->phone ?: '—' }}</span>
                    </div>
                    @if($employee->city)
                    <div class="flex justify-between mt-2">
                        <span class="text-sm text-gray-600">Location:</span>
                        <span class="text-sm font-medium">{{ $employee->city }}{{ $employee->region ? ', ' . $employee->region : '' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Balance & Requests -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Leave</h3>
                <a href="{{ route('selfservice.leave') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Request →</a>
            </div>

            <div class="mb-5">
                <div class="flex items-end justify-between mb-2">
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($leave_balance, 1) }}</p>
                        <p class="text-sm text-gray-500">days remaining</p>
                    </div>
                    <span class="text-xs text-gray-400">of {{ $employee->leave_balance ?: 28 }} days allocation</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ max(0, min(100, ($leave_balance / max(1, $employee->leave_balance ?: 28)) * 100)) }}%"></div>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($leave_requests as $request)
                <div class="border border-gray-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-purple-100 text-purple-800">{{ ucfirst($request->request_type) }}</span>
                        <span class="text-[11px] text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm font-medium text-gray-900">{{ $request->title }}</p>
                    @if($request->start_date)
                    <p class="text-xs text-gray-600 mt-1">{{ $request->start_date->format('d M Y') }} - {{ $request->end_date?->format('d M Y') }} ({{ $request->days_requested }} days)</p>
                    @endif
                    <span class="inline-block mt-2 px-2 py-0.5 rounded-full text-[11px] font-medium
                        {{ $request->status === 'approved' ? 'bg-green-100 text-green-700' : ($request->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($request->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i data-feather="calendar" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                    <p class="text-sm">No leave requests yet</p>
                    <p class="text-xs text-gray-400 mt-1">Submit your first leave request to see it here.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Contract Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Contract</h3>
                <a href="{{ route('selfservice.contract') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View →</a>
            </div>
            @if($contract)
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Contract Type:</span>
                    <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $contract->contract_type ?: 'unspecified') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Number:</span>
                    <span class="text-sm font-medium">{{ $contract->contract_number ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Start Date:</span>
                    <span class="text-sm font-medium">{{ $contract->start_date?->format('d M Y') ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">End Date:</span>
                    <span class="text-sm font-medium">{{ $contract->end_date?->format('d M Y') ?: 'Open-ended' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Salary:</span>
                    <span class="text-sm font-medium">{{ $contract->salary ? number_format($contract->salary, 0) : '—' }} {{ $contract->currency ?: 'TZS' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Schedule:</span>
                    <span class="text-sm font-medium">{{ $contract->work_schedule ?: '—' }}</span>
                </div>
                <div class="pt-3 mt-3 border-t border-gray-100">
                    <span class="inline-block px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                </div>
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <i data-feather="file-text" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                <p class="text-sm">No active contract found</p>
                <p class="text-xs text-gray-400 mt-1">Your employment contract details will appear here once available.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Attendance Stats -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance · {{ now()->format('F Y') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <p class="text-2xl font-bold text-green-600">{{ $attendance_summary['present'] }}</p>
                    <p class="text-sm text-gray-600">Present</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                    <p class="text-2xl font-bold text-yellow-600">{{ $attendance_summary['late'] }}</p>
                    <p class="text-sm text-gray-600">Late</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                    <p class="text-2xl font-bold text-red-600">{{ $attendance_summary['absent'] }}</p>
                    <p class="text-sm text-gray-600">Absent</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-2xl font-bold text-blue-600">{{ $attendance_summary['on_leave'] }}</p>
                    <p class="text-sm text-gray-600">On Leave</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Hours This Month:</span>
                    <span class="text-sm font-medium">{{ number_format($attendance_summary['total_hours'], 1) }} hrs</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Overtime This Month:</span>
                    <span class="text-sm font-medium text-blue-600">{{ number_format($attendance_summary['overtime_hours'], 1) }} hrs</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Half Days:</span>
                    <span class="text-sm font-medium">{{ $attendance_summary['half_day'] }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">My Recent Attendance</h3>
                @hasPermission('attendance.view')
                <a href="{{ route('attendance.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View Attendance →</a>
                @endhasPermission
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock Out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($month_attendance->take(6) as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $record->attendance_date->format('d M Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $record->clock_in?->format('H:i') ?: '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $record->clock_out?->format('H:i') ?: '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $record->total_hours ? number_format($record->total_hours, 1) : '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">{!! $record->status_badge !!}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i data-feather="clock" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                                <p>No attendance records this month</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payslips -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Payslips</h3>
            <a href="{{ route('selfservice.payslip') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Allowances</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payslips as $payslip)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            @php
                                try {
                                    $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $payslip->payroll_period)->format('F Y');
                                } catch (\Throwable $e) {
                                    $periodLabel = $payslip->payroll_period;
                                }
                            @endphp
                            {{ $periodLabel }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS {{ number_format($payslip->basic_salary ?? 0, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS {{ number_format(($payslip->allowances ?? 0) + ($payslip->bonuses ?? 0), 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">TZS {{ number_format($payslip->total_deductions ?? 0, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">TZS {{ number_format($payslip->net_pay ?? 0, 0) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{!! $payslip->status_badge !!}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i data-feather="credit-card" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                            <p>No payslips yet</p>
                            <p class="text-xs text-gray-400 mt-1">Your payslips will appear here once payroll is processed.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>