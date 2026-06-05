<?php
namespace App\Enum;

enum NewsVisibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
