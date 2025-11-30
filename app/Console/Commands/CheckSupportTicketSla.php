<?php

namespace App\Console\Commands;

use App\Services\SupportTicketSlaService;
use Illuminate\Console\Command;

class CheckSupportTicketSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'support-tickets:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SLA status for all active support tickets';

    /**
     * Execute the console command.
     */
    public function handle(SupportTicketSlaService $slaService)
    {
        $this->info('Checking SLA status for all active tickets...');
        
        $slaService->checkAllTickets();
        
        $overdue = $slaService->getOverdueCount();
        $dueSoon = $slaService->getDueSoonCount();
        
        $this->info("✓ SLA check completed");
        $this->line("  - Overdue tickets: {$overdue}");
        $this->line("  - Tickets due soon: {$dueSoon}");
        
        return Command::SUCCESS;
    }
}
