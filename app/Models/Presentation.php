<?php

namespace App\Models;

use Database\Factories\PresentationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Presentation extends Model implements HasMedia
{
    /** @use HasFactory<PresentationFactory> */
    use HasFactory;

    use HasSlug;
    use InteractsWithMedia;
    use SoftDeletes;

    /**
     * The attributes that are not mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [
        'id',
        'deleted_at',
    ];

    /**
     * Determine if this presentation be viewed, based on published status and
     * the authenticated user.
     *
     * @return Attribute<bool, null>
     */
    protected function canBeViewed(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                // If the presentation is published, then anyone can see it.
                if ($this->is_published) {
                    return true;
                }

                // If the user is not logged in, then they can't see any draft
                // presentations.
                if (! auth()->check()) {
                    return false;
                }

                // Default to the normal view policy function
                return auth()->user()->can('view', $this);
            },
        );
    }

    /**
     * Determine if this presentation should track a daily view, based on
     * published status and the authenticated user.
     *
     * @return Attribute<bool, null>
     */
    protected function shouldTrackView(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                // If the presentation is not published, then a view should not
                // be tracked.
                if (! $this->is_published) {
                    return false;
                }

                // If the user is not logged in, then a view should be tracked.
                if (! auth()->check()) {
                    return true;
                }

                // If the user is an admin, then a view should not be tracked.
                if (auth()->user()->isAdministrator()) {
                    return false;
                }

                // If the user is the creator of the presentation, then a view
                // should not be tracked.
                if (auth()->id() === $this->user_id) {
                    return false;
                }

                // Otherwise, a user would be logged in, but not as an admin or
                // the creator of the presentation, and thus should track a
                // daily view. This would be a pretty rare case.
                return true;
            },
        );
    }

    /**
     * Scope a query to only include presentations for the authenticated user.
     *
     * @param  Builder<Presentation>  $query
     */
    public function scopeForUser(Builder $query): void
    {
        $query->when(!auth()->user()->isAdministrator(), function($qr){
            $qr->where('user_id',auth()->id());
        });
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->doNotGenerateSlugsOnUpdate()
            ->saveSlugsTo('slug');
    }

    /**
     * Generate a daily view record for this presentation.
     */
    public function addDailyView(): DailyView
    {
        return $this->dailyViews()->create();
    }

    /**
     * The daily views that this presentation has.
     *
     * @return HasMany<DailyView>
     */
    public function dailyViews(): HasMany
    {
        return $this->hasMany(DailyView::class);
    }

    /**
     * The User that this record belongs to
     *
     * @return BelongsTo<User, Presentation>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
