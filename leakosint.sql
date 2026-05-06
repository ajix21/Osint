-- ============================================================
--  LeakOSINT Laravel - Database Schema
--  Compatible: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

CREATE DATABASE IF NOT EXISTS `leakosint_db`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `leakosint_db`;

-- ─────────────────────────────────────────────
--  TABLE: users
-- ─────────────────────────────────────────────
CREATE TABLE `users` (
  `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(255)    NOT NULL,
  `username`        VARCHAR(100)    NOT NULL UNIQUE,
  `email`           VARCHAR(255)    NOT NULL UNIQUE,
  `password`        VARCHAR(255)    NOT NULL,
  `role`            ENUM('admin','operator','viewer') NOT NULL DEFAULT 'viewer',
  `is_active`       TINYINT(1)      NOT NULL DEFAULT 1,
  `api_token`       VARCHAR(64)     NULL COMMENT 'Optional per-user LeakOSINT token',
  `last_login_at`   TIMESTAMP       NULL,
  `last_login_ip`   VARCHAR(45)     NULL,
  `remember_token`  VARCHAR(100)    NULL,
  `created_at`      TIMESTAMP       NULL,
  `updated_at`      TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `idx_role` (`role`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
--  TABLE: login_attempts
-- ─────────────────────────────────────────────
CREATE TABLE `login_attempts` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`    VARCHAR(100)    NOT NULL,
  `ip_address`  VARCHAR(45)     NOT NULL,
  `user_agent`  VARCHAR(500)    NULL,
  `success`     TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_username_ip` (`username`, `ip_address`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
--  TABLE: search_logs
-- ─────────────────────────────────────────────
CREATE TABLE `search_logs` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     BIGINT UNSIGNED NOT NULL,
  `query`       TEXT            NOT NULL,
  `limit_count` INT             NOT NULL DEFAULT 100,
  `lang`        VARCHAR(10)     NOT NULL DEFAULT 'en',
  `num_results` INT             NOT NULL DEFAULT 0,
  `num_sources` INT             NOT NULL DEFAULT 0,
  `search_time` DECIMAL(10,4)   NULL,
  `ip_address`  VARCHAR(45)     NULL,
  `created_at`  TIMESTAMP       NULL,
  `updated_at`  TIMESTAMP       NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `fk_search_logs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
--  SEED DATA
--  Default credentials:
--    admin    / Admin@12345
--    operator / Operator@12345
--    viewer   / Viewer@12345
--
--  !! GANTI PASSWORD SETELAH LOGIN PERTAMA !!
--  Hash digenerate dengan: password_hash('xxx', PASSWORD_BCRYPT, ['cost'=>12])
-- ─────────────────────────────────────────────

INSERT INTO `users` (`name`, `username`, `email`, `password`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(
  'Administrator',
  'admin',
  'admin@leakosint.local',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'admin', 1, NOW(), NOW()
),
(
  'Operator OSINT',
  'operator',
  'operator@leakosint.local',
  '$2y$12$eHD3MvAaRaXEhUFTXORkb.VR2p5EWerJyIh/mVqLT.a7A8GtAJlry',
  'operator', 1, NOW(), NOW()
),
(
  'Viewer Only',
  'viewer',
  'viewer@leakosint.local',
  '$2y$12$2ZZMQ5F4OBx0V3P2yL8b9.zOcvtj4RN/uSUFMFbJB5.9kf6dUmSsq',
  'viewer', 1, NOW(), NOW()
);

-- ─────────────────────────────────────────────
--  NOTE: Jika hash di atas tidak cocok dengan versi PHP Anda,
--  jalankan: php artisan db:seed
--  yang akan generate hash yang tepat secara otomatis.
-- ─────────────────────────────────────────────
