<?php

namespace App\Doctrine\DBAL\Type;

use App\Enum\PageVisibility;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Custom Doctrine DBAL type for Page visibility.
 *
 * Converts between:
 * - Database: SET('public','private','member','editor',...) stored as comma-separated string
 * - PHP: array format ['public'] or ['member', 'editor'] for API backward compatibility
 *
 * Unlike NewsVisibility (single value ENUM), PageVisibility supports multiple roles.
 * MySQL SET type natively stores multiple values as comma-separated string.
 */
class PageVisibilityType extends Type
{
    public const NAME = 'page_visibility';

    /**
     * Returns the SQL declaration for this column type.
     * Uses MySQL SET type which allows multiple values from a defined list.
     * Values are derived from PageVisibility enum as single source of truth.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = implode("','", PageVisibility::values());
        return "SET('{$values}')";
    }

    /**
     * Converts database value to PHP value.
     * Database stores: 'member,editor' (comma-separated string)
     * PHP receives: ['member', 'editor'] (array)
     *
     * Filters out any invalid values silently to handle potential data corruption.
     *
     * @return list<string>
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): array
    {
        // Return empty array for null/empty - no silent defaults
        if ($value === null || $value === '') {
            return [];
        }

        // MySQL SET returns comma-separated values
        $values = explode(',', $value);
        $validValues = PageVisibility::values();

        // Filter and validate - keep only valid values, trim whitespace
        $result = array_values(array_filter($values, function ($v) use ($validValues) {
            $v = trim($v);
            return $v !== '' && in_array($v, $validValues, true);
        }));

        return $result;
    }

    /**
     * Converts PHP value to database value.
     * PHP sends: ['member', 'editor'] (array)
     * Database stores: 'member,editor' (comma-separated string)
     *
     * Validates all values strictly and throws exception for invalid values.
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null || $value === []) {
            return null;
        }

        // Ensure we're working with an array
        if (!is_array($value)) {
            $value = [$value];
        }

        $validValues = PageVisibility::values();
        $sanitized = [];

        foreach ($value as $v) {
            $v = trim($v);
            if ($v === '') {
                continue;
            }

            // Strict validation - reject invalid values
            if (!in_array($v, $validValues, true)) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid page visibility value: "%s". Allowed: %s',
                        $v,
                        implode(', ', $validValues)
                    )
                );
            }
            $sanitized[] = $v;
        }

        // Return null for empty array, or comma-separated string
        return empty($sanitized) ? null : implode(',', $sanitized);
    }

    /**
     * Returns the name of this type.
     * Used in Entity column mapping: #[ORM\Column(type: 'page_visibility')]
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
