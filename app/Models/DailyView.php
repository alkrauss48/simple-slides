<?php

namespace App\Models;

use App\Enums\PresentationFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class DailyView extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'presentation_id',
        'adhoc_slug',
    ];

    /**
     * Generate a daily view record for this adhoc presentation.
     */
    public static function createForAdhocPresentation(?string $slug = null): self
    {
        return self::create([
            'adhoc_slug' => $slug,
        ]);
    }

    /**
     * Get the daily views based on the presentation filter, and the user's role.
     *
     * @return Collection<int, self>
     */
    public static function getForStat(?string $presentationId): Collection
    {
        if (auth()->user()->isAdministrator()) {
            if ($presentationId === PresentationFilter::INSTRUCTIONS->value) {
                return self::forUser()
                    ->whereNull('presentation_id')
                    ->whereNull('adhoc_slug')
                    ->get();
            }

            if ($presentationId === PresentationFilter::ADHOC->value) {
                return self::forUser()
                    ->whereNull('presentation_id')
                    ->whereNotNull('adhoc_slug')
                    ->get();
            }
        }

        return is_null($presentationId)
            ? self::forUser()->get()
            : self::forUser()
                ->where('presentation_id', intval($presentationId))
                ->get();
    }

    /**
     * Scope a query to only include daily views for the authenticated user.
     *
     * @param  Builder<DailyView>  $query
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
     * @return BelongsTo<Presentation, DailyView>
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }
}
