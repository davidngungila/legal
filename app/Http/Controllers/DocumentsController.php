<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        // Get documents for current client
        $documents = Document::forCurrentClient()
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
        ];

        return view('documents.index', compact('documents', 'groupedDocuments', 'stats'));
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
