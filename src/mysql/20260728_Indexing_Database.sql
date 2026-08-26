-- ============================================================================
-- Indexing Database Tables
-- ============================================================================
-- Stores indexing databases (DOAJ, AOJ, Scopus, etc.) that journals can be
-- referenced in.
--
-- Features:
-- - Centralized catalog of indexing databases moderated by epiadmin
-- - Status workflow: pending → validated/rejected
-- - ManyToMany relationship with REVIEW table
-- ============================================================================

-- Main table for indexing databases
CREATE TABLE INDEXING_DATABASE (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NAME VARCHAR(255) NOT NULL,
    URL VARCHAR(500) DEFAULT NULL,
    LOGO VARCHAR(500) DEFAULT NULL,
    STATUS VARCHAR(255) NOT NULL DEFAULT 'pending',
    CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
    UPDATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CREATED_BY INT UNSIGNED DEFAULT NULL,
    INDEX IDX_INDEXING_DB_STATUS (STATUS),
    FOREIGN KEY (CREATED_BY) REFERENCES USER(UID) ON DELETE SET NULL
);

-- Pivot table for ManyToMany relationship between REVIEW and INDEXING_DATABASE
CREATE TABLE REVIEW_INDEXING_DATABASE (
    INDEXING_DATABASE_ID INT UNSIGNED NOT NULL,
    RVID INT UNSIGNED NOT NULL,
    PRIMARY KEY (INDEXING_DATABASE_ID, RVID),
    FOREIGN KEY (INDEXING_DATABASE_ID) REFERENCES INDEXING_DATABASE(ID) ON DELETE CASCADE,
    FOREIGN KEY (RVID) REFERENCES REVIEW(RVID) ON DELETE CASCADE
);
