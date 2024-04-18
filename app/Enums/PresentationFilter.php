<?php

namespace App\Enums;

enum PresentationFilter: string
{
    use Traits\EnumToArray;

    case INSTRUCTIONS = 'instructions';
    case ADHOC = 'adhoc';

    /**
     * Returns the user-friendly label
     */
    public function label(): string
    {
        return match ($this) {
            self::INSTRUCTIONS => 'Instructions',
            self::ADHOC => 'Adhoc',
        };
    }
}
