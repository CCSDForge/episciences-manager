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
CREATE TABLE indexing_database (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url VARCHAR(500) DEFAULT NULL,
    logo VARCHAR(500) DEFAULT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED DEFAULT NULL,
    INDEX idx_indexing_db_status (status),
    FOREIGN KEY (created_by) REFERENCES USER(UID) ON DELETE SET NULL
);

-- Pivot table for ManyToMany relationship between REVIEW and indexing_database
CREATE TABLE review_indexing_database (
    indexing_database_id INT UNSIGNED NOT NULL,
    rvid INT UNSIGNED NOT NULL,
    PRIMARY KEY (indexing_database_id, rvid),
    FOREIGN KEY (indexing_database_id) REFERENCES indexing_database(id) ON DELETE CASCADE,
    FOREIGN KEY (rvid) REFERENCES REVIEW(RVID) ON DELETE CASCADE
);
