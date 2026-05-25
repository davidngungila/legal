@extends('layouts.app')

@section('title', 'Induction Training - Orvion HRIS')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Induction Training</h1>
            <p class="text-gray-600 mt-2">Manage employee induction and training programs</p>
        </div>
        <div class="flex space-x-3 mt-4 md:mt-0">
            <button onclick="showStatisticsModal()" 
                    class="px-4 py-2 border border-blue-300 text-blue-700 rounded-lg hover:bg-blue-50 transition-colors flex items-center">
                <i data-feather="bar-chart-2" class="w-4 h-4 mr-2"></i>
                Statistics
            </button>
            <button onclick="showScheduleModal()" 
                    class="px-4 py-2 border border-green-300 text-green-700 rounded-lg hover:bg-green-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Schedule Training
            </button>
            <button onclick="showCalendarModal()" 
                    class="px-4 py-2 border border-purple-300 text-purple-700 rounded-lg hover:bg-purple-50 transition-colors flex items-center">
                <i data-feather="calendar" class="w-4 h-4 mr-2"></i>
                Calendar
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i data-feather="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Employees</p>
                    <p class="text-2xl font-semibold text-gray-900" id="totalEmployees">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i data-feather="award" class="w-6 h-6 text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Trained</p>
                    <p class="text-2xl font-semibold text-gray-900" id="trainedCount">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i data-feather="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Completion Rate</p>
                    <p class="text-2xl font-semibold text-gray-900" id="completionRate">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-lg p-3">
                    <i data-feather="clock" class="w-6 h-6 text-orange-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg Hours</p>
                    <p class="text-2xl font-semibold text-gray-900" id="avgHours">-</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Upcoming</p>
                    <p class="text-2xl font-semibold text-gray-900" id="upcomingCount">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="searchInput" placeholder="Search employees..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <select id="workStationFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Work Stations</option>
                    @foreach($employees->pluck('work_station')->unique() as $workStation)
                        <option value="{{ $workStation }}">{{ $workStation }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select id="trainingTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Training Types</option>
                    <option value="company_policies">Company Policies</option>
                    <option value="safety_procedures">Safety Procedures</option>
                    <option value="job_specific">Job Specific</option>
                    <option value="compliance">Compliance</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Status</option>
                    <option value="completed">Completed</option>
                    <option value="incomplete">Incomplete</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="not_started">Not Started</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Training Progress
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Latest Training
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Hours Completed
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="employeesTableBody">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors employee-row" 
                            data-name="{{ $employee->first_name . ' ' . $employee->surname }}"
                            data-workstation="{{ $employee->work_station }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-bold text-sm">
                                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->surname, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->surname }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->work_station }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $totalModules = 4; // Assuming 4 standard modules
                                    $completedCount = $employee->inductionTrainings->where('status', 'completed')->count();
                                    $percentage = $totalModules > 0 ? ($completedCount / $totalModules) * 100 : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ number_format($percentage, 0) }}% Complete</div>
                                <div class="text-xs text-gray-400">{{ $completedCount }} of {{ $totalModules }} modules</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($latestTraining = $employee->inductionTrainings->sortByDesc('training_date')->first())
                                    <div class="text-sm text-gray-900">{{ Str::headline($latestTraining->training_type) }}</div>
                                    <div class="text-sm text-gray-500">Date: {{ $latestTraining->training_date->format('Y-m-d') }}</div>
                                    <div class="text-xs {{ $latestTraining->assessment_passed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $latestTraining->assessment_passed ? 'Score: ' . $latestTraining->assessment_score . '%' : 'Failed' }}
                                    </div>
                                @else
                                    <div class="text-sm text-gray-400 italic">No training recorded</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->inductionTrainings->sum('training_duration_hours') }} hours</div>
                                <div class="text-sm text-gray-500">Target: 32 hours</div>
                                <div class="text-xs text-blue-600">{{ number_format(($employee->inductionTrainings->sum('training_duration_hours') / 32) * 100, 1) }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($percentage >= 100)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                @elseif($completedCount > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        In Progress
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Started
                                    </span>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">
                                    @if($nextTraining = $employee->inductionTrainings->where('status', 'scheduled')->sortBy('training_date')->first())
                                        Next: {{ Str::headline($nextTraining->training_type) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="showEmployeeTraining({{ $employee->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="scheduleTraining({{ $employee->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        <i data-feather="calendar" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="generateCertificate({{ $employee->id }})" 
                                            class="text-purple-600 hover:text-purple-900">
                                        <i data-feather="award" class="w-4 h-4"></i>
                                    </button>
                                    <button onclick="uploadMaterials({{ $employee->id }})" 
                                            class="text-orange-600 hover:text-orange-900">
                                        <i data-feather="upload" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i data-feather="award" class="w-12 h-12 mx-auto text-gray-400 mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm">No approved employees to manage training for.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $employees->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">{{ $employees->firstItem() }}</span> to 
                            <span class="font-medium">{{ $employees->lastItem() }}</span> of 
                            <span class="font-medium">{{ $employees->total() }}</span> results
                        </p>
                    </div>
                    <div>
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Statistics Modal -->
<div id="statisticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Training Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Employees:</span>
                    <span class="text-sm font-medium" id="modalTotalEmployees">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Employees Trained:</span>
                    <span class="text-sm font-medium" id="modalTrainedCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Completion Rate:</span>
                    <span class="text-sm font-medium" id="modalCompletionRate">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Average Hours:</span>
                    <span class="text-sm font-medium" id="modalAvgHours">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Upcoming Trainings:</span>
                    <span class="text-sm font-medium" id="modalUpcomingCount">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Overdue Trainings:</span>
                    <span class="text-sm font-medium" id="modalOverdueCount">-</span>
                </div>
            </div>
            <div class="border-t pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Training Types</h4>
                <div class="space-y-2" id="trainingTypesStats">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideStatisticsModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Training Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Training</h3>
            <form id="scheduleForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Employees</label>
                    <div class="border rounded-lg p-3 max-h-32 overflow-y-auto">
                        <div id="employeeCheckboxes" class="space-y-2">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Training Type</label>
                    <select name="training_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Type</option>
                        <option value="company_policies">Company Policies</option>
                        <option value="safety_procedures">Safety Procedures</option>
                        <option value="job_specific">Job Specific</option>
                        <option value="compliance">Compliance</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Training Title</label>
                    <input type="text" name="training_title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                    <input type="date" name="scheduled_date" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trainer Name</label>
                    <input type="text" name="trainer_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (Hours)</label>
                    <input type="number" name="estimated_duration_hours" required min="0.5" max="40" step="0.5"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="hideScheduleModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="scheduleBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <span id="scheduleBtnText">Schedule</span>
                        <div id="scheduleBtnLoader" class="hidden ml-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Calendar Modal -->
<div id="calendarModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Training Calendar</h3>
            <div class="space-y-2" id="calendarEvents">
                <!-- Will be populated dynamically -->
            </div>
            <div class="flex justify-end mt-6">
                <button onclick="hideCalendarModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Induction Training Management System
class InductionTrainingManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeFeather();
        this.loadStatistics();
    }

    initializeFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', () => this.filterEmployees());

        // Filter functionality
        const filters = ['workStationFilter', 'trainingTypeFilter', 'statusFilter'];
        filters.forEach(filterId => {
            const filter = document.getElementById(filterId);
            filter.addEventListener('change', () => this.filterEmployees());
        });

        // Schedule form
        const scheduleForm = document.getElementById('scheduleForm');
        scheduleForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.scheduleTraining();
        });
    }

    async loadStatistics() {
        try {
            const response = await fetch('/induction-training/statistics');
            const result = await response.json();

            if (result.success) {
                const stats = result.statistics;
                
                // Update main page statistics
                document.getElementById('totalEmployees').textContent = stats.total_employees;
                document.getElementById('trainedCount').textContent = stats.employees_trained;
                document.getElementById('completionRate').textContent = stats.training_completion_rate + '%';
                document.getElementById('avgHours').textContent = stats.average_training_hours;
                document.getElementById('upcomingCount').textContent = stats.upcoming_trainings;

                // Update modal statistics
                document.getElementById('modalTotalEmployees').textContent = stats.total_employees;
                document.getElementById('modalTrainedCount').textContent = stats.employees_trained;
                document.getElementById('modalCompletionRate').textContent = stats.training_completion_rate + '%';
                document.getElementById('modalAvgHours').textContent = stats.average_training_hours;
                document.getElementById('modalUpcomingCount').textContent = stats.upcoming_trainings;
                document.getElementById('modalOverdueCount').textContent = stats.overdue_trainings;

                // Update training types
                const typesContainer = document.getElementById('trainingTypesStats');
                typesContainer.innerHTML = '';
                Object.entries(stats.training_types).forEach(([type, count]) => {
                    const typeLabel = this.getTrainingTypeLabel(type);
                    typesContainer.innerHTML += `
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">${typeLabel}:</span>
                            <span class="text-sm font-medium">${count}</span>
                        </div>
                    `;
                });
            }
        } catch (error) {
            console.error('Failed to load statistics:', error);
        }
    }

    getTrainingTypeLabel(type) {
        const labels = {
            'company_policies': 'Company Policies',
            'safety_procedures': 'Safety Procedures',
            'job_specific': 'Job Specific',
            'compliance': 'Compliance',
            'other': 'Other'
        };
        return labels[type] || type;
    }

    filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const workStationFilter = document.getElementById('workStationFilter').value;
        const trainingTypeFilter = document.getElementById('trainingTypeFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const employeeRows = document.querySelectorAll('.employee-row');

        employeeRows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const workStation = row.dataset.workstation;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesWorkStation = !workStationFilter || workStation === workStationFilter;
            const matchesTrainingType = !trainingTypeFilter; // Placeholder
            const matchesStatus = !statusFilter; // Placeholder

            if (matchesSearch && matchesWorkStation && matchesTrainingType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    async scheduleTraining() {
        const form = document.getElementById('scheduleForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Get selected employee IDs
        const selectedEmployees = [];
        const checkboxes = form.querySelectorAll('input[name="employee_ids[]"]:checked');
        checkboxes.forEach(checkbox => {
            selectedEmployees.push(checkbox.value);
        });

        if (selectedEmployees.length === 0) {
            this.showNotification('Please select at least one employee', 'error');
            return;
        }

        data.employee_ids = selectedEmployees;

        this.setScheduleLoadingState(true);

        try {
            const response = await fetch('/induction-training/schedule', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message, 'success');
                hideScheduleModal();
                setTimeout(() => window.location.reload(), 1500);
            } else {
                this.showNotification(result.message || 'Scheduling failed', 'error');
            }
        } catch (error) {
            console.error('Scheduling error:', error);
            this.showNotification('An error occurred during scheduling', 'error');
        } finally {
            this.setScheduleLoadingState(false);
        }
    }

    setScheduleLoadingState(loading) {
        const btnText = document.getElementById('scheduleBtnText');
        const btnLoader = document.getElementById('scheduleBtnLoader');
        const scheduleBtn = document.getElementById('scheduleBtn');

        if (loading) {
            btnText.textContent = 'Scheduling...';
            btnLoader.classList.remove('hidden');
            scheduleBtn.disabled = true;
        } else {
            btnText.textContent = 'Schedule';
            btnLoader.classList.add('hidden');
            scheduleBtn.disabled = false;
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Modal functions
function showStatisticsModal() {
    document.getElementById('statisticsModal').classList.remove('hidden');
}

function hideStatisticsModal() {
    document.getElementById('statisticsModal').classList.add('hidden');
}

function showScheduleModal() {
    document.getElementById('scheduleModal').classList.remove('hidden');
    loadEmployeeCheckboxes();
}

function hideScheduleModal() {
    document.getElementById('scheduleModal').classList.add('hidden');
    document.getElementById('scheduleForm').reset();
}

function showCalendarModal() {
    document.getElementById('calendarModal').classList.remove('hidden');
    loadCalendarEvents();
}

function hideCalendarModal() {
    document.getElementById('calendarModal').classList.add('hidden');
}

async function loadEmployeeCheckboxes() {
    try {
        const response = await fetch('/induction-training/requiring-training');
        const result = await response.json();

        if (result.success) {
            const container = document.getElementById('employeeCheckboxes');
            container.innerHTML = '';
            
            result.employees.forEach(employee => {
                const checkbox = document.createElement('div');
                checkbox.className = 'flex items-center';
                checkbox.innerHTML = `
                    <input type="checkbox" name="employee_ids[]" value="${employee.id}" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-900">${employee.first_name} ${employee.surname} (${employee.employee_number})</label>
                `;
                container.appendChild(checkbox);
            });
        }
    } catch (error) {
        console.error('Failed to load employees:', error);
    }
}

async function loadCalendarEvents() {
    try {
        const response = await fetch('/induction-training/calendar');
        const result = await response.json();

        if (result.success) {
            const container = document.getElementById('calendarEvents');
            container.innerHTML = '';
            
            result.events.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = 'p-3 bg-gray-50 rounded';
                eventDiv.innerHTML = `
                    <div class="text-sm font-medium">${event.title}</div>
                    <div class="text-xs text-gray-500">${event.start}</div>
                    <div class="text-xs text-gray-400">${event.employees} employees</div>
                    <div class="text-xs text-blue-600">${this.getTrainingTypeLabel(event.type)}</div>
                `;
                container.appendChild(eventDiv);
            });
        }
    } catch (error) {
        console.error('Failed to load calendar events:', error);
    }
}

// Action functions
function showEmployeeTraining(employeeId) {
    window.location.href = `/induction-training/employee/${employeeId}`;
}

function scheduleTraining(employeeId) {
    showScheduleModal();
}

function generateCertificate(employeeId) {
    window.inductionTrainingManager.showNotification('Certificate generation feature coming soon', 'info');
}

function uploadMaterials(employeeId) {
    window.inductionTrainingManager.showNotification('Materials upload feature coming soon', 'info');
}

// Initialize induction training manager
document.addEventListener('DOMContentLoaded', function() {
    window.inductionTrainingManager = new InductionTrainingManager();
});
</script>
@endpush
