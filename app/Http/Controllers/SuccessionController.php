<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Employee;
use App\Models\TalentPool;
use App\Models\TalentPoolMember;
use App\Models\SuccessionReadiness;
use App\Models\CareerPath;
use App\Models\CareerPathLevel;
use App\Models\CareerPathMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuccessionController extends Controller
{
    public function talentPools()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $pools = TalentPool::with(['members.employee'])
            ->orderBy('name')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name', 'position', 'department']);

        $allMembers = $pools->flatMap->members;

        $stats = [
            'total_pools' => $pools->count(),
            'total_members' => $allMembers->count(),
            'ready_now' => $allMembers->where('readiness', 'ready_now')->count(),
            'high_potentials' => $pools->where('type', 'high_potential')->flatMap->members->count(),
        ];

        return view('succession.talent-pools', compact('currentClient', 'pools', 'employees', 'stats'));
    }

    public function storeTalentPool(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(TalentPool::TYPES)),
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            TalentPool::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status ?: 'active',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('succession.talent-pools')->with('success', 'Talent pool created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create talent pool: ' . $e->getMessage())->withInput();
        }
    }

    public function updateTalentPool(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $pool = TalentPool::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(TalentPool::TYPES)),
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $pool->update([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status ?: 'active',
            ]);

            return redirect()->route('succession.talent-pools')->with('success', 'Talent pool updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update talent pool: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyTalentPool($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $pool = TalentPool::where('client_id', $clientId)->findOrFail($id);
        $pool->delete();

        return redirect()->route('succession.talent-pools')->with('success', 'Talent pool deleted successfully!');
    }

    public function storeMember(Request $request, $poolId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $pool = TalentPool::where('client_id', $clientId)->findOrFail($poolId);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'readiness' => 'nullable|in:' . implode(',', array_keys(TalentPoolMember::READINESS)),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::where('client_id', $clientId)->find($request->employee_id);
        if (!$employee) {
            return redirect()->back()->with('error', 'Invalid employee for this client.')->withInput();
        }

        $exists = TalentPoolMember::where('client_id', $clientId)
            ->where('talent_pool_id', $pool->id)
            ->where('employee_id', $request->employee_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This employee is already in the pool.')->withInput();
        }

        try {
            TalentPoolMember::create([
                'client_id' => $clientId,
                'talent_pool_id' => $pool->id,
                'employee_id' => $request->employee_id,
                'readiness' => $request->readiness ?: 'developing',
                'notes' => $request->notes,
                'added_by' => auth()->id(),
            ]);

            return redirect()->route('succession.talent-pools')->with('success', 'Member added to talent pool!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add member: ' . $e->getMessage())->withInput();
        }
    }

    public function updateMember(Request $request, $memberId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $member = TalentPoolMember::where('client_id', $clientId)->findOrFail($memberId);

        $validator = Validator::make($request->all(), [
            'readiness' => 'required|in:' . implode(',', array_keys(TalentPoolMember::READINESS)),
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $member->update([
            'readiness' => $request->readiness,
            'notes' => $request->notes,
        ]);

        return redirect()->route('succession.talent-pools')->with('success', 'Member readiness updated.');
    }

    public function destroyMember($memberId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $member = TalentPoolMember::where('client_id', $clientId)->findOrFail($memberId);
        $member->delete();

        return redirect()->route('succession.talent-pools')->with('success', 'Member removed from talent pool.');
    }

    public function readiness(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $department = $request->get('department');

        $query = SuccessionReadiness::with(['employee', 'pool'])
            ->orderByRaw("FIELD(readiness, 'ready_now', 'ready_1_2', 'ready_2_3', 'development')")
            ->orderBy('employee_id');

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $assessments = $query->get();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name', 'position', 'department']);

        $pools = TalentPool::orderBy('name')->get(['id', 'name']);

        $departments = Employee::where('client_id', $clientId)
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->select('department')
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->values()
            ->all();

        $stats = [
            'ready_now' => $assessments->where('readiness', 'ready_now')->count(),
            'ready_1_2' => $assessments->where('readiness', 'ready_1_2')->count(),
            'ready_2_3' => $assessments->where('readiness', 'ready_2_3')->count(),
            'development' => $assessments->where('readiness', 'development')->count(),
            'total' => $assessments->count(),
        ];

        return view('succession.readiness', compact('currentClient', 'assessments', 'employees', 'pools', 'departments', 'department', 'stats'));
    }

    public function storeReadiness(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'pool_id' => 'nullable|exists:talent_pools,id',
            'current_role' => 'nullable|string|max:255',
            'target_role' => 'nullable|string|max:255',
            'readiness' => 'required|in:' . implode(',', array_keys(SuccessionReadiness::READINESS)),
            'development_needs' => 'nullable|string',
            'assessment_date' => 'nullable|date',
            'status' => 'nullable|in:active,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::where('client_id', $clientId)->find($request->employee_id);
        if (!$employee) {
            return redirect()->back()->with('error', 'Invalid employee for this client.')->withInput();
        }

        try {
            SuccessionReadiness::create([
                'client_id' => $clientId,
                'employee_id' => $request->employee_id,
                'pool_id' => $request->pool_id ?: null,
                'current_role' => $request->current_role ?: $employee->position,
                'target_role' => $request->target_role,
                'readiness' => $request->readiness,
                'development_needs' => $request->development_needs,
                'assessment_date' => $request->assessment_date ?: now()->toDateString(),
                'status' => $request->status ?: 'active',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('succession.readiness')->with('success', 'Readiness assessment created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create readiness assessment: ' . $e->getMessage())->withInput();
        }
    }

    public function updateReadiness(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $assessment = SuccessionReadiness::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'pool_id' => 'nullable|exists:talent_pools,id',
            'current_role' => 'nullable|string|max:255',
            'target_role' => 'nullable|string|max:255',
            'readiness' => 'required|in:' . implode(',', array_keys(SuccessionReadiness::READINESS)),
            'development_needs' => 'nullable|string',
            'assessment_date' => 'nullable|date',
            'status' => 'nullable|in:active,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $assessment->update([
                'employee_id' => $request->employee_id,
                'pool_id' => $request->pool_id ?: null,
                'current_role' => $request->current_role,
                'target_role' => $request->target_role,
                'readiness' => $request->readiness,
                'development_needs' => $request->development_needs,
                'assessment_date' => $request->assessment_date,
                'status' => $request->status ?: 'active',
            ]);

            return redirect()->route('succession.readiness')->with('success', 'Readiness assessment updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update readiness assessment: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyReadiness($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $assessment = SuccessionReadiness::where('client_id', $clientId)->findOrFail($id);
        $assessment->delete();

        return redirect()->route('succession.readiness')->with('success', 'Readiness assessment deleted successfully!');
    }

    public function exportReadiness(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $department = $request->get('department');

        $query = SuccessionReadiness::with(['employee', 'pool'])->orderBy('readiness');

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        $assessments = $query->get();

        $rows = [
            ['Employee', 'Department', 'Current Role', 'Target Role', 'Talent Pool', 'Readiness', 'Development Needs', 'Assessment Date'],
        ];

        foreach ($assessments as $assessment) {
            $rows[] = [
                ($assessment->employee->first_name ?? '') . ' ' . ($assessment->employee->last_name ?? ''),
                $assessment->employee->department ?? '',
                $assessment->current_role ?? '',
                $assessment->target_role ?? '',
                $assessment->pool->name ?? '',
                SuccessionReadiness::READINESS[$assessment->readiness] ?? $assessment->readiness,
                $assessment->development_needs ?? '',
                $assessment->assessment_date?->format('Y-m-d') ?? '',
            ];
        }

        $fileName = 'readiness-report-' . date('Y-m-d') . '.csv';
        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function careerPaths()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $currentClient = Client::find($clientId);

        $paths = CareerPath::with(['levels', 'members.employee'])
            ->orderBy('name')
            ->get();

        $employees = Employee::where('client_id', $clientId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get(['id', 'employee_id', 'first_name', 'last_name', 'position', 'department']);

        $stats = [
            'total_paths' => $paths->count(),
            'total_levels' => $paths->sum(fn ($p) => $p->levels->count()),
            'total_members' => $paths->sum(fn ($p) => $p->members->count()),
            'active_paths' => $paths->where('status', 'active')->count(),
        ];

        return view('succession.career-paths', compact('currentClient', 'paths', 'employees', 'stats'));
    }

    public function storeCareerPath(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            CareerPath::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'department' => $request->department,
                'description' => $request->description,
                'status' => $request->status ?: 'active',
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('succession.career-paths')->with('success', 'Career path created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create career path: ' . $e->getMessage())->withInput();
        }
    }

    public function updateCareerPath(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $path = CareerPath::where('client_id', $clientId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $path->update([
                'name' => $request->name,
                'department' => $request->department,
                'description' => $request->description,
                'status' => $request->status ?: 'active',
            ]);

            return redirect()->route('succession.career-paths')->with('success', 'Career path updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update career path: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyCareerPath($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $path = CareerPath::where('client_id', $clientId)->findOrFail($id);
        $path->delete();

        return redirect()->route('succession.career-paths')->with('success', 'Career path deleted successfully!');
    }

    public function storeLevel(Request $request, $pathId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $path = CareerPath::where('client_id', $clientId)->findOrFail($pathId);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'typical_time' => 'nullable|string|max:100',
            'competencies' => 'nullable|string',
            'responsibilities' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $nextOrder = ($path->levels()->max('level_order') ?? 0) + 1;

        CareerPathLevel::create([
            'client_id' => $clientId,
            'career_path_id' => $path->id,
            'level_order' => $nextOrder,
            'title' => $request->title,
            'typical_time' => $request->typical_time,
            'competencies' => $request->competencies,
            'responsibilities' => $request->responsibilities,
        ]);

        return redirect()->route('succession.career-paths')->with('success', 'Career level added successfully!');
    }

    public function updateLevel(Request $request, $levelId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $level = CareerPathLevel::where('client_id', $clientId)->findOrFail($levelId);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'typical_time' => 'nullable|string|max:100',
            'competencies' => 'nullable|string',
            'responsibilities' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $level->update([
            'title' => $request->title,
            'typical_time' => $request->typical_time,
            'competencies' => $request->competencies,
            'responsibilities' => $request->responsibilities,
        ]);

        return redirect()->route('succession.career-paths')->with('success', 'Career level updated successfully!');
    }

    public function destroyLevel($levelId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $level = CareerPathLevel::where('client_id', $clientId)->findOrFail($levelId);
        $level->delete();

        return redirect()->route('succession.career-paths')->with('success', 'Career level deleted successfully!');
    }

    public function storePathMember(Request $request, $pathId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $path = CareerPath::where('client_id', $clientId)->findOrFail($pathId);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'current_level_order' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $employee = Employee::where('client_id', $clientId)->find($request->employee_id);
        if (!$employee) {
            return redirect()->back()->with('error', 'Invalid employee for this client.')->withInput();
        }

        $exists = CareerPathMember::where('client_id', $clientId)
            ->where('career_path_id', $path->id)
            ->where('employee_id', $request->employee_id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This employee is already on the career path.')->withInput();
        }

        try {
            CareerPathMember::create([
                'client_id' => $clientId,
                'career_path_id' => $path->id,
                'employee_id' => $request->employee_id,
                'current_level_order' => $request->current_level_order ?: 1,
            ]);

            return redirect()->route('succession.career-paths')->with('success', 'Employee added to career path!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add employee to career path: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePathMember(Request $request, $memberId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $member = CareerPathMember::where('client_id', $clientId)->findOrFail($memberId);

        $validator = Validator::make($request->all(), [
            'current_level_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $member->update(['current_level_order' => $request->current_level_order]);

        return redirect()->route('succession.career-paths')->with('success', 'Employee level updated.');
    }

    public function destroyPathMember($memberId)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->back()->with('error', 'Please select a client first.');
        }

        $member = CareerPathMember::where('client_id', $clientId)->findOrFail($memberId);
        $member->delete();

        return redirect()->route('succession.career-paths')->with('success', 'Employee removed from career path.');
    }
}
