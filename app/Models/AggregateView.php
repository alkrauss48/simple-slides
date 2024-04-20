<?php

namespace App\Models;

use App\Enums\PresentationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AggregateView extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * Scope a query to return stats for the dashboard
     *
     * @param  Builder<AggregateView>  $query
     */
    public function scopeStats(
        Builder $query,
        ?string $presentationId = null,
        ?string $startDate = null,
        ?string $endDate = null,
    ): void {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (auth()->user()->isAdministrator()) {
            if ($presentationId === PresentationFilter::INSTRUCTIONS->value) {
                $query
                    ->whereNull('presentation_id')
                    ->whereNull('adhoc_slug');

                return;
            }

            if ($presentationId === PresentationFilter::ADHOC->value) {
                $query
                    ->whereNull('presentation_id')
                    ->whereNotNull('adhoc_slug')
                    ->get();

                return;
            }
        }

        if (is_null($presentationId)) {
            return;
        }

        $query->where('presentation_id', intval($presentationId));
    }

    /**
     * Scope a query to only include aggregate views for the authenticated user.
     *
     * @param  Builder<AggregateView>  $query
     */
    public function scopeForUser(Builder $query): void
    {
        if (auth()->user()->isAdministrator()) {
            return;
        }

        $presentationIds = auth()->user()->presentations()->pluck('id');

        $query->whereIn('presentation_id', $presentationIds);
    }

    /**
     * The Presentation that this record belongs to
     *
     * @return BelongsTo<Presentation, AggregateView>
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }
}
