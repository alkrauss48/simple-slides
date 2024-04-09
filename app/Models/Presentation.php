<?php

namespace App\Models;

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
