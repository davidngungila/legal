@extends('layouts.app')

@section('title', 'Edit HR Competency Interview Assessment - Orvion HRIS')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">Edit HR Competency Interview Assessment</h1>
            <p class="text-gray-600 mt-2">Update the competency interview assessment details</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('hr-interview.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back
            </a>
            <a href="{{ route('hr-interview.show', $hrCompetencyInterview) }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                View
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form id="hrInterviewForm" class="p-6 space-y-8">
            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Number
                        </label>
                        <input type="text" id="interviewNumber" readonly
                               class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                               placeholder="Interview number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Job Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="job_title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="job_title_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interview Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="interview_date" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="interview_date_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Candidate Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="candidate_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="candidate_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Interviewer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="interviewer_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="interviewer_name_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Military Service Status <span class="text-red-500">*</span>
                        </label>
                        <select name="military_service_status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Status</option>
                            <option value="completed">Completed</option>
                            <option value="didnt_attend">Didn't Attend</option>
                            <option value="na">N/A</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="military_service_status_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Place of Recruitment <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="place_of_recruitment" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="place_of_recruitment_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Total Years Experience <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_years_experience" required min="0" max="50"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="total_years_experience_error"></span>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Military Service Certificate
                    </label>
                    <input type="file" name="military_certificate" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @if($hrCompetencyInterview->military_certificate_path)
                        <div class="mt-2 text-sm">
                            <a class="text-indigo-600 hover:text-indigo-800" href="{{ asset('storage/' . $hrCompetencyInterview->military_certificate_path) }}" target="_blank" rel="noopener noreferrer">
                                View current file
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">Rating Scale</h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-2 text-xs">
                    <div class="flex items-center">
                        <span class="font-medium">0</span> - N/A
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium">1</span> - Below Average
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium">2</span> - Average
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium">3</span> - Good
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium">4</span> - Very Good
                    </div>
                    <div class="flex items-center">
                        <span class="font-medium">5</span> - Outstanding
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Competency Assessment</h2>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Education and Job Knowledge</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Education and Job Knowledge <span class="text-red-500">*</span>
                            </label>
                            <select name="education_job_knowledge" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="education_job_knowledge_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="education_job_knowledge_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Experience and Achievement</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Relevant Job Experience <span class="text-red-500">*</span>
                            </label>
                            <select name="relevant_job_experience" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="relevant_job_experience_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Major Previous Achievement <span class="text-red-500">*</span>
                            </label>
                            <select name="major_previous_achievement" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="major_previous_achievement_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Language Fluency <span class="text-red-500">*</span>
                            </label>
                            <select name="language_fluency" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="language_fluency_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="language_fluency_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Core Competencies</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Interactive Communication <span class="text-red-500">*</span>
                            </label>
                            <select name="interactive_communication" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="interactive_communication_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="interactive_communication_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Accountability <span class="text-red-500">*</span>
                            </label>
                            <select name="accountability" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="accountability_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="accountability_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Work Excellence <span class="text-red-500">*</span>
                            </label>
                            <select name="work_excellence" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="work_excellence_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="work_excellence_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Functional Competencies <span class="text-red-500">*</span>
                            </label>
                            <select name="functional_competencies" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="functional_competencies_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="functional_competencies_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Planning and Problem Solving</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Planning & Organizing <span class="text-red-500">*</span>
                            </label>
                            <select name="planning_organizing" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="planning_organizing_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="planning_organizing_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Problem Solving <span class="text-red-500">*</span>
                            </label>
                            <select name="problem_solving" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="problem_solving_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="problem_solving_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Attention to Details <span class="text-red-500">*</span>
                            </label>
                            <select name="attention_to_details" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="attention_to_details_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="attention_to_details_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Additional Competencies</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Multitasking <span class="text-red-500">*</span>
                            </label>
                            <select name="multitasking" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="multitasking_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="multitasking_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Continuous Improvement <span class="text-red-500">*</span>
                            </label>
                            <select name="continuous_improvement" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="continuous_improvement_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="continuous_improvement_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Compliance <span class="text-red-500">*</span>
                            </label>
                            <select name="compliance" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="compliance_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="compliance_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Creative Innovation <span class="text-red-500">*</span>
                            </label>
                            <select name="creative_innovation" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="creative_innovation_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="creative_innovation_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Negotiation <span class="text-red-500">*</span>
                            </label>
                            <select name="negotiation" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="negotiation_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="negotiation_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Teamwork <span class="text-red-500">*</span>
                            </label>
                            <select name="teamwork" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="teamwork_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="teamwork_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Adaptability & Flexibility <span class="text-red-500">*</span>
                            </label>
                            <select name="adaptability_flexibility" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="adaptability_flexibility_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Comment
                            </label>
                            <textarea name="adaptability_flexibility_comment" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Leadership <span class="text-red-500">*</span>
                            </label>
                            <select name="leadership" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="leadership_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Managing & Developing People <span class="text-red-500">*</span>
                            </label>
                            <select name="managing_developing_people" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="managing_developing_people_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Managing Change <span class="text-red-500">*</span>
                            </label>
                            <select name="managing_change" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="managing_change_error"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Making Decisions <span class="text-red-500">*</span>
                            </label>
                            <select name="making_decisions" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Select Rating</option>
                                @for($i = 0; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} - {{ $i == 0 ? 'N/A' : ($i == 1 ? 'Below Average' : ($i == 2 ? 'Average' : ($i == 3 ? 'Good' : ($i == 4 ? 'Very Good' : 'Outstanding')))) }}</option>
                                @endfor
                            </select>
                            <span class="text-red-500 text-sm hidden" id="making_decisions_error"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 pb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Personal & Background</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Relative Inside Client <span class="text-red-500">*</span>
                        </label>
                        <select name="relative_inside_client" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="relative_inside_client_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Relative Name
                        </label>
                        <input type="text" name="relative_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Birthplace <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="birthplace" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="birthplace_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Residence <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="residence" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="text-red-500 text-sm hidden" id="residence_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Employed Before <span class="text-red-500">*</span>
                        </label>
                        <select name="employed_before" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="employed_before_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reference Checking <span class="text-red-500">*</span>
                        </label>
                        <select name="reference_checking" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="reference_checking_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Recruiter Recommendation <span class="text-red-500">*</span>
                        </label>
                        <select name="recruiter_recommendation" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Recommendation</option>
                            <option value="accepted">Accepted</option>
                            <option value="not_accepted">Not Accepted</option>
                            <option value="waiting_list">Waiting List</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="recruiter_recommendation_error"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Recommended Job Title
                        </label>
                        <input type="text" name="recommended_job_title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Salary
                        </label>
                        <input type="number" name="current_salary" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Required Notice Days
                        </label>
                        <input type="number" name="required_notice_days" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Current Employer Entity <span class="text-red-500">*</span>
                        </label>
                        <select name="current_employer_entity" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select Entity</option>
                            <option value="government">Government</option>
                            <option value="private">Private</option>
                        </select>
                        <span class="text-red-500 text-sm hidden" id="current_employer_entity_error"></span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('hr-interview.show', $hrCompetencyInterview) }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <span id="btnText">Update Assessment</span>
                    <div id="btnLoader" class="hidden ml-2">
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
@endsection

@push('scripts')
<script>
class HrInterviewEditManager {
    constructor(data, updateUrl) {
        this.data = data || {};
        this.updateUrl = updateUrl;
        this.form = document.getElementById('hrInterviewForm');
        this.submitBtn = document.getElementById('submitBtn');
        this.btnText = document.getElementById('btnText');
        this.btnLoader = document.getElementById('btnLoader');
        this.init();
    }

    init() {
        this.populateForm();
        this.setupEventListeners();
        this.setupFormValidation();
    }

    populateForm() {
        const interviewNumberField = document.getElementById('interviewNumber');
        if (interviewNumberField) {
            interviewNumberField.value = this.data.interview_number || '';
        }

        const elements = Array.from(this.form.elements || []);
        elements.forEach((el) => {
            if (!el.name) return;
            if (!(el.name in this.data)) return;
            if (el.type === 'file') return;

            let value = this.data[el.name];
            if (value === null || typeof value === 'undefined') return;

            if (el.type === 'date' && typeof value === 'string') {
                value = value.includes('T') ? value.split('T')[0] : value;
            }

            el.value = value;
        });
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        const inputs = this.form.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    setupFormValidation() {
        const experienceField = document.querySelector('input[name="total_years_experience"]');
        if (experienceField) {
            experienceField.addEventListener('input', () => {
                if (experienceField.value && (experienceField.value < 0 || experienceField.value > 50)) {
                    this.showFieldError('total_years_experience', 'Experience must be between 0 and 50 years');
                } else {
                    this.clearFieldError(experienceField);
                }
            });
        }

        const salaryField = document.querySelector('input[name="current_salary"]');
        if (salaryField) {
            salaryField.addEventListener('input', () => {
                if (salaryField.value && salaryField.value < 0) {
                    this.showFieldError('current_salary', 'Salary must be a positive number');
                } else {
                    this.clearFieldError(salaryField);
                }
            });
        }

        const noticeDaysField = document.querySelector('input[name="required_notice_days"]');
        if (noticeDaysField) {
            noticeDaysField.addEventListener('input', () => {
                if (noticeDaysField.value && noticeDaysField.value < 0) {
                    this.showFieldError('required_notice_days', 'Notice days must be a positive number');
                } else {
                    this.clearFieldError(noticeDaysField);
                }
            });
        }
    }

    validateField(field) {
        const value = (field.value || '').toString().trim();
        const fieldName = field.name;
        this.clearFieldError(field);

        if (field.required && !value) {
            this.showFieldError(fieldName, 'This field is required');
            return false;
        }

        if (fieldName === 'total_years_experience' && value && (value < 0 || value > 50)) {
            this.showFieldError(fieldName, 'Experience must be between 0 and 50 years');
            return false;
        }

        if (fieldName === 'current_salary' && value && value < 0) {
            this.showFieldError(fieldName, 'Salary must be a positive number');
            return false;
        }

        if (fieldName === 'required_notice_days' && value && value < 0) {
            this.showFieldError(fieldName, 'Notice days must be a positive number');
            return false;
        }

        return true;
    }

    showFieldError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        const errorElement = document.getElementById(`${fieldName}_error`);

        if (field) {
            field.classList.add('border-red-500');
        }

        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }
    }

    clearFieldError(field) {
        const fieldName = field.name;
        const errorElement = document.getElementById(`${fieldName}_error`);

        field.classList.remove('border-red-500');

        if (errorElement) {
            errorElement.classList.add('hidden');
        }
    }

    async submitForm() {
        const inputs = this.form.querySelectorAll('input[required], select[required]');
        let isValid = true;
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            this.showNotification('Please correct the errors in the form', 'error');
            return;
        }

        this.setLoadingState(true);

        try {
            const formData = new FormData(this.form);
            formData.append('_method', 'PUT');

            const response = await fetch(this.updateUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification(result.message || 'Interview successfully updated!', 'success');
                setTimeout(() => {
                    window.location.href = @json(route('hr-interview.show', $hrCompetencyInterview));
                }, 500);
            } else {
                if (result.errors) {
                    this.displayServerErrors(result.errors);
                } else {
                    this.showNotification(result.message || 'Update failed', 'error');
                }
            }
        } catch (error) {
            console.error('Update error:', error);
            this.showNotification('An error occurred during update', 'error');
        } finally {
            this.setLoadingState(false);
        }
    }

    displayServerErrors(errors) {
        Object.keys(errors).forEach(fieldName => {
            this.showFieldError(fieldName, errors[fieldName][0]);
        });
    }

    setLoadingState(loading) {
        if (loading) {
            this.btnText.textContent = 'Updating...';
            this.btnLoader.classList.remove('hidden');
            this.submitBtn.disabled = true;
        } else {
            this.btnText.textContent = 'Update Assessment';
            this.btnLoader.classList.add('hidden');
            this.submitBtn.disabled = false;
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
        setTimeout(() => notification.remove(), 3000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    window.hrInterviewEditManager = new HrInterviewEditManager(
        @json($hrCompetencyInterview),
        @json(route('hr-interview.update', $hrCompetencyInterview))
    );
});
</script>
@endpush

