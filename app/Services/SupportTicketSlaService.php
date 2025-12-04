<?php

namespace App\Services;

use App\Models\SupportTicket;
use App\Models\SupportTicketSlaLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SupportTicketSlaService
{
    /**
     * Check and log SLA status for all active tickets
     */
    public function checkAllTickets(): void
    {
        $tickets = SupportTicket::whereNotNull('sla_due_at')
            ->whereNotIn('status', ['resolved', 'closed', 'cancelled'])
            ->get();

        foreach ($tickets as $ticket) {
            $this->checkTicketSla($ticket);
        }
    }

    /**
     * Check SLA status for a specific ticket
     */
    public function checkTicketSla(SupportTicket $ticket): void
    {
        if (!$ticket->sla_due_at || in_array($ticket->status, ['resolved', 'closed', 'cancelled'])) {
            return;
        }

        $now = now();
        $dueAt = Carbon::parse($ticket->sla_due_at);
        $targetHours = $ticket->category->sla_hours ?? 24;
        
        // Calculate actual hours elapsed
        $actualHours = $ticket->created_at->diffInHours($now);

        // Check response SLA (first reply)
        $this->checkResponseSla($ticket, $targetHours, $actualHours);

        // Check resolution SLA (if not resolved)
        if (!in_array($ticket->status, ['resolved', 'closed'])) {
            $this->checkResolutionSla($ticket, $targetHours, $actualHours);
        }
    }

    /**
     * Check response SLA (first reply within target hours)
     */
    protected function checkResponseSla(SupportTicket $ticket, int $targetHours, float $actualHours): void
    {
        $firstReply = $ticket->replies()->where('is_internal', false)->first();
        
        if ($firstReply) {
            $responseHours = $ticket->created_at->diffInHours($firstReply->created_at);
            $status = $responseHours <= $targetHours ? 'met' : 'breached';
            
            // Log or update SLA log
            $slaLog = SupportTicketSlaLog::firstOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'sla_type' => 'response',
                ],
                [
                    'target_hours' => $targetHours,
                    'actual_hours' => $responseHours,
                    'status' => 'pending',
                ]
            );

            if ($slaLog->status !== $status) {
                $slaLog->actual_hours = $responseHours;
                $slaLog->status = $status;
                
                if ($status === 'breached' && !$slaLog->breached_at) {
                    $slaLog->breached_at = now();
                }
                
                $slaLog->save();
            }
        } else {
            // No reply yet - check if overdue
            if ($actualHours > $targetHours) {
                $slaLog = SupportTicketSlaLog::firstOrCreate(
                    [
                        'ticket_id' => $ticket->id,
                        'sla_type' => 'response',
                    ],
                    [
                        'target_hours' => $targetHours,
                        'actual_hours' => $actualHours,
                        'status' => 'breached',
                        'breached_at' => now(),
                    ]
                );

                if ($slaLog->status === 'pending' && $actualHours > $targetHours) {
                    $slaLog->status = 'breached';
                    $slaLog->actual_hours = $actualHours;
                    $slaLog->breached_at = now();
                    $slaLog->save();
                }
            }
        }
    }

    /**
     * Check resolution SLA
     */
    protected function checkResolutionSla(SupportTicket $ticket, int $targetHours, float $actualHours): void
    {
        $slaLog = SupportTicketSlaLog::firstOrCreate(
            [
                'ticket_id' => $ticket->id,
                'sla_type' => 'resolution',
            ],
            [
                'target_hours' => $targetHours,
                'actual_hours' => $actualHours,
                'status' => 'pending',
            ]
        );

        if ($actualHours > $targetHours && $slaLog->status === 'pending') {
            $slaLog->status = 'breached';
            $slaLog->actual_hours = $actualHours;
            $slaLog->breached_at = now();
            $slaLog->save();
        } elseif ($ticket->status === 'resolved') {
            $resolutionHours = $ticket->created_at->diffInHours($ticket->resolved_at ?? now());
            $status = $resolutionHours <= $targetHours ? 'met' : 'breached';
            
            $slaLog->actual_hours = $resolutionHours;
            $slaLog->status = $status;
            
            if ($status === 'breached' && !$slaLog->breached_at) {
                $slaLog->breached_at = now();
            }
            
            $slaLog->save();
        } else {
            // Update actual hours
            $slaLog->actual_hours = $actualHours;
            $slaLog->save();
        }
    }

    /**
     * Get overdue tickets count
     */
    public function getOverdueCount(): int
    {
        return SupportTicket::overdue()->count();
    }

    /**
     * Get tickets due soon (within 2 hours)
     */
    public function getDueSoonCount(): int
    {
        return SupportTicket::dueSoon()->count();
    }

    /**
     * Get SLA statistics
     */
    public function getSlaStatistics(): array
    {
        $totalTickets = SupportTicket::whereNotNull('sla_due_at')->count();
        $metSla = SupportTicketSlaLog::where('status', 'met')->count();
        $breachedSla = SupportTicketSlaLog::where('status', 'breached')->count();
        $pendingSla = SupportTicketSlaLog::where('status', 'pending')->count();

        return [
            'total' => $totalTickets,
            'met' => $metSla,
            'breached' => $breachedSla,
            'pending' => $pendingSla,
            'compliance_rate' => $totalTickets > 0 ? round(($metSla / $totalTickets) * 100, 2) : 0,
        ];
    }
}










