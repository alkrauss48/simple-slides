<?php

namespace App\Enums\Traits;

// Inspired From: https://stackoverflow.com/a/71680007/3482221

trait EnumToArray
{
    /**
     * The array of keys for the enum.
     *
     * @return mixed[] - These will be a string array. But laravel's toArray
     *                 doesn't specify that, so we have to use mixed.
     */
    public static function names(): array
    {
        return collect(self::cases())
            ->pluck('name')
            ->toArray();
    }

    /**
     * The array of values for the enum.
     *
     * @return mixed[]
     */
    public static function values(): array
    {
        return collect(self::cases())
            ->pluck('value')
            ->toArray();
    }

    /**
     * The array of values for the enum.
     *
     * @return array<mixed, string>
     */
    public static function array(): array
    {
        return collect(self::cases())
            ->reduce(function ($carry, $row) {
                $carry[$row->value] = $row->label();

                return $carry;
            }, []);
    }
}
