# Visibility Column Migration: JSON to ENUM/SET

## Context

The `visibility` columns in `news` and `pages` tables currently store JSON arrays (e.g., `["public"]`, `["member", "editor"]`).

This JSON storage is suboptimal in MySQL due to:
- Parsing overhead on every read
- Complex indexing
- Inefficient queries

**Goal**: Migrate to native MySQL types (ENUM/SET) while maintaining full API backward compatibility.

---

## Architecture Overview

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   PHP (Entity)  │     │   DBAL Type      │     │   MySQL         │
│                 │     │                  │     │                 │
│   ['public']    │ ──► │   → 'public'     │ ──► │   ENUM/SET      │
│   (array)       │     │   (converts)     │     │   (string)      │
│                 │     │                  │     │                 │
│   ['public']    │ ◄── │   ← 'public'     │ ◄── │                 │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

---

## Files Structure

```
src/
├── Enum/
│   ├── NewsVisibility.php      # Valid values for news (public, private)
│   └── PageVisibility.php      # Valid values for pages (public, member, editor, ...)
│
├── Doctrine/DBAL/Type/
│   ├── NewsVisibilityType.php  # Converts ENUM ↔ array
│   └── PageVisibilityType.php  # Converts SET ↔ array
│
└── Entity/
    ├── News.php                # Uses news_visibility type
    └── Page.php                # Uses page_visibility type

config/packages/
└── doctrine.yaml               # Registers custom types
```

---

## Why ENUM vs SET?

| Type | Values | Use Case |
|------|--------|----------|
| **ENUM** | Single value only | News: always `'public'` OR `'private'` |
| **SET** | Multiple values | Pages: can have multiple roles like `'member,editor,chief_editor'` |

### MySQL SET Storage (Efficient)

```
SET stores as bitmask internally:
┌─────────────────┬───────┐
│ public          │ 0000001 │
│ member          │ 0000010 │
│ editor          │ 0000100 │
│ chief_editor    │ 0001000 │
│ administrator   │ 0010000 │
├─────────────────┼─────────┤
│ member,editor → 0000110 (very efficient for queries)
└─────────────────┴─────────┘
```

---

## Enum Classes

### NewsVisibility

```php
// src/Enum/NewsVisibility.php
enum NewsVisibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```

### PageVisibility

```php
// src/Enum/PageVisibility.php
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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```

---

## DBAL Types

### How They Work

1. **`getSQLDeclaration()`**: Defines the MySQL column type
2. **`convertToPHPValue()`**: MySQL → PHP (string → array)
3. **`convertToDatabaseValue()`**: PHP → MySQL (array → string)

### NewsVisibilityType

```php
// Database: 'public' (string)
// PHP: ['public'] (array)

public function convertToPHPValue($value, $platform): array
{
    if ($value === null || $value === '') {
        return [];
    }
    return [$value];  // Wrap in array for API compatibility
}

public function convertToDatabaseValue($value, $platform): ?string
{
    return is_array($value) ? ($value[0] ?? null) : $value;
}
```

### PageVisibilityType

```php
// Database: 'member,editor' (comma-separated string)
// PHP: ['member', 'editor'] (array)

public function convertToPHPValue($value, $platform): array
{
    if ($value === null || $value === '') {
        return [];
    }
    return explode(',', $value);  // Split to array
}

public function convertToDatabaseValue($value, $platform): ?string
{
    return is_array($value) ? implode(',', $value) : $value;
}
```

---

## Doctrine Configuration

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            news_visibility: App\Doctrine\DBAL\Type\NewsVisibilityType
            page_visibility: App\Doctrine\DBAL\Type\PageVisibilityType
```

---

## Entity Mapping (Transition Period)

During the transition period, the Entity maps **both columns** to keep them synchronized.
This allows other projects to continue reading the old JSON column while this project uses the new optimized column.

### Why Synchronize Both Columns?

```
Problem without sync:
┌─────────────────────────────────────────────────────────┐
│ This project writes to → visibility_enum                │
│ Other projects read from → visibility (JSON)            │
│ Result: Other projects see stale/empty data ❌          │
└─────────────────────────────────────────────────────────┘

Solution with sync:
┌─────────────────────────────────────────────────────────┐
│ This project writes to → visibility_enum + visibility   │
│ Other projects read from → visibility (JSON)            │
│ Result: All projects see consistent data ✅             │
└─────────────────────────────────────────────────────────┘
```

### News Entity

```php
// src/Entity/News.php

// Old column (JSON) - kept for backward compatibility with other projects
#[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
private array $visibilityJson = [];

// New column (ENUM) - optimized storage
#[ORM\Column(name: 'visibility_enum', type: 'news_visibility', nullable: false)]
private array $visibility = [];

public function getVisibility(): array
{
    return $this->visibility;
}

public function setVisibility(array $visibility): static
{
    $this->visibility = $visibility;
    $this->visibilityJson = $visibility;  // Sync both columns
    return $this;
}
```

### Page Entity

```php
// src/Entity/Page.php

// Old column (JSON) - kept for backward compatibility with other projects
#[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
private array $visibilityJson = [];

// New column (SET) - optimized storage
#[ORM\Column(name: 'visibility_set', type: 'page_visibility', nullable: false)]
private array $visibility = [];

public function getVisibility(): array
{
    return $this->visibility;
}

public function setVisibility(array $visibility): static
{
    $this->visibility = $visibility;
    $this->visibilityJson = $visibility;  // Sync both columns
    return $this;
}
```

### Data Flow During Transition

```
setVisibility(['public'])
         │
         ▼
┌─────────────────────────────────────┐
│ $this->visibility = ['public']      │  → New column (ENUM/SET)
│ $this->visibilityJson = ['public']  │  → Old column (JSON)
└─────────────────────────────────────┘
         │
         ▼
Database writes both:
┌─────────────────┬──────────────────┐
│ visibility      │ visibility_enum  │
│ '["public"]'    │ 'public'         │
└─────────────────┴──────────────────┘
         │                  │
         ▼                  ▼
   Other projects      This project
   (read JSON)         (read ENUM)
```

### After All Projects Migrated

Once all projects have migrated to use the new columns, you can:
1. Remove `$visibilityJson` property from Entities
2. Remove the old `visibility` column from database (Phase 4)

---

## Backward Compatibility

The migration preserves full API compatibility:

| Layer | Before | After | Change |
|-------|--------|-------|--------|
| **API Response** | `["public"]` | `["public"]` | None |
| **Controller** | `setVisibility(['public'])` | `setVisibility(['public'])` | None |
| **Template** | `'public' in news.visibility` | `'public' in news.visibility` | None |
| **Database** | `'["public"]'` (JSON) | `'public'` (ENUM/SET) | Optimized |

---

## Migration Strategy

Since the database is shared between multiple projects, we use a progressive migration:

### Phase 1: Add Temporary Columns

```sql
-- NEWS: Add ENUM column
ALTER TABLE news
ADD COLUMN visibility_enum ENUM('public', 'private') DEFAULT 'private'
AFTER visibility;

-- PAGES: Add SET column
ALTER TABLE pages
ADD COLUMN visibility_set SET(
    'public',
    'member',
    'editor',
    'chief_editor',
    'administrator',
    'secretary',
    'webmaster',
    'guest_editor'
) DEFAULT 'public'
AFTER visibility;
```

### Phase 2: Migrate Data

```sql
-- NEWS: JSON → ENUM
UPDATE news
SET visibility_enum = CASE
    WHEN JSON_UNQUOTE(JSON_EXTRACT(visibility, '$[0]')) = 'public' THEN 'public'
    ELSE 'private'
END
WHERE visibility IS NOT NULL;

-- PAGES: JSON → SET (handles all cases)
UPDATE pages
SET visibility_set = (
    SELECT GROUP_CONCAT(JSON_UNQUOTE(jt.val) SEPARATOR ',')
    FROM JSON_TABLE(
        visibility,
        '$[*]' COLUMNS (val JSON PATH '$')
    ) AS jt
)
WHERE JSON_VALID(visibility);
```

### Phase 3: Update Each Project

Each project using the database must update its code to use the new columns.

### Phase 4: Cleanup (After All Projects Migrated)

```sql
-- Remove old JSON columns
ALTER TABLE news DROP COLUMN visibility;
ALTER TABLE pages DROP COLUMN visibility;

-- Rename new columns to original names
ALTER TABLE news CHANGE visibility_enum visibility ENUM('public', 'private') NOT NULL DEFAULT 'private';
ALTER TABLE pages CHANGE visibility_set visibility SET('public','member','editor','chief_editor','administrator','secretary','webmaster','guest_editor') NOT NULL DEFAULT 'public';
```

---

## Verification

### After Phase 1

```sql
DESCRIBE news;
DESCRIBE pages;
```

### After Phase 2

```sql
SELECT id, visibility, visibility_enum FROM news LIMIT 20;
SELECT id, visibility, visibility_set FROM pages LIMIT 20;
```

### After Phase 3

```bash
php bin/console cache:clear
# Test creating/editing a news item
# Test creating/editing a page
```

---

## Troubleshooting

### Error: Unknown column type "news_visibility"

Make sure types are registered in `config/packages/doctrine.yaml`:

```yaml
doctrine:
    dbal:
        types:
            news_visibility: App\Doctrine\DBAL\Type\NewsVisibilityType
            page_visibility: App\Doctrine\DBAL\Type\PageVisibilityType
```

Then clear cache:

```bash
php bin/console cache:clear
```

### Error: Invalid visibility value

Check that the value exists in the corresponding Enum class (`NewsVisibility` or `PageVisibility`).