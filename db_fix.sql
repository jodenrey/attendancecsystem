-- Add teacherId column to tblclassarms table if it doesn't exist
ALTER TABLE `tblclassarms` ADD COLUMN `teacherId` INT(10) NULL AFTER `isAssigned`;

-- Index for faster lookups
ALTER TABLE `tblclassarms` ADD INDEX `teacher_idx` (`teacherId`);

-- Update existing assigned classes
UPDATE tblclassarms ca
JOIN tblclassteacher ct ON ca.classId = ct.classId AND ca.Id = ct.classArmId
SET ca.teacherId = ct.Id, ca.isAssigned = '1';