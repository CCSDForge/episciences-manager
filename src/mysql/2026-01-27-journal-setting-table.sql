-- ============================================================================
-- Journal Setting Table
-- ============================================================================
-- Stores JSON settings for each journal
-- Includes: API settings, theme, languages, homepage, menu, statistics
--
-- Note: Different from REVIEW_SETTING which uses key-value (EAV) structure
-- This table stores all settings as a single JSON object per journal
-- ============================================================================

CREATE TABLE JOURNAL_SETTING (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    RVID INT UNSIGNED NOT NULL UNIQUE,
    SETTING JSON NOT NULL,
    CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
    UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (RVID) REFERENCES REVIEW(RVID) ON DELETE CASCADE
);