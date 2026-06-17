-- Migration: General Members feature
-- Run this once on your Hostinger MySQL database via phpMyAdmin

ALTER TABLE `applications`
  ADD COLUMN `member_id`       VARCHAR(12)  DEFAULT NULL AFTER `status`,
  ADD COLUMN `show_on_website` TINYINT(1)   NOT NULL DEFAULT 0 AFTER `member_id`,
  ADD COLUMN `badge_name`      VARCHAR(100) DEFAULT NULL AFTER `show_on_website`,
  ADD COLUMN `visible_fields`  TEXT         DEFAULT NULL AFTER `badge_name`,
  ADD UNIQUE KEY `uniq_member_id` (`member_id`);
