CREATE TABLE `" . mysql_connect['db'] . "`.`orders_2` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `event_id` INT(11) NOT NULL DEFAULT 0,
    `event_date` DATETIME NOT NULL,
    `barcode` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `user_id` INT(10) NOT NULL DEFAULT 0, 
    `equal_price` INT(11) NOT NULL DEFAULT 0,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE (`barcode`(120))
) ENGINE = InnoDB;

CREATE TABLE `" . mysql_connect['db'] . "`.`tickets_2` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `order_id` INT(11) NOT NULL DEFAULT 0,
    `type` INT(10) NOT NULL COMMENT '0 - adult\r\n1 - kid', -- таким образом мы можем добавлять множество разных типов билетов 
    `price` INT(11) NOT NULL DEFAULT 0,
    `quantity` INT(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;