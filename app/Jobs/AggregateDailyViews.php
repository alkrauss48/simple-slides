<?php

namespace App\Jobs;

use App\Models\AggregateView;
use App\Models\DailyView;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AggregateDailyViews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Process daily views from presentation records
        $presentationData = DailyView::whereNotNull('presentation_id')
            ->selectRaw('presentation_id, session_id, count(*) as total')
            ->groupBy('presentation_id', 'session_id')
            ->get()
            ->groupBy('presentation_id')
            ->map(function ($record) {
                return [
                    'presentation_id' => $record[0]->presentation_id,
                    'unique_count' => $record->count(),
                    'total_count' => $record->sum('total'),
                ];
            })->values()
            ->toArray();

        // Process daily views from adhoc presentations
        $adhocData = DailyView::whereNull('presentation_id')
            ->selectRaw('adhoc_slug, session_id, count(*) as total')
            ->groupBy('adhoc_slug', 'session_id')
            ->get()
            ->groupBy('adhoc_slug')
            ->map(function ($record) {
                return [
                    'adhoc_slug' => $record[0]->adhoc_slug,
                    'unique_count' => $record->count(),
                    'total_count' => $record->sum('total'),
                ];
            })->values()
            ->toArray();

        AggregateView::insert([
            ...$presentationData,
            ...$adhocData,
        ]);

        DailyView::truncate();
    }
}
