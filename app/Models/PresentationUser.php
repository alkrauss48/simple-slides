<?php

namespace App\Models;

use App\Enums\InviteStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $presentation_id
 * @property int $user_id
 * @property string $email
 * @property string $invite_token
 * @property InviteStatus $invite_status
 * @property \Carbon\Carbon $invited_at
 * @property \Carbon\Carbon $accepted_at
 * @property Presentation $presentation
 * @property User $user
 */
class PresentationUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'presentation_id',
        'user_id',
        'email',
        'invite_token',
        'invite_status',
        'invited_at',
        'accepted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'invite_status' => InviteStatus::class,
            'invited_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($presentationUser) {
            if (empty($presentationUser->invite_token)) {
                $presentationUser->invite_token = Str::random(32);
            }
        });
    }

    /**
     * Get the user that owns the invitation.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the presentation that this invitation is for.
     *
     * @return BelongsTo<Presentation, $this>
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }

    /**
     * Check if the invitation is pending.
     *
     * @return Attribute<bool, null>
     */
    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->invite_status === InviteStatus::PENDING,
        );
    }

    /**
     * Check if the invitation is accepted.
     *
     * @return Attribute<bool, null>
     */
    protected function isAccepted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->invite_status === InviteStatus::ACCEPTED,
        );
    }

    /**
     * Check if the invitation is rejected.
     *
     * @return Attribute<bool, null>
     */
    protected function isRejected(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->invite_status === InviteStatus::REJECTED,
        );
    }

    /**
     * Accept the invitation.
     */
    public function accept(): void
    {
        $updateData = [
            'invite_status' => InviteStatus::ACCEPTED,
            'accepted_at' => now(),
        ];

        // If user_id is not set, set it to the current authenticated user
        if (! $this->user_id && auth()->check()) {
            $updateData['user_id'] = auth()->id();
        }

        $this->update($updateData);
    }

    /**
     * Reject the invitation.
     */
    public function reject(): void
    {
        $this->update([
            'invite_status' => InviteStatus::REJECTED,
        ]);
    }
}
