<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HelpController extends Controller
{
    /**
     * Display the help and support page.
     */
    public function index()
    {
        $user = Auth::user();
        $currentClient = session('current_client');
        $currentUser = $user; // Add currentUser for layout
        
        // Get user's support tickets
        $tickets = SupportTicket::forCurrentClient()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get ticket statistics
        $ticketStats = [
            'total' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->count(),
            'open' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];

        // Get help articles (this would typically come from a database)
        $helpArticles = $this->getHelpArticles();

        return view('help.index', compact('user', 'tickets', 'ticketStats', 'helpArticles', 'currentClient', 'currentUser'));
    }

    /**
     * Search help articles.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a search term'
            ]);
        }

        $articles = $this->searchHelpArticles($query);

        return response()->json([
            'success' => true,
            'query' => $query,
            'articles' => $articles,
            'count' => count($articles)
        ]);
    }

    /**
     * Create a new support ticket.
     */
    public function createTicket(Request $request)
    {
        $user = Auth::user();
        $clientId = session('current_client_id');

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(SupportTicket::CATEGORIES))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_keys(SupportTicket::PRIORITIES))],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:2048'],
        ]);

        $ticketData = [
            'user_id' => $user->id,
            'client_id' => $clientId,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
        ];

        $ticket = SupportTicket::createTicket($ticketData);

        // Handle attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                // Store attachment logic here
                // $path = $attachment->store('ticket-attachments', 'public');
                // TicketAttachment::create(['ticket_id' => $ticket->id, 'file_path' => $path]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Support ticket created successfully!',
            'ticket' => $ticket->load('user', 'client')
        ]);
    }

    /**
     * Get user's support tickets.
     */
    public function getTickets(Request $request)
    {
        $user = Auth::user();
        
        $tickets = SupportTicket::forCurrentClient()
            ->where('user_id', $user->id)
            ->with(['responses' => function($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->get('status')) {
            $tickets->byStatus($request->get('status'));
        }
        
        if ($request->get('priority')) {
            $tickets->byPriority($request->get('priority'));
        }
        
        if ($request->get('category')) {
            $tickets->byCategory($request->get('category'));
        }

        $tickets = $tickets->paginate(10);

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    /**
     * Get a specific support ticket.
     */
    public function getTicket($ticketNumber)
    {
        $user = Auth::user();
        
        $ticket = SupportTicket::forCurrentClient()
            ->where('user_id', $user->id)
            ->where('ticket_number', $ticketNumber)
            ->with(['user', 'client', 'assignedTo', 'responses' => function($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }

    /**
     * Add a response to a support ticket.
     */
    public function addResponse(Request $request, $ticketNumber)
    {
        $user = Auth::user();
        
        $ticket = SupportTicket::forCurrentClient()
            ->where('user_id', $user->id)
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'is_internal' => ['boolean'],
        ]);

        $response = SupportTicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        // Update ticket status if needed
        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Response added successfully!',
            'response' => $response->load('user')
        ]);
    }

    /**
     * Close a support ticket.
     */
    public function closeTicket($ticketNumber)
    {
        $user = Auth::user();
        
        $ticket = SupportTicket::forCurrentClient()
            ->where('user_id', $user->id)
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully!'
        ]);
    }

    /**
     * Get help articles (mock data - would come from database).
     */
    private function getHelpArticles()
    {
        return [
            [
                'id' => 1,
                'title' => 'Getting Started with LegalHR',
                'category' => 'Getting Started',
                'description' => 'Learn the basics of using LegalHR Tanzania',
                'content' => 'This comprehensive guide covers...',
                'views' => 1234,
                'last_updated' => now()->subDays(5),
                'featured' => true
            ],
            [
                'id' => 2,
                'title' => 'Employee Management Guide',
                'category' => 'Employee Management',
                'description' => 'Complete guide to managing employees',
                'content' => 'Learn how to add, edit, and manage...',
                'views' => 856,
                'last_updated' => now()->subDays(3),
                'featured' => false
            ],
            [
                'id' => 3,
                'title' => 'Payroll Processing',
                'category' => 'Payroll',
                'description' => 'How to process payroll efficiently',
                'content' => 'Step-by-step guide to payroll processing...',
                'views' => 2341,
                'last_updated' => now()->subDays(7),
                'featured' => true
            ],
            [
                'id' => 4,
                'title' => 'Compliance Requirements',
                'category' => 'Compliance',
                'description' => 'Understanding Tanzania labor laws',
                'content' => 'Stay compliant with local regulations...',
                'views' => 1567,
                'last_updated' => now()->subDays(10),
                'featured' => false
            ],
        ];
    }

    /**
     * Search help articles (mock implementation).
     */
    private function searchHelpArticles($query)
    {
        $articles = $this->getHelpArticles();
        
        $results = array_filter($articles, function($article) use ($query) {
            $searchText = strtolower($article['title'] . ' ' . $article['description'] . ' ' . $article['content']);
            return strpos($searchText, strtolower($query)) !== false;
        });

        return array_values($results);
    }

    /**
     * Get help article by ID.
     */
    public function getArticle($id)
    {
        $articles = $this->getHelpArticles();
        $article = collect($articles)->firstWhere('id', $id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found'
            ], 404);
        }

        // Increment view count
        $article['views']++;

        return response()->json([
            'success' => true,
            'article' => $article
        ]);
    }

    /**
     * Get support statistics.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_tickets' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->count(),
            'open_tickets' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->where('status', 'open')->count(),
            'resolved_tickets' => SupportTicket::forCurrentClient()->where('user_id', $user->id)->where('status', 'resolved')->count(),
            'average_response_time' => '2 hours', // This would be calculated from actual data
            'satisfaction_rate' => '95%', // This would be calculated from actual data
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get support contact information.
     */
    public function getContactInfo()
    {
        $contactInfo = [
            'phone' => '+255 22 123 4567',
            'email' => 'support@legalhr.co.tz',
            'whatsapp' => '+255 754 123 456',
            'office_hours' => 'Monday - Friday: 8:00 AM - 6:00 PM EAT',
            'emergency_contact' => '+255 754 999 999',
            'live_chat_available' => true,
            'response_time' => 'Within 2 hours during business hours'
        ];

        return response()->json([
            'success' => true,
            'contact_info' => $contactInfo
        ]);
    }
}
