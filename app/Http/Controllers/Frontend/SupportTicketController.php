<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketCategory;
use App\Models\SupportTicketReply;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of user's tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = SupportTicket::where('user_id', $user->id)
            ->with(['category', 'admin'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(15);
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();

        // Statistics
        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];

        return view('frontend.support-tickets.index', compact('tickets', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();
        return view('frontend.support-tickets.create', compact('categories'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:support_ticket_categories,id'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'category_id' => $request->category_id,
            'priority' => $request->priority ?? 'medium',
            'status' => 'open',
            'subject' => $request->subject,
            'description' => $request->description,
            'email' => $user->email,
            'source' => 'web',
        ]);

        // Calculate SLA
        if ($ticket->category && $ticket->category->sla_hours) {
            $ticket->sla_due_at = now()->addHours($ticket->category->sla_hours);
            $ticket->save();
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, null, $file, $user);
            }
        }

        // Send email notification
        try {
            Mail::to($user->email)->send(new TicketCreatedMail($ticket, false));
            
            // Notify assigned admin
            if ($ticket->admin) {
                Mail::to($ticket->admin->email)->send(new TicketAssignedMail($ticket));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket creation email: ' . $e->getMessage());
        }

        toast(__('Ticket created successfully'), 'success');

        return redirect()->route('support-tickets.show', $ticket->id);
    }

    /**
     * Display the specified ticket.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('user_id', $user->id)
            ->with(['category', 'admin', 'publicReplies.user', 'publicReplies.admin', 'attachments'])
            ->findOrFail($id);

        $replies = $ticket->publicReplies()->with(['user', 'admin', 'replyAttachments'])->get();

        return view('frontend.support-tickets.show', compact('ticket', 'replies'));
    }

    /**
     * Add reply to ticket
     */
    public function addReply(Request $request, string $id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'message' => ['required', 'string'],
        ]);

        $reply = $ticket->addReply($request->message, $user);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, $reply, $file, $user);
            }
        }

        // Update ticket status
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->changeStatus('open');
        } elseif ($ticket->status === 'waiting_customer') {
            $ticket->changeStatus('in_progress');
        }

        // Send email notification to assigned admin
        try {
            if ($ticket->admin) {
                Mail::to($ticket->admin->email)->send(new TicketReplyMail($ticket, $reply, false));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send reply email: ' . $e->getMessage());
        }

        toast(__('Reply added successfully'), 'success');

        return redirect()->back();
    }

    /**
     * Submit satisfaction rating
     */
    public function submitRating(Request $request, string $id)
    {
        $user = Auth::user();

        $ticket = SupportTicket::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->findOrFail($id);

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $ticket->update([
            'satisfaction_rating' => $request->rating,
            'satisfaction_feedback' => $request->feedback,
        ]);

        toast(__('Thank you for your feedback'), 'success');

        return redirect()->back();
    }

    /**
     * Store attachment
     */
    private function storeAttachment($ticket, $reply, $file, $uploader)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(40) . '.' . $extension;
        $filePath = $file->storeAs('support-tickets/' . $ticket->id, $fileName, 'public');

        \App\Models\SupportTicketAttachment::create([
            'ticket_id' => $ticket->id,
            'reply_id' => $reply ? $reply->id : null,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by_type' => get_class($uploader),
            'uploaded_by_id' => $uploader->id,
        ]);
    }
}
