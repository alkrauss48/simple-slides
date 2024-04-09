<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * The Presentation that this record belongs to
     *
     * @return BelongsTo<Presentation, DailyView>
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }
}
