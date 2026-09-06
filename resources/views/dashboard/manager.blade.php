<div class="p-6">
    <!-- Dashboard Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 font-manrope">Manager Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()?->name ?? 'User' }}! Here's your team overview.</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 shadow-sm">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i data-feather="briefcase" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Current Client</p>
                        <p class="text-sm font-semibold text-gray-900" data-client-display>{{ $currentClient?->name ?? 'No Client Selected' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$manager)
    <!-- Missing employee record -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8">
        <div class="flex items-center">
            <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3"></i>
            <div>
                <h3 class="text-yellow-800 font-semibold">Employee Record Not Found</h3>
                <p class="text-yellow-600 text-sm">Your employee record is not linked to the current employer, so team data cannot be resolved. Please contact HR.</p>
            </div>
        </div>
    </div>
    @else
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Team Members -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <span class="text-sm text-green-600 font-medium">{{ $team_active }} active</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $team_count }}</h3>
            <p class="text-gray-600 text-sm">Team Members</p>
        </div>

        <!-- Present Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <span class="text-sm text-yellow-600 font-medium">{{ $late_today }} late</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $present_today }}</h3>
            <p class="text-gray-600 text-sm">Present Today</p>
        </div>

        <!-- Absent Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
                </div>
                <span class="text-sm text-red-600 font-medium">{{ $absent_today }}</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $absent_today }}</h3>
            <p class="text-gray-600 text-sm">Absent Today</p>
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <span class="text-sm text-gray-500 font-medium">leave</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $pending_approvals }}</h3>
            <p class="text-gray-600 text-sm">Pending Approvals</p>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Team Hours -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Hours · {{ now()->format('F Y') }}</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Hours:</span>
                    <span class="text-sm font-medium">{{ number_format($team_hours, 1) }} hrs</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Overtime:</span>
                    <span class="text-sm font-medium text-blue-600">{{ number_format($team_overtime, 1) }} hrs</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg per Member:</span>
                    <span class="text-sm font-medium">{{ $team_count ? number_format($team_hours / max(1, $team_count), 1) : 0 }} hrs</span>
                </div>
            </div>
        </div>

        <!-- Team Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Composition</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active:</span>
                    <span class="text-sm font-medium text-green-600">{{ $team_active }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">On Leave:</span>
                    <span class="text-sm font-medium text-yellow-600">{{ $team->where('status', 'on_leave')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Probation:</span>
                    <span class="text-sm font-medium text-blue-600">{{ $team->filter(fn ($e) => $e->status === 'probation' || $e->isOnProbation())->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Terminated:</span>
                    <span class="text-sm font-medium text-gray-600">{{ $team->where('status', 'terminated')->count() }}</span>
                </div>
            </div>
        </div>

        <!-- My Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">My Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Department:</span>
                    <span class="text-sm font-medium">{{ $manager->department ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Position:</span>
                    <span class="text-sm font-medium">{{ $manager->position ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Email:</span>
                    <span class="text-sm font-medium">{{ $manager->email ?: '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Hire Date:</span>
                    <span class="text-sm font-medium">{{ $manager->hire_date?->format('d M Y') ?: '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Attendance Today -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Team Attendance · {{ now()->format('d M Y') }}</h3>
            <a href="{{ route('attendance.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage Attendance →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock In</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($team_attendance_today as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($row['employee']->first_name ?? '?', 0, 1) . substr($row['employee']->last_name ?? '', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $row['employee']->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $row['employee']->position ?: $row['employee']->department ?: '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($row['status'] === 'present')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-green-100 text-green-700">Present</span>
                            @elseif($row['status'] === 'late')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-yellow-100 text-yellow-700">Late</span>
                            @elseif($row['status'] === 'absent')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-red-100 text-red-700">Absent</span>
                            @elseif($row['status'] === 'on_leave')
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-100 text-blue-700">On Leave</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">Not Marked</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            @if($row['clock_in'])
                                {{ \Carbon\Carbon::parse($row['clock_in'])->format('H:i') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            {{ $row['hours'] ? number_format($row['hours'], 1) : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            <i data-feather="users" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                            <p>No team members assigned to you yet</p>
                            <p class="text-xs text-gray-400 mt-1">Employees who report to you will appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Pending Leave Requests</h3>
            @hasPermission('selfservice.leave')
            <a href="{{ route('leave.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Review All →</a>
            @endhasPermission
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($team_leave_requests as $request)
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-yellow-100 text-yellow-700">Pending</span>
                    <span class="text-[11px] text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                </div>
                <p class="font-medium text-gray-900">{{ $request->employee?->full_name }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $request->leave_type ?: \Illuminate\Support\Str::title($request->leave_type) ?: 'Leave' }}
                    · {{ $request->start_date?->format('d M') }} - {{ $request->end_date?->format('d M Y') }}
                </p>
                <p class="text-xs text-gray-500 mt-1">{{ $request->days ?? $request->days_requested ?? '' }} days</p>
                <div class="mt-3 flex space-x-2">
                    <a href="{{ route('leave.approve', $request->id) }}" class="px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700 transition-colors">Approve</a>
                    <a href="{{ route('leave.reject', $request->id) }}" class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition-colors">Reject</a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                <i data-feather="calendar" class="w-10 h-10 mx-auto mb-3 text-gray-300"></i>
                <p>No pending leave requests</p>
                <p class="text-xs text-gray-400 mt-1">Team leave requests awaiting your approval will appear here.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('attendance.index') }}" class="flex items-center justify-center px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Manage Attendance
            </a>
            @hasPermission('selfservice.leave')
            <a href="{{ route('leave.index') }}" class="flex items-center justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i data-feather="clipboard" class="w-4 h-4 mr-2"></i>
                Review Leave
            </a>
            @endhasPermission
            <a href="{{ route('employees.index') }}" class="flex items-center justify-center px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i data-feather="users" class="w-4 h-4 mr-2"></i>
                My Team
            </a>
            <a href="{{ route('performance.index') }}" class="flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-feather="target" class="w-4 h-4 mr-2"></i>
                Performance
            </a>
        </div>
    </div>
    @endif
</div>