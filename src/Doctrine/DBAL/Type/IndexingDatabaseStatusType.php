<?php

namespace App\Doctrine\DBAL\Type;

use App\Enum\IndexingDatabaseStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class IndexingDatabaseStatusType extends Type
{
    public const NAME = 'indexing_database_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode("','", IndexingDatabaseStatus::values());
        return "ENUM('{$values}')";
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?IndexingDatabaseStatus
    {
        if ($value === null || $value === '') {
            return null;
        }
        return IndexingDatabaseStatus::from($value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        return $value instanceof IndexingDatabaseStatus ? $value->value : $value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
