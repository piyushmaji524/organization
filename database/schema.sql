-- ============================================================
-- Sarak Youth Development Council — Database Schema v1.0
-- Engine: InnoDB | Charset: utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- roles
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_key`     VARCHAR(50)  NOT NULL UNIQUE COMMENT 'super_admin | president | vp | treasurer | secretary | asst_secretary | it_head | event_manager | sm_manager',
  `display_name` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- admins
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name`          VARCHAR(150) NOT NULL,
  `role_id`       INT UNSIGNED NOT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- role_permissions
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role_id`     INT UNSIGNED NOT NULL,
  `section_key` VARCHAR(60)  NOT NULL COMMENT 'dashboard|members|events|rsvp|news|gallery|messages|applications|donate|settings|content|role_permissions|admin_users',
  `can_view`    TINYINT(1)   NOT NULL DEFAULT 0,
  `can_edit`    TINYINT(1)   NOT NULL DEFAULT 0,
  `can_delete`  TINYINT(1)   NOT NULL DEFAULT 0,
  UNIQUE KEY `role_section` (`role_id`, `section_key`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- members
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `members` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name_en`          VARCHAR(150) NOT NULL,
  `name_hi`          VARCHAR(150) NOT NULL,
  `name_bn`          VARCHAR(150) NOT NULL,
  `designation_en`   VARCHAR(200) NOT NULL,
  `designation_hi`   VARCHAR(200) NOT NULL,
  `designation_bn`   VARCHAR(200) NOT NULL,
  `bio_en`           TEXT,
  `bio_hi`           TEXT,
  `bio_bn`           TEXT,
  `achievements_en`  TEXT,
  `achievements_hi`  TEXT,
  `achievements_bn`  TEXT,
  `photo`            VARCHAR(255) DEFAULT NULL,
  `category`         ENUM('executive','core','advisory') NOT NULL DEFAULT 'core',
  `display_order`    INT          NOT NULL DEFAULT 99,
  `email`            VARCHAR(200) DEFAULT NULL,
  `phone`            VARCHAR(30)  DEFAULT NULL,
  `whatsapp`         VARCHAR(30)  DEFAULT NULL,
  `facebook_url`     VARCHAR(255) DEFAULT NULL,
  `instagram_url`    VARCHAR(255) DEFAULT NULL,
  `linkedin_url`     VARCHAR(255) DEFAULT NULL,
  `is_active`        TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- events
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title_en`      VARCHAR(300) NOT NULL,
  `title_hi`      VARCHAR(300) NOT NULL,
  `title_bn`      VARCHAR(300) NOT NULL,
  `description_en` TEXT,
  `description_hi` TEXT,
  `description_bn` TEXT,
  `event_date`    DATE         NOT NULL,
  `event_time`    TIME         DEFAULT NULL,
  `location_en`   VARCHAR(300) DEFAULT NULL,
  `location_hi`   VARCHAR(300) DEFAULT NULL,
  `location_bn`   VARCHAR(300) DEFAULT NULL,
  `type`          ENUM('religious','sports','education','business','general') NOT NULL DEFAULT 'general',
  `cover_image`   VARCHAR(255) DEFAULT NULL,
  `status`        ENUM('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `rsvp_enabled`  TINYINT(1)   NOT NULL DEFAULT 1,
  `max_attendees` INT          DEFAULT NULL,
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- rsvp
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rsvp` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `event_id`       INT UNSIGNED NOT NULL,
  `name`           VARCHAR(200) NOT NULL,
  `email`          VARCHAR(200) DEFAULT NULL,
  `phone`          VARCHAR(30)  NOT NULL,
  `attendee_count` INT          NOT NULL DEFAULT 1,
  `registered_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- gallery
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `gallery` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `image_path`  VARCHAR(255) DEFAULT NULL,
  `caption_en`  VARCHAR(300) DEFAULT NULL,
  `caption_hi`  VARCHAR(300) DEFAULT NULL,
  `caption_bn`  VARCHAR(300) DEFAULT NULL,
  `event_id`    INT UNSIGNED DEFAULT NULL,
  `year`        YEAR         NOT NULL,
  `is_video`    TINYINT(1)   NOT NULL DEFAULT 0,
  `video_url`   VARCHAR(500) DEFAULT NULL,
  `uploaded_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- news
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `news` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title_en`    VARCHAR(400) NOT NULL,
  `title_hi`    VARCHAR(400) NOT NULL,
  `title_bn`    VARCHAR(400) NOT NULL,
  `content_en`  LONGTEXT,
  `content_hi`  LONGTEXT,
  `content_bn`  LONGTEXT,
  `category`    VARCHAR(50)  DEFAULT 'general',
  `cover_image` VARCHAR(255) DEFAULT NULL,
  `is_alert`    TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 = show as home banner',
  `published_at` TIMESTAMP   DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- messages
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(200) NOT NULL,
  `email`      VARCHAR(200) NOT NULL,
  `phone`      VARCHAR(30)  DEFAULT NULL,
  `subject`    VARCHAR(300) DEFAULT NULL,
  `message`    TEXT         NOT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- applications
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `applications` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name`   VARCHAR(200) NOT NULL,
  `father_name` VARCHAR(200) NOT NULL,
  `age`         INT          NOT NULL,
  `address`     TEXT         NOT NULL,
  `phone`       VARCHAR(30)  NOT NULL,
  `email`       VARCHAR(200) DEFAULT NULL,
  `education`   VARCHAR(300) DEFAULT NULL,
  `occupation`  VARCHAR(300) DEFAULT NULL,
  `referral`    VARCHAR(200) DEFAULT NULL,
  `photo`       VARCHAR(255) DEFAULT NULL,
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_note`  TEXT         DEFAULT NULL,
  `applied_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- site_settings (key-value store)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key`   VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` LONGTEXT     DEFAULT NULL,
  `updated_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
