CREATE TABLE IF NOT EXISTS `UserScans` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `scan_id` INT NOT NULL,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `Users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`scan_id`) REFERENCES `MaliciousScans`(`id`) ON DELETE CASCADE,
    UNIQUE (`user_id`, `scan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

