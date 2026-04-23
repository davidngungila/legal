<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupportTicket;

class HelpController
{
    /**
     * Display help page
     */
    public function index()
    {
        return view('help.index');
    }

    /**
     * Search help articles
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $request->query;
        
        // Search logic would go here
        $results = [
            'articles' => [],
            'faqs' => [],
            'videos' => [],
        ];

        return response()->json($results);
    }

    /**
     * Create support ticket
     */
    public function createTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|string|in:low,medium,high,critical',
            'category' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'client_id' => session('current_client_id'),
            'ticket_number' => $this->generateTicketNumber(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority,
            'category' => $request->category,
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'ticket_number' => $ticket->ticket_number,
            'message' => 'Support ticket created successfully!'
        ]);
    }

    /**
     * Get user tickets
     */
    public function getTickets()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($tickets);
    }

    /**
     * Get specific ticket
     */
    public function getTicket($ticketNumber)
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', auth()->id())
            ->with(['responses' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->firstOrFail();

        return response()->json($ticket);
    }

    /**
     * Add response to ticket
     */
    public function addResponse(Request $request, $ticketNumber)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $response = $ticket->responses()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_internal' => false,
        ]);

        return response()->json([
            'success' => true,
            'response' => $response,
            'message' => 'Response added successfully!'
        ]);
    }

    /**
     * Close ticket
     */
    public function closeTicket($ticketNumber)
    {
        $ticket = SupportTicket::where('ticket_number', $ticketNumber)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket closed successfully!'
        ]);
    }

    /**
     * Get help article
     */
    public function getArticle($id)
    {
        // This would fetch the article from database or help system
        $article = [
            'id' => $id,
            'title' => 'Sample Article',
            'content' => 'Article content goes here...',
            'category' => 'General',
            'views' => 0,
        ];

        return response()->json($article);
    }

    /**
     * Get help statistics
     */
    public function getStats()
    {
        $stats = [
            'total_tickets' => SupportTicket::where('user_id', auth()->id())->count(),
            'open_tickets' => SupportTicket::where('user_id', auth()->id())->where('status', 'open')->count(),
            'closed_tickets' => SupportTicket::where('user_id', auth()->id())->where('status', 'closed')->count(),
            'avg_response_time' => '2 hours', // Placeholder
        ];

        return response()->json($stats);
    }

    /**
     * Get contact information
     */
    public function getContactInfo()
    {
        $contact = [
            'email' => 'support@legalhr.com',
            'phone' => '+255 712 345 678',
            'address' => 'Dar es Salaam, Tanzania',
            'working_hours' => 'Monday - Friday: 8:00 AM - 5:00 PM',
            'emergency_contact' => '+255 754 123 456',
        ];

        return response()->json($contact);
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber()
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $random = mt_rand(1000, 9999);
        
        return $prefix . $date . $random;
    }
}
