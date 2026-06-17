-- Migration: Blog System
-- Run this once in phpMyAdmin

CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `slug`       VARCHAR(100) NOT NULL UNIQUE,
  `color`      VARCHAR(7)   DEFAULT '#C9A84C',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_tags` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(80) NOT NULL,
  `slug`       VARCHAR(80) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`         VARCHAR(220) NOT NULL UNIQUE,
  `title_en`     VARCHAR(300) NOT NULL,
  `title_hi`     VARCHAR(300) DEFAULT '',
  `title_bn`     VARCHAR(300) DEFAULT '',
  `content_en`   LONGTEXT,
  `content_hi`   LONGTEXT,
  `content_bn`   LONGTEXT,
  `excerpt_en`   TEXT,
  `excerpt_hi`   TEXT,
  `excerpt_bn`   TEXT,
  `thumbnail`    VARCHAR(255) DEFAULT NULL,
  `category_id`  INT UNSIGNED DEFAULT NULL,
  `author_name`  VARCHAR(100) DEFAULT 'SYDC Team',
  `status`       ENUM('draft','published') DEFAULT 'draft',
  `views`        INT UNSIGNED DEFAULT 0,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_tags` (
  `post_id` INT UNSIGNED NOT NULL,
  `tag_id`  INT UNSIGNED NOT NULL,
  PRIMARY KEY (`post_id`, `tag_id`),
  FOREIGN KEY (`post_id`) REFERENCES `blog_posts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tag_id`)  REFERENCES `blog_tags`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
