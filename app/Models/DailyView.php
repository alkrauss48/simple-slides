<?php

namespace App\Models;

use App\Enums\PresentationFilter;
use Database\Factories\DailyViewFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyView extends Model
{
    /** @use HasFactory<DailyViewFactory> */
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
     * Scope a query to return stats for the dashboard
     *
     * @param  Builder<DailyView>  $query
     */
    public function scopeStats(Builder $query, ?string $presentationId = null): void
    {
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

        if (is_null($presentationId)) {
            return;
        }

        $query->where('presentation_id', intval($presentationId));
    }

    /**
     * Scope a query to only include daily views for the authenticated user.
     *
     * @param  Builder<DailyView>  $query
     */
    public function scopeForUser(Builder $query): void
    {
        $query->when(! auth()->user()->isAdministrator(), function ($qr) {
            $qr->whereHas('presentation', function ($qrPresn) {
                // Include views for presentations owned by the user
                $qrPresn->where('user_id', auth()->id())
                    // Include views for presentations shared with the user (with accepted invitations)
                    ->orWhereHas('presentationUsers', function ($query) {
                        $query->where('user_id', auth()->id())
                            ->where('invite_status', \App\Enums\InviteStatus::ACCEPTED);
                    });
            });
        });
    }

    /**
     * The Presentation that this record belongs to
     *
     * @return BelongsTo<Presentation, $this>
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }
}
