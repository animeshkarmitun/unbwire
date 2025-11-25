<?php

namespace App\Jobs;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AggregateDailyAnalytics implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected Carbon $date;

    /**
     * Create a new job instance.
     */
    public function __construct(?Carbon $date = null)
    {
        $this->date = $date ?? Carbon::yesterday();
    }

    /**
     * Execute the job.
     */
    public function handle(AnalyticsService $analyticsService): void
    {
        $analyticsService->aggregateDailyStats($this->date);
    }
}
