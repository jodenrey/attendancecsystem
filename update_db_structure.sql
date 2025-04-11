-- Add teacherId column to tblclassarms table
ALTER TABLE `tblclassarms` ADD COLUMN `teacherId` INT(10) NULL;

-- Create a backup of all class teacher data
CREATE TABLE IF NOT EXISTS `tblclassteacher_backup` AS SELECT * FROM `tblclassteacher`;

-- Map existing teacher assignments to the new structure
-- First, update the tblclassarms table to include the teacher assignments
UPDATE tblclassarms ca, tblclassteacher ct 
SET ca.teacherId = ct.Id, ca.isAssigned = '1' 
WHERE ca.classId = ct.classId AND ca.Id = ct.classArmId;