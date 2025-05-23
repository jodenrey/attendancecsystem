-- Remove term functionality from database structure

-- 1. Create backup of tblsessionterm table before modifying
CREATE TABLE IF NOT EXISTS tblsessionterm_backup AS SELECT * FROM tblsessionterm;

-- 2. Remove termId references from tblsessionterm table
ALTER TABLE tblsessionterm DROP COLUMN termId;

-- 3. Update existing records to ensure they work without termId
-- No additional updates needed as we're simply removing the column

-- 4. For safety, we'll keep the tblterm table but it won't be used anymore
-- You can manually drop this table later if desired: DROP TABLE tblterm;
