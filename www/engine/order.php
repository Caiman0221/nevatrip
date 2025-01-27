<?php
    final class order 
    {
        private $order;
        private $db;
        private $api;
        public function __construct () {
            $this->db = new db;
            $this->api = new api;

            // автоподнятие таблицы для проекта
            $sql = "SHOW TABLES LIKE 'orders'";
            $result = $this->db->get_single($sql);

            if ($result === false) {
                // в задании неточность, event_date в примере datetime, а в описании varchar(10) ?? 
                // в таблице в примере id 2 created в часе 62 минуты ?? 
                $sql = "CREATE TABLE `" . mysql_connect['db'] . "`.`orders` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT, 
                    `event_id` INT(11) NOT NULL DEFAULT 0,
                    `event_date` DATETIME NOT NULL,
                    `ticket_adult_price` INT(11) NOT NULL DEFAULT 0,
                    `ticket_adult_quantity` INT(11) NOT NULL DEFAULT 0,
                    `ticket_kid_price` INT(11) NOT NULL DEFAULT 0,
                    `ticket_kid_quantity` INT(11) NOT NULL DEFAULT 0,
                    `barcode` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                    `user_id` INT(10) NOT NULL DEFAULT 0, 
                    `equal_price` INT(11) NOT NULL DEFAULT 0,
                    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE (`barcode`(120))
                ) ENGINE = InnoDB;";
                $this->db->send($sql);
            }
        }

        public function add_order (array $data = []) : array  // основная функция для добавления заказа
        {
            if (empty($data)) return ['error' => 'empty info'];

            do {
                // генерируем новый баркод
                $barcode = $this->barcodeGeneration();
                $data['barcode'] = $barcode;
                // отправляем его в апи для проверки
                $book = $this->api->book($data);
            } while (empty($book['message']));

            // api.site.com/approve
            $approve = $this->api->approve($barcode);
            if (empty($approve['message'])) return $approve; // если вернулась ошибка, выходим

            // генерируем данные, которых не хватало для заполнения
            // попадают из личного кабинета и т.п.
            $data['user_id'] = rand(1, 1000); // можно через отправлять в post от js или хранить в сессии
            $data['equal_price'] = 
                intval($data['ticket_adult_price']) * intval($data['ticket_adult_quantity']) + 
                intval($data['ticket_kid_price']) * intval($data['ticket_kid_quantity']);

            $sql = "INSERT INTO `orders`(
                        `event_id`, 
                        `event_date`, 
                        `ticket_adult_price`, 
                        `ticket_adult_quantity`, 
                        `ticket_kid_price`, 
                        `ticket_kid_quantity`, 
                        `barcode`, 
                        `user_id`, 
                        `equal_price`
                    ) VALUES (
                        '" . intval($data['event_id']) . "',
                        " . $this->db->escape($data['event_date']) . ",
                        '" . intval($data['ticket_adult_price']) . "',
                        '" . intval($data['ticket_adult_quantity']) . "',
                        '" . intval($data['ticket_kid_price']) . "',
                        '" . intval($data['ticket_kid_quantity']) . "',
                        '" . intval($data['barcode']) . "',
                        '" . intval($data['user_id']) . "',
                        '" . intval($data['equal_price']) . "'
                    )";
            $this->db->send($sql);
            return $approve;
        }

        public function barcodeGeneration (): string // генератор для баркода
        {
            // в БД указано кличество символов до 120, можно поставить ограничение в rand()
            // для генерации больших чисел 
            $barcode = rand(0, 99999999);
            return $barcode;
        } 
    }
    