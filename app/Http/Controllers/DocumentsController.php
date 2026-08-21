<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{
    /**
     * Display the documents and policies page.
     */
    public function index()
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        // Get all documents for current client (for management)
        $documents = Document::forCurrentClient()
            ->orderBy('title')
            ->get();

        // Get active/public documents for featured section
        $publicDocuments = Document::forCurrentClient()
            ->active()
            ->public()
            ->orderBy('title')
            ->get();

        // Group documents by type
        $groupedDocuments = $documents->groupBy('document_type');

        // Get document statistics
        $stats = [
            'total' => $documents->count(),
            'contracts' => $documents->where('document_type', 'contract')->count(),
            'handbooks' => $documents->where('document_type', 'handbook')->count(),
            'policies' => $documents->where('document_type', 'policy')->count(),
            'safety' => $documents->where('document_type', 'safety')->count(),
            'draft' => $documents->where('status', 'draft')->count(),
            'archived' => $documents->where('status', 'archived')->count(),
        ];

        return view('documents.index', compact('documents', 'groupedDocuments', 'stats', 'publicDocuments'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create()
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        return response()->json([
            'success' => true,
            'document_types' => [
                'contract' => 'Contract',
                'handbook' => 'Handbook',
                'policy' => 'Policy',
                'safety' => 'Safety',
                'procedure' => 'Procedure',
                'form' => 'Form',
                'report' => 'Report',
                'other' => 'Other',
            ],
        ]);
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        // Handle tags - can come as JSON string from FormData
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:contract,handbook,policy,safety,procedure,form,report,other',
            'category' => 'nullable|string|max:100',
            'version' => 'required|string|max:20',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:effective_date',
            'status' => 'required|in:draft,active,archived',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['client_id'] = $currentClient['id'];
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();
        $data['tags'] = $tags;

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents/' . $currentClient['id'], 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        $document = Document::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully.',
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit($id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = Document::forCurrentClient()->findOrFail($id);

        return response()->json([
            'success' => true,
            'document' => $document,
            'document_types' => [
                'contract' => 'Contract',
                'handbook' => 'Handbook',
                'policy' => 'Policy',
                'safety' => 'Safety',
                'procedure' => 'Procedure',
                'form' => 'Form',
                'report' => 'Report',
                'other' => 'Other',
            ],
        ]);
    }

    /**
     * Update the specified document.
     */
    public function update(Request $request, $id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = Document::forCurrentClient()->findOrFail($id);

        // Handle tags - can come as JSON string from FormData
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:contract,handbook,policy,safety,procedure,form,report,other',
            'category' => 'nullable|string|max:100',
            'version' => 'required|string|max:20',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:effective_date',
            'status' => 'required|in:draft,active,archived',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'replace_file' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['updated_by'] = auth()->id();
        $data['tags'] = $tags;

        // Handle file upload
        if ($request->hasFile('file') && $request->boolean('replace_file')) {
            // Delete old file if exists
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('documents/' . $currentClient['id'], 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
        } elseif (!$request->hasFile('file')) {
            // Remove file-related fields from data if not replacing
            unset($data['file_path'], $data['file_size'], $data['file_type']);
        }

        $document->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully.',
            'document' => $document->fresh(),
        ]);
    }

    /**
     * Remove the specified document.
     */
    public function destroy($id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = Document::forCurrentClient()->findOrFail($id);

        // Delete file from storage if exists
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.',
        ]);
    }

    /**
     * Preview document before saving.
     */
    public function preview(Request $request)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        // Handle tags - can come as JSON string from FormData
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $tags = json_decode($tags, true) ?? [];
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|in:contract,handbook,policy,safety,procedure,form,report,other',
            'category' => 'nullable|string|max:100',
            'version' => 'required|string|max:20',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:draft,active,archived',
            'is_required' => 'boolean',
            'is_public' => 'boolean',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['client_id'] = $currentClient['id'];
        $data['tags'] = $tags;

        // Check if file was uploaded for preview
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
            
            // Store temporarily for preview
            $tempPath = $file->store('temp/preview', 'public');
            $data['file_path'] = $tempPath;
            
            // Create document object with file info
            $document = new Document($data);
            $document->setRelation('client', Client::find($currentClient['id']));
            
            // If PDF, return URL for iframe preview
            if ($file->getClientOriginalExtension() === 'pdf') {
                return response()->json([
                    'success' => true,
                    'type' => 'pdf',
                    'url' => Storage::disk('public')->url($tempPath),
                    'title' => $document->title,
                ]);
            }
            
            // For DOC/DOCX, generate metadata preview (can't embed in browser easily)
            // Fall through to metadata preview
        } else {
            $data['file_size'] = 0;
            $data['file_type'] = 'pdf';
        }

        // Create a temporary document object for preview
        $document = new Document($data);
        $document->setRelation('client', Client::find($currentClient['id']));

        // Generate preview HTML using the PDF view
        $html = view('documents.pdf', [
            'document' => $document,
            'client' => Client::find($currentClient['id']),
        ])->render();

        return response()->json([
            'success' => true,
            'type' => 'html',
            'html' => $html,
        ]);
    }

    /**
     * Preview a saved document's uploaded PDF (or metadata fallback).
     */
    public function filePreview($id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $document = Document::forCurrentClient()->findOrFail($id);

        // If a real PDF file exists on disk, serve its URL for iframe preview
        if ($document->file_path
            && Storage::disk('public')->exists($document->file_path)
            && strtolower($document->file_type) === 'pdf') {
            return response()->json([
                'success' => true,
                'type' => 'pdf',
                'url' => Storage::disk('public')->url($document->file_path),
                'title' => $document->title,
            ]);
        }

        // Fallback: metadata preview HTML
        $client = Client::find($document->client_id);
        $html = view('documents.pdf', [
            'document' => $document,
            'client' => $client,
        ])->render();

        return response()->json([
            'success' => true,
            'type' => 'html',
            'html' => $html,
        ]);
    }

    /**
     * Display a specific document.
     */
    public function view($id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $document = Document::forCurrentClient()
            ->active()
            ->findOrFail($id);

        return view('documents.view', compact('document'));
    }

    /**
     * Download a document.
     */
    public function download($id)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $document = Document::forCurrentClient()
            ->active()
            ->findOrFail($id);

        $filename = Str::slug($document->title).'-v'.$document->version.'.pdf';

        // If a real file exists on disk, serve it.
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            return response()->download(
                Storage::disk('public')->path($document->file_path),
                $filename,
                ['Content-Type' => 'application/pdf']
            );
        }

        // Otherwise generate a formatted PDF from the document metadata.
        $client = Client::find($document->client_id);
        $pdf = Pdf::loadView('documents.pdf', compact('document', 'client'));

        return $pdf->download($filename);
    }

    /**
     * Search documents.
     */
    public function search(Request $request)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return response()->json(['error' => 'Please select a client first.'], 400);
        }

        $query = $request->get('q', '');

        if (empty($query)) {
            return response()->json(['documents' => []]);
        }

        $documents = Document::forCurrentClient()
            ->active()
            ->public()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%");
            })
            ->orderBy('title')
            ->get();

        return response()->json([
            'documents' => $documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'title' => $doc->title,
                    'description' => $doc->description,
                    'document_type' => $doc->document_type,
                    'category' => $doc->category,
                    'status_badge' => $doc->status_badge,
                    'icon' => $doc->icon,
                    'formatted_file_size' => $doc->formatted_file_size,
                    'view_url' => $doc->view_url,
                    'download_url' => $doc->download_url,
                ];
            }),
        ]);
    }

    /**
     * Get documents by category.
     */
    public function byCategory($category)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $documents = Document::forCurrentClient()
            ->active()
            ->public()
            ->where('category', $category)
            ->orderBy('title')
            ->get();

        return view('documents.category', compact('documents', 'category'));
    }

    /**
     * Get documents by type.
     */
    public function byType($type)
    {
        $currentClient = session('current_client');

        if (! $currentClient) {
            return redirect()->route('clients.index')
                ->with('error', 'Please select a client first.');
        }

        $documents = Document::forCurrentClient()
            ->active()
            ->public()
            ->byType($type)
            ->orderBy('title')
            ->get();

        return view('documents.type', compact('documents', 'type'));
    }
}
