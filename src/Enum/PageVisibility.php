<?php
namespace App\Enum;

enum PageVisibility: string
{
    case PUBLIC = 'public';
    case MEMBER = 'member';
    case EDITOR = 'editor';
    case CHIEF_EDITOR = 'chief_editor';
    case ADMINISTRATOR = 'administrator';
    case SECRETARY = 'secretary';
    case WEBMASTER = 'webmaster';
    case GUEST_EDITOR = 'guest_editor';

    /** @return list<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
