-- Run after dev-api-upgrade.sql
-- Purpose:
-- 1) inventory table becomes full RFID tag registry
-- 2) inventory_objects stores types
-- 3) migrate user_inventory rows into inventory
-- 4) movements and user_inventory reference inventory_objects for type IDs

SET @db = DATABASE();

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `locations` (`id`, `name`, `sort_order`) VALUES
  (1, 'Main warehouse', 10),
  (2, 'Kitchen / site A', 20),
  (3, 'Supplier dock', 30),
  (4, 'Transit / truck', 40);

-- Ensure inventory has tag-assignment columns
SET @has_obj_col = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'inventory_object_id'
);
SET @q = IF(@has_obj_col = 0, 'ALTER TABLE inventory ADD COLUMN inventory_object_id INT UNSIGNED NULL', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_user_col = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'assigned_user_id'
);
SET @q = IF(@has_user_col = 0, 'ALTER TABLE inventory ADD COLUMN assigned_user_id INT UNSIGNED NULL', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_assigned_at = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'assigned_at'
);
SET @q = IF(@has_assigned_at = 0, 'ALTER TABLE inventory ADD COLUMN assigned_at DATETIME NULL', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_created_at = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'created_at'
);
SET @q = IF(@has_created_at = 0, 'ALTER TABLE inventory ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_updated_at = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'updated_at'
);
SET @q = IF(@has_updated_at = 0, 'ALTER TABLE inventory ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_idx_obj = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND INDEX_NAME = 'idx_inventory_object'
);
SET @q = IF(@has_idx_obj = 0, 'ALTER TABLE inventory ADD KEY idx_inventory_object (inventory_object_id)', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_idx_user = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND INDEX_NAME = 'idx_inventory_user'
);
SET @q = IF(@has_idx_user = 0, 'ALTER TABLE inventory ADD KEY idx_inventory_user (assigned_user_id)', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_uq_rfid = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND INDEX_NAME = 'uk_inventory_rfid'
);
SET @q = IF(@has_uq_rfid = 0, 'ALTER TABLE inventory ADD UNIQUE KEY uk_inventory_rfid (rfid_uid)', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- Copy old type rows from inventory -> inventory_objects (for old schema)
SET @has_name_col = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'name'
);
SET @q = IF(
  @has_name_col > 0,
  'INSERT IGNORE INTO inventory_objects (id, name, label) SELECT id, name, label FROM inventory WHERE name IS NOT NULL AND name != ''''',
  'SELECT 1'
);
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @q = IF(
  @has_name_col > 0,
  'ALTER TABLE inventory MODIFY COLUMN name VARCHAR(128) NOT NULL DEFAULT '''', MODIFY COLUMN label VARCHAR(255) NOT NULL DEFAULT ''''',
  'SELECT 1'
);
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- Migrate user_inventory into inventory tag registry
SET @has_ui_tbl = (
  SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'user_inventory'
);
SET @q = IF(
  @has_ui_tbl > 0,
  'INSERT INTO inventory (rfid_uid, inventory_object_id, assigned_user_id, assigned_at)
   SELECT LOWER(TRIM(ui.rfid_uid)), ui.inventory_id, ui.user_id, ui.created_at
   FROM user_inventory ui
   WHERE ui.rfid_uid IS NOT NULL AND TRIM(ui.rfid_uid) != ''''
   ON DUPLICATE KEY UPDATE
     inventory_object_id = VALUES(inventory_object_id),
     assigned_user_id = VALUES(assigned_user_id),
     assigned_at = VALUES(assigned_at)',
  'SELECT 1'
);
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- user_inventory indexes
SET @has_user_idx = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'user_inventory' AND INDEX_NAME = 'idx_user_inventory_user'
);
SET @q = IF(@has_user_idx = 0, 'ALTER TABLE user_inventory ADD KEY idx_user_inventory_user (user_id)', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_old = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'user_inventory' AND INDEX_NAME = 'uq_user_inventory'
);
SET @q = IF(@has_old > 0, 'ALTER TABLE user_inventory DROP INDEX uq_user_inventory', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

SET @has_new = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'user_inventory' AND INDEX_NAME = 'uq_user_rfid'
);
SET @q = IF(@has_new = 0, 'ALTER TABLE user_inventory ADD UNIQUE KEY uq_user_rfid (user_id, rfid_uid)', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- Repoint user_inventory foreign key to inventory_objects
SET @has_fk_ui_old = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @db AND TABLE_NAME = 'user_inventory' AND CONSTRAINT_NAME = 'fk_user_inventory_inv'
);
SET @q = IF(@has_fk_ui_old > 0, 'ALTER TABLE user_inventory DROP FOREIGN KEY fk_user_inventory_inv', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;
SET @q = IF(
  @has_ui_tbl > 0,
  'ALTER TABLE user_inventory ADD CONSTRAINT fk_user_inventory_inv FOREIGN KEY (inventory_id) REFERENCES inventory_objects (id) ON DELETE CASCADE',
  'SELECT 1'
);
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

-- Recreate movements table to guarantee inventory_id -> inventory_objects
DROP TABLE IF EXISTS `inventory_movements`;
CREATE TABLE IF NOT EXISTS `inventory_movements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `direction` enum('in','out') NOT NULL,
  `inventory_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT 1,
  `from_location_id` int unsigned DEFAULT NULL,
  `to_location_id` int unsigned DEFAULT NULL,
  `rfid_uid` varchar(128) DEFAULT NULL,
  `note` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inv_move_user` (`user_id`),
  KEY `idx_inv_move_created` (`created_at`),
  CONSTRAINT `fk_inv_move_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inv_move_inv` FOREIGN KEY (`inventory_id`) REFERENCES `inventory_objects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inv_move_from` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_inv_move_to` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
