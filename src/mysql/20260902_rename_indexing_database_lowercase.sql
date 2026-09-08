-- ============================================================================
-- Migration: Rename INDEXING_DATABASE tables and columns to lowercase
-- ============================================================================
-- This migration converts table and column names to lowercase for consistency
-- with MySQL/MariaDB naming conventions.
-- ============================================================================

-- Rename the main table
RENAME TABLE INDEXING_DATABASE TO indexing_database;

-- Rename the pivot table
RENAME TABLE REVIEW_INDEXING_DATABASE TO review_indexing_database;

-- Note: Column names in MySQL are case-insensitive by default,
-- but we rename them for consistency in schema definitions.
-- The column renames below require MySQL 8.0+ syntax.

-- Rename columns in indexing_database
ALTER TABLE indexing_database
    RENAME COLUMN ID TO id,
    RENAME COLUMN NAME TO name,
    RENAME COLUMN URL TO url,
    RENAME COLUMN LOGO TO logo,
    RENAME COLUMN STATUS TO status,
    RENAME COLUMN CREATED_AT TO created_at,
    RENAME COLUMN UPDATED_AT TO updated_at,
    RENAME COLUMN CREATED_BY TO created_by;

-- Rename indexes
ALTER TABLE indexing_database
    RENAME INDEX IDX_INDEXING_DB_STATUS TO idx_indexing_db_status,
    RENAME INDEX INDEXING_DATABASE_ibfk_1 TO indexing_database_ibfk_1;

-- Rename columns and index in review_indexing_database
ALTER TABLE review_indexing_database
    RENAME COLUMN INDEXING_DATABASE_ID TO indexing_database_id,
    RENAME COLUMN RVID TO rvid,
    RENAME INDEX REVIEW_INDEXING_DATABASE_ibfk_2 TO review_indexing_database_ibfk_2;

ALTER TABLE indexing_database ADD UNIQUE INDEX unique_url (url);
