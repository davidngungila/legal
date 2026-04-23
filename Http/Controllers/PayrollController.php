<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollController extends Controller
{
    /**
     * Display payroll management page.
     */
    public function index()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payrolls = Payroll::where('client_id', $clientId)
            ->with(['employee', 'client'])
            ->orderBy('pay_date', 'desc')
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show CSV upload form.
     */
    public function showUploadForm()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $client = Client::find($clientId);
        return view('payroll.upload', compact('client'));
    }

    /**
     * Process CSV upload and save to database.
     */
    public function uploadCsv(Request $request)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            'payroll_period' => 'required|string|max:50',
            'pay_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            $payrollPeriod = $request->input('payroll_period');
            $payDate = $request->input('pay_date');

            // Read CSV file
            $csvData = $this->readCsvFile($file);
            
            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or invalid.'
                ], 400);
            }

            // Process and save payroll data
            $result = $this->processPayrollData($csvData, $clientId, $payrollPeriod, $payDate);

            return response()->json([
                'success' => true,
                'message' => "Successfully processed {$result['processed']} payroll records. {$result['skipped']} records were skipped.",
                'processed' => $result['processed'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read CSV file and return data array.
     */
    private function readCsvFile($file)
    {
        $csvData = [];
        $header = [];
        $rowNumber = 0;

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // First row is header
                if (empty($header)) {
                    $header = array_map('strtolower', array_map('trim', $row));
                    continue;
                }

                // Combine header with row data
                $rowData = array_combine($header, $row);
                $rowData['_row_number'] = $rowNumber;
                $csvData[] = $rowData;
            }
            fclose($handle);
        }

        return $csvData;
    }

    /**
     * Process payroll data and save to database.
     */
    private function processPayrollData($csvData, $clientId, $payrollPeriod, $payDate)
    {
        $processed = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($csvData as $row) {
                try {
                    // Validate required fields
                    if (empty($row['employee_id']) && empty($row['email'])) {
                        $errors[] = "Row {$row['_row_number']}: Employee ID or Email is required";
                        $skipped++;
                        continue;
                    }

                    // Find employee
                    $employee = $this->findEmployee($row, $clientId);
                    
                    if (!$employee) {
                        $errors[] = "Row {$row['_row_number']}: Employee not found";
                        $skipped++;
                        continue;
                    }

                    // Check if payroll already exists for this employee and period
                    $existingPayroll = Payroll::where('client_id', $clientId)
                        ->where('employee_id', $employee->id)
                        ->where('payroll_period', $payrollPeriod)
                        ->first();

                    if ($existingPayroll) {
                        $errors[] = "Row {$row['_row_number']}: Payroll already exists for this employee in period {$payrollPeriod}";
                        $skipped++;
                        continue;
                    }

                    // Prepare payroll data
                    $payrollData = $this->preparePayrollData($row, $employee, $clientId, $payrollPeriod, $payDate);

                    // Create payroll record
                    $payroll = Payroll::create($payrollData);
                    
                    // Calculate automatic values
                    $payroll->calculateGrossPay();
                    $payroll->calculateTotalDeductions();
                    $payroll->calculateNetPay();
                    $payroll->save();

                    $processed++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$row['_row_number']}: " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception('Transaction failed: ' . $e->getMessage());
        }

        return [
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors
        ];
    }

    /**
     * Find employee by ID or email.
     */
    private function findEmployee($row, $clientId)
    {
        // Try to find by employee_id first
        if (!empty($row['employee_id'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where(function($query) use ($row) {
                    $query->where('employee_id', $row['employee_id'])
                          ->orWhere('id', $row['employee_id']);
                })
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        // Try to find by email
        if (!empty($row['email'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where('email', $row['email'])
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        // Try to find by name combination
        if (!empty($row['first_name']) && !empty($row['last_name'])) {
            $employee = Employee::where('client_id', $clientId)
                ->where('first_name', 'LIKE', trim($row['first_name']))
                ->where('last_name', 'LIKE', trim($row['last_name']))
                ->first();
            
            if ($employee) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Prepare payroll data from CSV row.
     */
    private function preparePayrollData($row, $employee, $clientId, $payrollPeriod, $payDate)
    {
        return [
            'client_id' => $clientId,
            'employee_id' => $employee->id,
            'payroll_period' => $payrollPeriod,
            'pay_date' => $payDate,
            'basic_salary' => $this->parseDecimal($row['basic_salary'] ?? 0),
            'overtime_hours' => $this->parseDecimal($row['overtime_hours'] ?? 0),
            'overtime_rate' => $this->parseDecimal($row['overtime_rate'] ?? 0),
            'overtime_pay' => $this->parseDecimal($row['overtime_pay'] ?? 0),
            'allowances' => $this->parseDecimal($row['allowances'] ?? 0),
            'bonuses' => $this->parseDecimal($row['bonuses'] ?? 0),
            'tax_deductions' => $this->parseDecimal($row['tax_deductions'] ?? 0),
            'social_security' => $this->parseDecimal($row['social_security'] ?? 0),
            'pension' => $this->parseDecimal($row['pension'] ?? 0),
            'other_deductions' => $this->parseDecimal($row['other_deductions'] ?? 0),
            'status' => 'draft', // Default status
            'notes' => $row['notes'] ?? null,
        ];
    }

    /**
     * Parse decimal value from CSV.
     */
    private function parseDecimal($value)
    {
        // Remove currency symbols, commas, and spaces
        $cleaned = preg_replace('/[^\d.-]/', '', $value);
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $filename = "payroll_template_" . date('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'employee_id',
                'email',
                'first_name',
                'last_name',
                'basic_salary',
                'overtime_hours',
                'overtime_rate',
                'overtime_pay',
                'allowances',
                'bonuses',
                'tax_deductions',
                'social_security',
                'pension',
                'other_deductions',
                'notes'
            ]);
            
            // Sample row
            fputcsv($file, [
                'EMP001',
                'john.doe@company.com',
                'John',
                'Doe',
                '500000',
                '10',
                '5000',
                '50000',
                '20000',
                '10000',
                '50000',
                '10000',
                '5000',
                '5000',
                'Sample notes'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show payroll details.
     */
    public function show($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payroll = Payroll::where('client_id', $clientId)
            ->with(['employee', 'client'])
            ->findOrFail($id);

        return view('payroll.show', compact('payroll'));
    }

    /**
     * Update payroll status.
     */
    public function updateStatus(Request $request, $id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a client first.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,processed,paid,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status.',
                'errors' => $validator->errors()
            ], 422);
        }

        $payroll = Payroll::where('client_id', $clientId)->findOrFail($id);
        $payroll->status = $request->input('status');
        $payroll->save();

        return response()->json([
            'success' => true,
            'message' => 'Payroll status updated successfully.',
            'payroll' => $payroll
        ]);
    }

    /**
     * Delete payroll record.
     */
    public function destroy($id)
    {
        $clientId = session('current_client_id');
        if (!$clientId) {
            return redirect()->route('dashboard')->with('error', 'Please select a client first.');
        }

        $payroll = Payroll::where('client_id', $clientId)->findOrFail($id);
        $payroll->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll record deleted successfully.');
    }
}
