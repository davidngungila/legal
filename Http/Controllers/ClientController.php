<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request)
    {
        $clients = Client::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'clients' => $clients,
            'industries' => Client::distinct()->pluck('industry'),
            'stats' => [
                'total' => Client::count(),
                'active' => Client::where('status', 'active')->count(),
                'inactive' => Client::where('status', 'inactive')->count(),
                'suspended' => Client::where('status', 'suspended')->count(),
            ]
        ]);
    }
    
    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_title' => 'required|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'employee_count' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $client = Client::create($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'client' => $client
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create client: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified client.
     */
    public function show($id)
    {
        $client = Client::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'client' => $client
        ]);
    }
    
    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('clients')->ignore($client->id)
            ],
            'phone' => 'required|string|max:20',
            'industry' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_title' => 'required|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'employee_count' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $client->update($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully',
                'client' => $client
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified client from storage.
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        
        try {
            $client->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Perform bulk operations on clients.
     */
    public function bulkOperations(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,export',
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $action = $request->get('action');
            $clientIds = $request->get('client_ids');
            
            switch ($action) {
                case 'activate':
                    Client::whereIn('id', $clientIds)->update(['status' => 'active']);
                    $message = 'Clients activated successfully';
                    break;
                    
                case 'deactivate':
                    Client::whereIn('id', $clientIds)->update(['status' => 'inactive']);
                    $message = 'Clients deactivated successfully';
                    break;
                    
                case 'delete':
                    Client::whereIn('id', $clientIds)->delete();
                    $message = 'Clients deleted successfully';
                    break;
                    
                case 'export':
                    $clients = Client::whereIn('id', $clientIds)->get();
                    $csvData = $this->generateClientCSV($clients);
                    return response()->json([
                        'success' => true,
                        'message' => 'Export ready',
                        'csv_data' => $csvData
                    ]);
                    break;
                    
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action'
                    ], 400);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk operation failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export clients data.
     */
    public function export(Request $request)
    {
        $query = Client::query();
        
        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        
        if ($request->has('industry')) {
            $query->where('industry', $request->get('industry'));
        }
        
        $clients = $query->get();
        $csvData = $this->generateClientCSV($clients);
        
        return response()->json([
            'success' => true,
            'message' => 'Export ready',
            'csv_data' => $csvData,
            'count' => $clients->count()
        ]);
    }
    
    /**
     * Generate CSV data for clients.
     */
    private function generateClientCSV($clients)
    {
        $csv = "Name,Email,Phone,Industry,City,Country,Status,Subscription Plan,Employee Count,Contact Person,Created At\n";
        
        foreach ($clients as $client) {
            $csv .= "\"{$client->name}\",\"{$client->email}\",\"{$client->phone}\",\"{$client->industry}\",\"{$client->city}\",\"{$client->country}\",\"{$client->status}\",\"{$client->subscription_plan}\",\"{$client->employee_count}\",\"{$client->contact_person}\",\"{$client->created_at}\"\n";
        }
        
        return $csv;
    }
    
    /**
     * Get client statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Client::count(),
            'active' => Client::where('status', 'active')->count(),
            'inactive' => Client::where('status', 'inactive')->count(),
            'suspended' => Client::where('status', 'suspended')->count(),
            'by_industry' => Client::selectRaw('industry, COUNT(*) as count')
                ->groupBy('industry')
                ->orderBy('count', 'desc')
                ->get(),
            'by_subscription' => Client::selectRaw('subscription_plan, COUNT(*) as count')
                ->groupBy('subscription_plan')
                ->orderBy('count', 'desc')
                ->get(),
            'recent' => Client::orderBy('created_at', 'desc')->take(5)->get(),
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
