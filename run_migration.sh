#!/bin/bash
cd /workspace/StaffAttendance_System
docker-compose exec -T mysql mysql -u root -proot staffAttend_data <<EOF
-- Convert staff_id from numeric to formatted (ST110110, ST110111, etc.)

-- Step 1: Add a temporary column to store the old staff_id
ALTER TABLE staff ADD COLUMN old_staff_id BIGINT UNSIGNED NULL;

-- Step 2: Back up the existing staff_ids
UPDATE staff SET old_staff_id = staff_id;

-- Step 3: Alter staff_id column to VARCHAR(20) to hold formatted IDs
ALTER TABLE staff MODIFY COLUMN staff_id VARCHAR(20);

-- Step 4: Generate and assign formatted staff_ids
SET @counter = 10;
UPDATE staff 
SET staff_id = CONCAT('ST1101', LPAD(@counter:=@counter+1, 2, '0'))
ORDER BY id ASC;

-- Step 5: Add unique constraint on staff_id
ALTER TABLE staff ADD UNIQUE KEY unique_staff_id (staff_id);

-- Step 6: Drop the temporary column
ALTER TABLE staff DROP COLUMN old_staff_id;

-- Verification: Check the converted staff_ids
SELECT id, staff_id, staff_name, staff_email FROM staff ORDER BY id;
EOF
