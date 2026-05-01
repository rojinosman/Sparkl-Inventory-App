-- Local development schema + seed user (run: mysql -u root paul_bulkdb < live/dev-bootstrap.sql)
-- Model:
--  - inventory_objects: object/types (bowl, clamshell)
--  - inventory: one row per RFID tag; includes assigned object/user

SET NAMES utf8mb4;
SET @db = DATABASE();

CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL DEFAULT '',
  `lastname` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(32) NOT NULL DEFAULT 'vendor',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `notif_email` varchar(255) NOT NULL DEFAULT '',
  `email_authcode` varchar(255) NOT NULL DEFAULT '',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(512) NOT NULL DEFAULT '',
  `street1` varchar(255) NOT NULL DEFAULT '',
  `street2` varchar(255) NOT NULL DEFAULT '',
  `city` varchar(255) NOT NULL DEFAULT '',
  `state` varchar(32) NOT NULL DEFAULT '',
  `zip` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `parent_id` int NOT NULL DEFAULT 0,
  `status` varchar(16) NOT NULL DEFAULT 'A',
  `username` varchar(255) NOT NULL DEFAULT '',
  `user_data` text,
  `is_locked` varchar(8) NOT NULL DEFAULT '0',
  `login_attempts_remaining` varchar(8) NOT NULL DEFAULT '5',
  `lock_expire_d` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `inventory_objects` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `label` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_inventory_objects_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `inventory_objects` (`id`, `name`, `label`) VALUES
  (1, 'clamshell', 'Clamshell'),
  (2, 'bowl', 'Bowl');

CREATE TABLE IF NOT EXISTS `inventory` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `rfid_uid` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @has_rfid_col = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'inventory' AND COLUMN_NAME = 'rfid_uid'
);
SET @q = IF(@has_rfid_col = 0, 'ALTER TABLE inventory ADD COLUMN rfid_uid VARCHAR(128) NULL', 'SELECT 1');
PREPARE s FROM @q; EXECUTE s; DEALLOCATE PREPARE s;

INSERT IGNORE INTO `inventory` (`rfid_uid`)
VALUES
  ('E2801170000002034AABBCC01'),
  ('E2801170000002034AABBCC02');

CREATE TABLE IF NOT EXISTS `api_tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `token` char(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_api_tokens_token` (`token`),
  KEY `idx_api_tokens_user` (`user_id`),
  CONSTRAINT `fk_api_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `user_inventory` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `inventory_id` int unsigned NOT NULL,
  `rfid_uid` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_user_rfid` (`user_id`,`rfid_uid`),
  KEY `idx_user_inventory_user` (`user_id`),
  KEY `idx_user_inventory_inv` (`inventory_id`),
  CONSTRAINT `fk_user_inventory_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_inventory_inv` FOREIGN KEY (`inventory_id`) REFERENCES `inventory_objects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login: Operator@test.local / OperatorTest1!
INSERT INTO `users` (
  `firstname`, `lastname`, `type`, `phone`, `email`, `notif_email`, `email_authcode`,
  `company_name`, `title`, `url`, `street1`, `street2`, `city`, `state`, `zip`,
  `password`, `parent_id`, `status`, `username`, `user_data`
) VALUES (
  'Dev', 'Admin', 'admin', '4155551212', 'Operator@test.local', 'Operator@test.local', '',
  'Sparkl Local', '', '', '', '', 'San Francisco', 'CA', '94102',
  '$2y$12$EpEB3zeX9cJIAQxVdCy9yOWMkJ.X/7iSXHekFSNlOUUJiXd43ylUe',
  0, 'A', 'devadmin', NULL
) ON DUPLICATE KEY UPDATE
  `password` = VALUES(`password`),
  `type` = VALUES(`type`),
  `status` = VALUES(`status`);
