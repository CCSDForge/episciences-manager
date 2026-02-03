-- ============================================================================
-- Journal Configuration Table
-- ============================================================================
-- Stores JSON configuration for each journal (review)
-- Includes: API settings, theme, languages, homepage, menu, statistics
--
-- Usage: Each journal has one configuration record identified by RVID
-- The CONFIGURATION column contains all settings as a JSON object
-- ============================================================================

CREATE TABLE JOURNAL_CONFIGURATION (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    RVID INT UNSIGNED NOT NULL UNIQUE,
    CONFIGURATION JSON NOT NULL,
    CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
    UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (RVID) REFERENCES REVIEW(RVID) ON DELETE CASCADE
);