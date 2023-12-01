<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as VerifyEmailContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements FilamentUser, VerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable;
    use MustVerifyEmail;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_admin',
        'image_uploaded_size',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdministrator(): bool
    {
        return $this->is_admin;
    }

    /**
     * Determine whether this user can access a given Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function modifyImageUploadedSize(int $size): void
    {
        $newSize = $this->image_uploaded_size + $size;

        if ($newSize < 0) {
            $newSize = 0;
        }

        $this->update(['image_uploaded_size' => $newSize]);
    }

    public function regenerateImageUploadedSize(): void
    {
        $size = Media::where(function (Builder $query) {
            $imageUploadIds = $this->imageUploads()->pluck('id');

            $query
                ->where('model_type', \App\Models\ImageUpload::class)
                ->whereIn('model_id', $imageUploadIds);
        })->orWhere(function (Builder $query) {
            $presentationIds = $this->presentations()->pluck('id');

            $query
                ->where('model_type', \App\Models\Presentation::class)
                ->whereIn('model_id', $presentationIds);
        })->sum('size');

        $this->update(['image_uploaded_size' => $size]);
    }

    /**
     * The image uploads that this user has.
     *
     * @return HasMany<ImageUpload>
     */
    public function imageUploads(): HasMany
    {
        return $this->hasMany(ImageUpload::class);
    }

    /**
     * The presentations that this user has.
     *
     * @return HasMany<Presentation>
     */
    public function presentations(): HasMany
    {
        return $this->hasMany(Presentation::class);
    }
}
