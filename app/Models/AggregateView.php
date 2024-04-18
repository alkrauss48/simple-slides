<?php

namespace App\Models;

use App\Enums\PresentationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AggregateView extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * Get the aggregate views based on the presentation filter, and the user's role.
     *
     * @return Collection<int, self>
     */
    public static function getForStat(
        ?string $presentationId,
        ?string $startDate,
        ?string $endDate,
    ): Collection {
        $query = self::forUser();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        if (auth()->user()->isAdministrator()) {
            if ($presentationId === PresentationFilter::INSTRUCTIONS->value) {
                return $query
                    ->whereNull('presentation_id')
                    ->whereNull('adhoc_slug')
                    ->get();
            }

            if ($presentationId === PresentationFilter::ADHOC->value) {
                return $query
                    ->whereNull('presentation_id')
                    ->whereNotNull('adhoc_slug')
                    ->get();
            }
        }

        return is_null($presentationId)
            ? $query->get()
            : $query
                ->where('presentation_id', intval($presentationId))
                ->get();
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
}
