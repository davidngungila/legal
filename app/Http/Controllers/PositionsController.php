<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionsController extends Controller
{
    public function index()
    {
        $clientId = session('current_client_id');
        
        $positions = Position::with('department')
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->orderBy('title')
            ->get();
            
        $departments = Department::when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->orderBy('name')
            ->get();
            
        // Calculate stats
        $stats = [
            'total' => $positions->count(),
            'active' => $positions->where('is_active', true)->count(),
            'inactive' => $positions->where('is_active', false)->count(),
            'with_salary' => $positions->filter(fn($p) => $p->min_salary || $p->max_salary)->count()
        ];
            
        return view('positions.index', compact('positions', 'departments', 'stats'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'job_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'grade_level' => 'nullable|integer|min:1|max:20',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $clientId = session('current_client_id');

            Position::create([
                'client_id' => $clientId,
                'title' => $request->title,
                'department_id' => $request->department_id,
                'job_code' => $request->job_code,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'grade_level' => $request->grade_level,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            ]);

            return redirect()->route('positions.index')
                ->with('success', 'Position created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create position: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'job_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'grade_level' => 'nullable|integer|min:1|max:20',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $position = Position::findOrFail($id);
            $position->update([
                'title' => $request->title,
                'department_id' => $request->department_id,
                'job_code' => $request->job_code,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'grade_level' => $request->grade_level,
                'min_salary' => $request->min_salary,
                'max_salary' => $request->max_salary,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
            ]);

            return redirect()->route('positions.index')
                ->with('success', 'Position updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update position: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted successfully!');
    }

    public function export()
    {
        $clientId = session('current_client_id');
        $positions = Position::with('department')
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->orderBy('title')
            ->get();
            
        $filename = 'positions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Title',
            'Department',
            'Job Code',
            'Description',
            'Requirements',
            'Grade Level',
            'Min Salary',
            'Max Salary',
            'Status',
            'Created At',
            'Updated At'
        ];

        $data = $positions->map(function ($position) {
            return [
                $position->title,
                $position->department->name ?? '-',
                $position->job_code,
                $position->description,
                $position->requirements,
                $position->grade_level,
                $position->min_salary,
                $position->max_salary,
                $position->is_active ? 'Active' : 'Inactive',
                $position->created_at->format('Y-m-d H:i:s'),
                $position->updated_at->format('Y-m-d H:i:s')
            ];
        })->toArray();

        $callback = function() use ($headers, $data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
