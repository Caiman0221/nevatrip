-- предложенные варинаты таблиц для БД
-- Таблица с заказами
CREATE TABLE `" . mysql_connect['db'] . "`.`orders` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `event_id` INT(10) NOT NULL DEFAULT 0, -- информация о мероприятии хранится в таблице events и доступна по id через left join
    `user_id` INT(10) NOT NULL DEFAULT 0, -- информация о пользователе
    `equal_price` INT(11) NOT NULL DEFAULT 0, -- полная стоимость, сколько заплатил пользователь
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- дата, когда пользователь оплатил билеты
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- Таблица с билетами
CREATE TABLE `" . mysql_connect['db'] . "`.`tickets` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `event_id` INT(10) NOT NULL, -- продублирована относительно orders для того, чтобы не делать 2а join (для удобства)
    `order_id` INT(11) NOT NULL, -- информация о том, кто сделал заказ
    `type` INT(10) NOT NULL, -- тип билета из таблицы ticket_types, для того, чтобы организатор мог менять типы билетов или добавлять новые
    `price` INT(11) NOT NULL DEFAULT 0, -- цена билета ингда может варироваться (за день до мероприятия стоить дороже, так что храним реальную цену билета, сколько за нее заплатили) (аналогично можно сделать и с названием типа билета)
    `barcode` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, -- у каждого билета он уникален UNIQUE (`barcode`(120))
    PRIMARY KEY (`id`),
    UNIQUE (`barcode`(120))
) ENGINE = InnoDB;

-- таблица с типами билетов
CREATE TABLE `" . mysql_connect['db'] . "`.`ticket_types` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `event_id` INT(10) NOT NULL,
    `type` INT(10) NOT NULL,
    `text` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `price` INT(11) NOT NULL DEFAULT 0,
    `quantity` INT(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

-- пример таблицы с мероприятиями
CREATE TABLE `" . mysql_connect['db'] . "`.`events` (
    `id` INT(10) NOT NULL AUTO_INCREMENT, 
    `event_date` DATETIME NOT NULL,
    `name` VARCHAR(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;