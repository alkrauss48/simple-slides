<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InviteStatus: string implements HasLabel
{
    use Traits\EnumToArray;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return $this->label();
    }

    /**
     * Returns the user-friendly label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::ACCEPTED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
