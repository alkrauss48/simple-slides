<?php

namespace App\Models;

use Database\Factories\ImageUploadFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ImageUpload extends Model implements HasMedia
{
    /** @use HasFactory<ImageUploadFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * The attributes that are not mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [
        'id',
    ];

    /**
     * Get the computed markdown URL for the image
     *
     * @return Attribute<string, string>
     */
    protected function markdownUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "![{$attributes['alt_text']}]({$this->getFirstMediaUrl('image')})",
        );
    }

    /**
     * The User that this record belongs to
     *
     * @return BelongsTo<User, ImageUpload>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
