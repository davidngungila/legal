<?php

namespace App\Http\Controllers;

use App\Models\ClientRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ClientRegistrationConfirmation;

class ClientRegistrationController extends Controller
{
    /**
     * Display client registration form.
     */
    public function create()
    {
        return view('hris.client-registration.create');
    }

    /**
     * Store a newly registered client.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employer_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'tin_number' => 'required|string|max:50|unique:client_registrations',
            'osha_registration' => 'required|string|max:50|unique:client_registrations',
            'nhif_registration' => 'required|string|max:50|unique:client_registrations',
            'wcf_registration' => 'required|string|max:50|unique:client_registrations',
            'vat_registration_number' => 'required|string|max:50|unique:client_registrations',
            'nssf_registration' => 'required|string|max:50|unique:client_registrations',
            'phone' => 'required|string|max:20',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:client_registrations',
            'region' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ], [
            'employer_name.required' => 'Employer name is required',
            'tin_number.unique' => 'Client created already exists - This TIN number is already registered',
            'email.unique' => 'Client created already exists - This email is already registered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generate unique employer number
            $employerNumber = ClientRegistration::generateEmployerNumber();

            $client = ClientRegistration::create(array_merge($request->all(), [
                'employer_number' => $employerNumber,
                'is_active' => true,
            ]));

            // Handle file uploads
            $this->handleFileUploads($request, $client);

            // Send confirmation email
            try {
                Mail::to($client->contact_email)->send(new ClientRegistrationConfirmation($client));
            } catch (\Exception $e) {
                \Log::error('Failed to send client registration email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Client registered successfully',
                'client' => $client,
                'employer_number' => $employerNumber
            ]);

        } catch (\Exception $e) {
            \Log::error('Client registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the list of registered clients.
     */
    public function index()
    {
        $clients = ClientRegistration::active()->paginate(10);
        return view('hris.client-registration.index', compact('clients'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(ClientRegistration $client)
    {
        return view('hris.client-registration.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, ClientRegistration $client)
    {
        $validator = Validator::make($request->all(), [
            'employer_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255|unique:client_registrations,contact_email,' . $client->id,
            'tin_number' => 'required|string|max:50|unique:client_registrations,tin_number,' . $client->id,
            'osha_registration' => 'required|string|max:50|unique:client_registrations,osha_registration,' . $client->id,
            'nhif_registration' => 'required|string|max:50|unique:client_registrations,nhif_registration,' . $client->id,
            'wcf_registration' => 'required|string|max:50|unique:client_registrations,wcf_registration,' . $client->id,
            'vat_registration_number' => 'required|string|max:50|unique:client_registrations,vat_registration_number,' . $client->id,
            'nssf_registration' => 'required|string|max:50|unique:client_registrations,nssf_registration,' . $client->id,
            'phone' => 'required|string|max:20',
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:client_registrations,email,' . $client->id,
            'region' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'location' => 'required|string|max:255',
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

            // Handle file uploads
            $this->handleFileUploads($request, $client);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            \Log::error('Client update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate the specified client.
     */
    public function deactivate(ClientRegistration $client)
    {
        try {
            $client->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Client deactivated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Client deactivation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate the specified client.
     */
    public function activate(ClientRegistration $client)
    {
        try {
            $client->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Client activated successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Client activation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sorry! Operation failed - ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle file uploads for certificates.
     */
    private function handleFileUploads(Request $request, ClientRegistration $client)
    {
        $certificates = [
            'tin_certificate' => 'tin_certificate_path',
            'osha_certificate' => 'osha_certificate_path',
            'nhif_certificate' => 'nhif_certificate_path',
            'wcf_certificate' => 'wcf_certificate_path',
            'vat_certificate' => 'vat_certificate_path',
            'nssf_certificate' => 'nssf_certificate_path',
        ];

        foreach ($certificates as $inputName => $fieldName) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $fileName = time() . '_' . $inputName . '_' . $client->id . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('certificates', $fileName, 'public');
                $client->update([$fieldName => $filePath]);
            }
        }
    }
}
