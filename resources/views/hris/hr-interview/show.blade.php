@extends('layouts.app')

@section('title', 'HR Competency Interview - Orvion HRIS')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 font-manrope">HR Competency Interview</h1>
            <p class="text-gray-600 mt-2">Interview number: <span class="font-semibold">{{ $hrCompetencyInterview->interview_number }}</span></p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('hr-interview.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Back
            </a>
            @if($hrCompetencyInterview->canBeEdited())
                <a href="{{ route('hr-interview.edit', $hrCompetencyInterview) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    Edit
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Job Title</div>
                        <div class="font-medium text-gray-900">{{ $hrCompetencyInterview->job_title }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Interview Date</div>
                        <div class="font-medium text-gray-900">{{ optional($hrCompetencyInterview->interview_date)->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Candidate</div>
                        <div class="font-medium text-gray-900">{{ $hrCompetencyInterview->candidate_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Interviewer</div>
                        <div class="font-medium text-gray-900">{{ $hrCompetencyInterview->interviewer_name }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Place of Recruitment</div>
                        <div class="font-medium text-gray-900">{{ $hrCompetencyInterview->place_of_recruitment }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Total Years Experience</div>
                        <div class="font-medium text-gray-900">{{ $hrCompetencyInterview->total_years_experience }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Competency Ratings</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @php
                                $ratings = [
                                    'Education & Job Knowledge' => $hrCompetencyInterview->education_job_knowledge,
                                    'Relevant Job Experience' => $hrCompetencyInterview->relevant_job_experience,
                                    'Major Previous Achievement' => $hrCompetencyInterview->major_previous_achievement,
                                    'Language Fluency' => $hrCompetencyInterview->language_fluency,
                                    'Interactive Communication' => $hrCompetencyInterview->interactive_communication,
                                    'Accountability' => $hrCompetencyInterview->accountability,
                                    'Work Excellence' => $hrCompetencyInterview->work_excellence,
                                    'Functional Competencies' => $hrCompetencyInterview->functional_competencies,
                                    'Planning & Organizing' => $hrCompetencyInterview->planning_organizing,
                                    'Problem Solving' => $hrCompetencyInterview->problem_solving,
                                    'Attention to Details' => $hrCompetencyInterview->attention_to_details,
                                    'Multitasking' => $hrCompetencyInterview->multitasking,
                                    'Continuous Improvement' => $hrCompetencyInterview->continuous_improvement,
                                    'Compliance' => $hrCompetencyInterview->compliance,
                                    'Creative Innovation' => $hrCompetencyInterview->creative_innovation,
                                    'Negotiation' => $hrCompetencyInterview->negotiation,
                                    'Teamwork' => $hrCompetencyInterview->teamwork,
                                    'Adaptability & Flexibility' => $hrCompetencyInterview->adaptability_flexibility,
                                    'Leadership' => $hrCompetencyInterview->leadership,
                                    'Managing & Developing People' => $hrCompetencyInterview->managing_developing_people,
                                    'Managing Change' => $hrCompetencyInterview->managing_change,
                                    'Making Decisions' => $hrCompetencyInterview->making_decisions,
                                    'Overall Rating' => $hrCompetencyInterview->overall_rating,
                                ];
                            @endphp
                            @foreach($ratings as $label => $value)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ $label }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ is_null($value) ? '-' : $value }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status</h2>
                <div class="text-sm">
                    <div class="text-gray-500 mb-1">Current status</div>
                    <div class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $hrCompetencyInterview->status)) }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Documents</h2>
                <div class="text-sm space-y-3">
                    <div>
                        <div class="text-gray-500">Military service</div>
                        @if($hrCompetencyInterview->military_certificate_path)
                            <a class="text-indigo-600 hover:text-indigo-800" href="{{ asset('storage/' . $hrCompetencyInterview->military_certificate_path) }}" target="_blank" rel="noopener noreferrer">
                                View file
                            </a>
                        @else
                            <div class="text-gray-900">-</div>
                        @endif
                    </div>
                    <div>
                        <div class="text-gray-500">Signed file</div>
                        @if($hrCompetencyInterview->signed_file_path)
                            <a class="text-indigo-600 hover:text-indigo-800" href="{{ asset('storage/' . $hrCompetencyInterview->signed_file_path) }}" target="_blank" rel="noopener noreferrer">
                                View file
                            </a>
                        @else
                            <div class="text-gray-900">-</div>
                        @endif
                    </div>
                    @if($hrCompetencyInterview->interviewer_signature_path)
                    <div>
                        <div class="text-gray-500">Interviewer Signature</div>
                        <img src="{{ Storage::url($hrCompetencyInterview->interviewer_signature_path) }}"
                             alt="Interviewer Signature"
                             class="max-h-20 border border-gray-200 rounded bg-white mt-1">
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Background</h2>
                <div class="text-sm space-y-3">
                    <div>
                        <div class="text-gray-500">Birthplace</div>
                        <div class="text-gray-900">{{ $hrCompetencyInterview->birthplace ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Residence</div>
                        <div class="text-gray-900">{{ $hrCompetencyInterview->residence ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Relative Inside Client</div>
                        <div class="text-gray-900">{{ $hrCompetencyInterview->relative_inside_client ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Relative Name</div>
                        <div class="text-gray-900">{{ $hrCompetencyInterview->relative_name ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

