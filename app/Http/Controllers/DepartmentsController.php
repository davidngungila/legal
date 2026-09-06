<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentsController extends Controller
{
    public function index()
    {
        $clientId = session('current_client_id');
        
        $departments = Department::with(['parent', 'manager'])
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->orderBy('name')
            ->get();
            
        $users = User::when($clientId, fn($q) => $q->where('current_client_id', $clientId))
            ->orderBy('first_name')
            ->get();
            
        // Calculate stats
        $stats = [
            'total' => $departments->count(),
            'active' => $departments->where('is_active', true)->count(),
            'inactive' => $departments->where('is_active', false)->count(),
            'with_parent' => $departments->whereNotNull('parent_id')->count()
        ];
            
        return view('departments.index', compact('departments', 'users', 'stats'));
    }

    public function export()
    {
        $clientId = session('current_client_id');
        $departments = Department::with(['parent', 'manager'])
            ->when($clientId, fn($q) => $q->where('client_id', $clientId))
            ->orderBy('name')
            ->get();
            
        $filename = 'departments_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Name',
            'Code',
            'Parent Department',
            'Manager',
            'Description',
            'Status',
            'Created At',
            'Updated At'
        ];

        $data = $departments->map(function ($department) {
            return [
                $department->name,
                $department->code,
                $department->parent->name ?? '-',
                $department->manager ? $department->manager->first_name . ' ' . $department->manager->last_name : '-',
                $department->description,
                $department->is_active ? 'Active' : 'Inactive',
                $department->created_at->format('Y-m-d H:i:s'),
                $department->updated_at->format('Y-m-d H:i:s')
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

    public function importTemplate()
    {
        $filename = 'departments_import_template.csv';
        
        $headers = [
            'name',
            'code',
            'description',
            'parent_id',
            'manager_id',
            'is_active'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'No client selected'
            ], 400);
        }

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty'
                ], 400);
            }

            $headers = array_map('strtolower', $data[0]);
            $rows = array_slice($data, 1);
            
            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $row) {
                // Skip empty rows
                if (empty($row) || (count($row) === 1 && empty($row[0]))) {
                    continue;
                }
                
                $rowData = array_combine($headers, $row);
                
                // Check if array_combine failed (headers and row count mismatch)
                if ($rowData === false) {
                    $errors[] = "Row " . ($imported + $skipped + 1) . ": Column count mismatch";
                    continue;
                }
                
                // Check if department name is provided
                if (empty($rowData['name'])) {
                    $errors[] = "Row " . ($imported + $skipped + 1) . ": Department name is required";
                    continue;
                }
                
                // Check if department with same name exists for current client
                $existingDepartment = Department::where('client_id', $clientId)
                    ->where('name', $rowData['name'])
                    ->first();
                
                if ($existingDepartment) {
                    $skipped++;
                    $errors[] = "Row " . ($imported + $skipped) . ": Department '{$rowData['name']}' already exists";
                    continue;
                }
                
                try {
                    Department::create([
                        'client_id' => $clientId,
                        'name' => $rowData['name'],
                        'code' => $rowData['code'] ?? null,
                        'description' => $rowData['description'] ?? null,
                        'parent_id' => !empty($rowData['parent_id']) ? $rowData['parent_id'] : null,
                        'manager_id' => !empty($rowData['manager_id']) ? $rowData['manager_id'] : null,
                        'is_active' => isset($rowData['is_active']) ? filter_var($rowData['is_active'], FILTER_VALIDATE_BOOLEAN) : true,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + $skipped + 1) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => implode(', ', $errors)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $clientId = session('current_client_id');

            Department::create([
                'client_id' => $clientId,
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'manager_id' => $request->manager_id,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
            ]);

            return redirect()->route('departments.index')
                ->with('success', 'Department created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create department: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('A department cannot be its own parent.');
                    }
                }
            ],
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $department = Department::where('client_id', session('current_client_id'))->findOrFail($id);
            $department->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'manager_id' => $request->manager_id,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : false,
            ]);

            return redirect()->route('departments.index')
                ->with('success', 'Department updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update department: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $department = Department::where('client_id', session('current_client_id'))->findOrFail($id);
        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $department = Department::where('client_id', session('current_client_id'))->findOrFail($id);
            $department->update([
                'is_active' => $request->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => $department->is_active ? 'Department activated successfully!' : 'Department deactivated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update department status: ' . $e->getMessage()
            ], 500);
        }
    }
}
