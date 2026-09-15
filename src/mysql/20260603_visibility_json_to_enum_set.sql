-- ============================================================================
-- Migration: Visibility columns from JSON to ENUM/SET
-- Date: 2026-06-03
-- Description: Migrate visibility columns from JSON arrays to native MySQL types
--              for better performance and indexing
-- ============================================================================

-- ============================================================================
-- PHASE 1: Add Temporary Columns
-- ============================================================================

-- NEWS: Add ENUM column (single value: public or private)
ALTER TABLE news
ADD COLUMN visibility_enum ENUM('public', 'private') DEFAULT 'public'
AFTER visibility;

-- PAGES: Add SET column (multiple values possible)
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

-- ============================================================================
-- PHASE 2: Migrate Data
-- ============================================================================

-- NEWS: JSON → ENUM
-- Converts ["public"] to 'public', anything else to 'private'
UPDATE news
SET visibility_enum = CASE
    WHEN JSON_UNQUOTE(JSON_EXTRACT(visibility, '$[0]')) = 'public' THEN 'public'
    ELSE 'private'
END
WHERE visibility IS NOT NULL;

-- PAGES: JSON → SET
-- Handles all cases:
--   ["chief_editor,administrator"] → 'chief_editor,administrator'
UPDATE pages
SET visibility_set = COALESCE(
    (SELECT GROUP_CONCAT(JSON_UNQUOTE(jt.val) SEPARATOR ',')
     FROM JSON_TABLE(
         visibility,
         '$[*]' COLUMNS (val JSON PATH '$')
     ) AS jt
    ),
    'public'
)
WHERE JSON_VALID(visibility);

-- ============================================================================
-- PHASE 3: Verification (Run these queries to verify migration)
-- ============================================================================

-- Verify NEWS migration
SELECT id, visibility, visibility_enum FROM news LIMIT 20;

-- Verify PAGES migration
SELECT id, visibility, visibility_set FROM pages LIMIT 50;

-- Check for any NULL values (should be 0)
SELECT COUNT(*) FROM news WHERE visibility_enum IS NULL;
SELECT COUNT(*) FROM pages WHERE visibility_set IS NULL;


-- MIGRATION STEPS (2 deployments required):
-- ==========================================
--
-- ┌─────────────────────────────────────────────────────────────────┐
-- │ STEP 1: Deploy current PR (with dual-write)                    │
-- │         → Execute Phase 1, 2, 3 of this SQL script             │
-- │         → Doctrine entities write to BOTH columns              │
-- │         → Rollback possible if issues                          │
-- └─────────────────────────────────────────────────────────────────┘
--                               ↓
-- ┌─────────────────────────────────────────────────────────────────┐
-- │ STEP 2: Validate in production                                 │
-- │         → Monitor for errors                                   │
-- │         → Run Phase 3 verification queries                     │
-- └─────────────────────────────────────────────────────────────────┘
--                               ↓
-- ┌─────────────────────────────────────────────────────────────────┐
-- │ STEP 3: Remove dual-write code (NEW PR) + Deploy               │
-- │         → Remove 6 lines listed in Phase 4 below               │
-- │         → Deploy to production (2nd deployment)                │
-- │         → Doctrine now writes ONLY to new columns              │
-- └─────────────────────────────────────────────────────────────────┘
--                               ↓
-- ┌─────────────────────────────────────────────────────────────────┐
-- │ STEP 4: Execute Phase 5 (DROP COLUMN)                          │
-- │         → Take database backup first                           │
-- │         → Run DROP COLUMN statements below                     │
-- │         → Migration complete                                   │
-- └─────────────────────────────────────────────────────────────────┘
--
--
-- Why this order matters:
-- -----------------------
-- The Doctrine entities currently write to both columns (dual-write)
-- via setVisibility() which syncs $visibility and $visibilityJson.
--
-- After Phase 5, the old 'visibility' column no longer exists.
-- If the entity still has the ORM mapping, Doctrine will throw an error.
--

-- ============================================================================
-- PHASE 4: Update PHP Entities (see STEP 3 DETAILS above)
-- ============================================================================

-- This phase is done in code, not SQL.
-- STEP 3 DETAILS - Lines to remove:
-- ==================================
--
--    File: src/Entity/News.php
--    --------------------------
--    Line 47-48:
--        #[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
--        private array $visibilityJson = [];
--    Line 159 (in setVisibility method):
--        $this->visibilityJson = $visibility;  // Sync both columns
--
--    File: src/Entity/Page.php
--    --------------------------
--    Line 36-37:
--        #[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
--        private array $visibilityJson = [];
--    Line 142 (in setVisibility method):
--        $this->visibilityJson = $visibility;  // Sync both columns
--
--    Total: 6 lines to remove (3 per entity)

-- ============================================================================
-- PHASE 5: Drop Old Columns (Only after Phase 4 is deployed)
-- ============================================================================

-- WARNING: Only execute after the 2nd deployment (without dual-write code)!

-- Remove old JSON columns
ALTER TABLE news DROP COLUMN visibility;
ALTER TABLE pages DROP COLUMN visibility;
