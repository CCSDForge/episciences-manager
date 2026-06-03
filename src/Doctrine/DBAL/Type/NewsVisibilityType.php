<?php

namespace App\Doctrine\DBAL\Type;

use App\Enum\NewsVisibility;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Custom Doctrine DBAL type for News visibility.
 *
 * Converts between:
 * - Database: ENUM('public', 'private') stored as string
 * - PHP: array format ['public'] or ['private'] for API backward compatibility
 *
 * This allows the Entity to work with arrays while the database stores native ENUM.
 */
class NewsVisibilityType extends Type
{
    public const NAME = 'news_visibility';

    /**
     * Returns the SQL declaration for this column type.
     * Uses the NewsVisibility enum as the single source of truth for valid values.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode("','", NewsVisibility::values());
        return "ENUM('{$values}')";
    }

    /**
     * Converts database value to PHP value.
     * Database stores: 'public' (string)
     * PHP receives: ['public'] (array)
     *
     * @return list<string>
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): array
    {
        // Return empty array for null/empty - no silent defaults
        if ($value === null || $value === '') {
            return [];
        }

        // Strict validation against allowed enum values
        if (!in_array($value, NewsVisibility::values(), true)) {
            throw new \UnexpectedValueException(
                sprintf('Invalid news visibility value from DB: "%s"', $value)
            );
        }

        // Wrap in array for backward compatibility with existing code
        return [$value];
    }

    /**
     * Converts PHP value to database value.
     * PHP sends: ['public'] (array)
     * Database stores: 'public' (string)
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === []) {
            return null;
        }

        // Extract first value from array (or use directly if string)
        $firstValue = is_array($value) ? ($value[0] ?? null) : $value;

        if ($firstValue === null) {
            return null;
        }

        // Strict validation against allowed enum values
        if (!in_array($firstValue, NewsVisibility::values(), true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid news visibility value: "%s". Allowed: %s',
                    $firstValue,
                    implode(', ', NewsVisibility::values())
                )
            );
        }

        return $firstValue;
    }

    /**
     * Returns the name of this type.
     * Used in Entity column mapping: #[ORM\Column(type: 'news_visibility')]
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Tells Doctrine to add a SQL comment hint for this type.
     * This helps Doctrine recognize the type during schema comparisons.
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
