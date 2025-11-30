<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketCategory;
use App\Models\SupportTicketReply;
use App\Models\User;
use App\Models\Admin;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketReplyMail;
use App\Mail\TicketStatusChangedMail;
use App\Mail\TicketAssignedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:support tickets index,admin'])->only(['index']);
        $this->middleware(['permission:support tickets view,admin'])->only(['show']);
        $this->middleware(['permission:support tickets create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:support tickets update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:support tickets assign,admin'])->only(['assign']);
        $this->middleware(['permission:support tickets delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of support tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'admin', 'category'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by assigned agent
        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('admin_id');
            } else {
                $query->where('admin_id', $request->assigned_to);
            }
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('support_ticket_tags.id', $request->tag);
            });
        }

        // Filter by overdue
        if ($request->filled('overdue')) {
            $query->overdue();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $tickets = $query->paginate(20);
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();
        $admins = Admin::where('status', 1)->get();

        // Statistics
        $stats = [
            'total_open' => SupportTicket::where('status', 'open')->count(),
            'total_in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'overdue' => SupportTicket::overdue()->count(),
            'due_soon' => SupportTicket::dueSoon()->count(),
            'unassigned' => SupportTicket::unassigned()->where('status', '!=', 'closed')->count(),
            'my_tickets' => SupportTicket::assignedTo(Auth::guard('admin')->id())
                ->whereNotIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('admin.support-tickets.index', compact('tickets', 'categories', 'admins', 'stats'));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();
        $users = User::select('id', 'name', 'email')->get();
        $admins = Admin::where('status', 1)->select('id', 'name')->get();

        return view('admin.support-tickets.create', compact('categories', 'users', 'admins'));
    }

    /**
     * Store a newly created ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:support_ticket_categories,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'user_id' => ['nullable', 'exists:users,id'],
            'admin_id' => ['nullable', 'exists:admins,id'],
            'email' => ['required_if:user_id,null', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => $request->user_id,
            'admin_id' => $request->admin_id,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'status' => 'open',
            'subject' => $request->subject,
            'description' => $request->description,
            'email' => $request->email ?? ($request->user_id ? User::find($request->user_id)->email : null),
            'phone' => $request->phone,
            'source' => 'web',
        ]);

        // Calculate SLA
        if ($ticket->category && $ticket->category->sla_hours) {
            $ticket->sla_due_at = now()->addHours($ticket->category->sla_hours);
            $ticket->save();
        }

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $admin = Auth::guard('admin')->user();
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, null, $file, $admin);
            }
        }

        // Send email notification
        try {
            if ($ticket->user) {
                Mail::to($ticket->user->email)->send(new TicketCreatedMail($ticket, false));
            } elseif ($ticket->email) {
                Mail::to($ticket->email)->send(new TicketCreatedMail($ticket, false));
            }
            
            // Notify assigned admin
            if ($ticket->admin) {
                Mail::to($ticket->admin->email)->send(new TicketAssignedMail($ticket));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket creation email: ' . $e->getMessage());
        }

        toast(__('Ticket created successfully'), 'success');

        return redirect()->route('admin.support-tickets.show', $ticket->id);
    }

    /**
     * Display the specified ticket.
     */
    public function show(string $id)
    {
        $ticket = SupportTicket::with(['user', 'admin', 'category', 'replies.user', 'replies.admin', 'replies.replyAttachments', 'attachments', 'activities.admin', 'activities.user', 'tags'])
            ->findOrFail($id);

        $replies = $ticket->replies()->with(['user', 'admin', 'replyAttachments'])->get();
        $admins = Admin::where('status', 1)->select('id', 'name')->get();
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();

        return view('admin.support-tickets.show', compact('ticket', 'replies', 'admins', 'categories'));
    }

    /**
     * Show the form for editing the specified ticket.
     */
    public function edit(string $id)
    {
        $ticket = SupportTicket::with(['category', 'tags'])->findOrFail($id);
        $categories = SupportTicketCategory::active()->orderBy('sort_order')->get();
        $admins = Admin::where('status', 1)->select('id', 'name')->get();
        $tags = \App\Models\SupportTicketTag::all();

        return view('admin.support-tickets.edit', compact('ticket', 'categories', 'admins', 'tags'));
    }

    /**
     * Update the specified ticket.
     */
    public function update(Request $request, string $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:support_ticket_categories,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['required', 'in:open,in_progress,waiting_customer,resolved,closed,cancelled'],
            'admin_id' => ['nullable', 'exists:admins,id'],
        ]);

        // Track changes
        $oldStatus = $ticket->status;
        if ($ticket->status !== $request->status) {
            $ticket->changeStatus($request->status);
            
            // Send status change email
            try {
                if ($ticket->user) {
                    Mail::to($ticket->user->email)->send(new TicketStatusChangedMail($ticket, $oldStatus, $request->status));
                } elseif ($ticket->email) {
                    Mail::to($ticket->email)->send(new TicketStatusChangedMail($ticket, $oldStatus, $request->status));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send status change email: ' . $e->getMessage());
            }
        }

        if ($ticket->priority !== $request->priority) {
            $ticket->changePriority($request->priority);
        }

        if ($ticket->admin_id != $request->admin_id) {
            $oldAdminId = $ticket->admin_id;
            $ticket->assignTo($request->admin_id);
            
            // Send assignment email
            try {
                if ($ticket->admin) {
                    Mail::to($ticket->admin->email)->send(new TicketAssignedMail($ticket));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send assignment email: ' . $e->getMessage());
            }
        }

        $ticket->update([
            'subject' => $request->subject,
            'category_id' => $request->category_id,
        ]);

        // Sync tags
        if ($request->has('tags')) {
            $ticket->tags()->sync($request->tags);
        }

        toast(__('Ticket updated successfully'), 'success');

        return redirect()->route('admin.support-tickets.show', $ticket->id);
    }

    /**
     * Remove the specified ticket.
     */
    public function destroy(string $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        toast(__('Ticket deleted successfully'), 'success');

        return redirect()->route('admin.support-tickets.index');
    }

    /**
     * Assign ticket to admin
     */
    public function assign(Request $request, string $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'admin_id' => ['required', 'exists:admins,id'],
        ]);

        $ticket->assignTo($request->admin_id);

        // Send assignment email
        try {
            if ($ticket->admin) {
                Mail::to($ticket->admin->email)->send(new TicketAssignedMail($ticket));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send assignment email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('Ticket assigned successfully'),
        ]);
    }

    /**
     * Add reply to ticket
     */
    public function addReply(Request $request, string $id)
    {
        $ticket = SupportTicket::findOrFail($id);

        $request->validate([
            'message' => ['required', 'string'],
            'is_internal' => ['sometimes', 'boolean'],
        ]);

        $admin = Auth::guard('admin')->user();

        $reply = $ticket->addReply(
            $request->message,
            null,
            $admin,
            $request->boolean('is_internal', false)
        );

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $this->storeAttachment($ticket, $reply, $file, $admin);
            }
        }

        // Update ticket status if it was waiting for customer
        if ($ticket->status === 'waiting_customer' && !$request->boolean('is_internal')) {
            $ticket->changeStatus('in_progress');
        }

        // Send email notification (only for non-internal replies)
        if (!$request->boolean('is_internal')) {
            try {
                if ($ticket->user) {
                    Mail::to($ticket->user->email)->send(new TicketReplyMail($ticket, $reply, true));
                } elseif ($ticket->email) {
                    Mail::to($ticket->email)->send(new TicketReplyMail($ticket, $reply, true));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send reply email: ' . $e->getMessage());
            }
        }

        toast(__('Reply added successfully'), 'success');

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
