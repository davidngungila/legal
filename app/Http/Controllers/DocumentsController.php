<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;

class DocumentsController
{
    /**
     * Display documents index
     */
    public function index()
    {
        $clientId = session('current_client_id');
        $documents = Document::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('documents.index', compact('documents'));
    }

    /**
     * View document
     */
    public function view($id)
    {
        $document = Document::findOrFail($id);
        
        // Check if user has permission to view this document
        if ($document->client_id !== session('current_client_id')) {
            abort(403);
        }

        return view('documents.view', compact('document'));
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        // Check if user has permission to download this document
        if ($document->client_id !== session('current_client_id')) {
            abort(403);
        }

        $filePath = storage_path('app/documents/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $document->document_name);
    }

    /**
     * Get documents by category
     */
    public function byCategory($category)
    {
        $clientId = session('current_client_id');
        $documents = Document::where('client_id', $clientId)
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('documents.index', compact('documents', 'category'));
    }

    /**
     * Get documents by type
     */
    public function byType($type)
    {
        $clientId = session('current_client_id');
        $documents = Document::where('client_id', $clientId)
            ->where('document_type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('documents.index', compact('documents', 'type'));
    }

    /**
     * Search documents
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $clientId = session('current_client_id');
        $query = $request->query;
        
        $documents = Document::where('client_id', $clientId)
            ->where(function ($q) use ($query) {
                $q->where('document_name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('tags', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('documents.index', compact('documents', 'query'));
    }
}
