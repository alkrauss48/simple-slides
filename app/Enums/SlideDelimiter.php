<?php

namespace App\Enums;

enum SlideDelimiter: string
{
    use Traits\EnumToArray;

    case DOUBLE_NEW_LINE = '(\n\n|\r\n)';
    case THREE_DASHES = '---';

    public const TOOLTIP = <<<'TOOLTIP'
        This determines how your slides are broken apart. The simplest option
        is Double New line, but if you need more control (such as when writing
        formatted text or code), then the --- option may be better.
    TOOLTIP;

    /**
     * Returns the user-friendly label
     */
    public function label(): string
    {
        return match ($this) {
            self::DOUBLE_NEW_LINE => 'Double New Line',
            self::THREE_DASHES => '---',
        };
    }

    /**
     * Returns the user-friendly label
     */
    public function helperText(): string
    {
        return match ($this) {
            self::DOUBLE_NEW_LINE => '2 newlines (i.e. hitting Enter twice)',
            self::THREE_DASHES => '3 hyphens (---)',
        };
    }
}
