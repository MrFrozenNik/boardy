CREATE DATABASE IF NOT EXISTS boardy_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS boardy_api     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

GRANT ALL ON boardy_laravel.* TO 'boardy'@'%';
GRANT ALL ON boardy_api.*     TO 'boardy'@'%';
FLUSH PRIVILEGES;

USE boardy_api;
CREATE TABLE IF NOT EXISTS `comments` (
  `id`          bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`     bigint UNSIGNED NOT NULL,
  `author_id`   bigint UNSIGNED NOT NULL,
  `author_name` varchar(255) NOT NULL DEFAULT '',
  `body`        text NOT NULL,
  `created_at`  timestamp NULL DEFAULT NULL,
  `updated_at`  timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_post_id` (`post_id`),
  KEY `idx_author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
