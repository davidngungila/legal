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
            
        return view('positions.index', compact('positions', 'departments'));
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
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->route('positions.index')
            ->with('success', 'Position created successfully!');
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

        $position = Position::findOrFail($id);
        $position->update($request->all());

        return redirect()->route('positions.index')
            ->with('success', 'Position updated successfully!');
    }

    public function destroy($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted successfully!');
    }
}
