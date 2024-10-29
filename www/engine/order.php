<?php
    final class order 
    {
        private $order;
        private $db;
        private $api;
        public function __construct () {
            $this->db = new db;
            $this->api = new api;

            // orders
            $sql = "SHOW TABLES LIKE 'orders_3'";
            $result = $this->db->get_single($sql);
            
            // event_date не является обязательным параметром в данной таблице, 
            // так как event_date относится к событию, как информацию о нем и логичнее при необхоидмости найти через join в таблице мероприятий 
            if ($result === false) {
                $sql = "CREATE TABLE `" . mysql_connect['db'] . "`.`orders_3` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT, 
                    `event_id` INT(10) NOT NULL DEFAULT 0,
                    `user_id` INT(10) NOT NULL DEFAULT 0, 
                    `equal_price` INT(11) NOT NULL DEFAULT 0,
                    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE = InnoDB;";
                $this->db->send($sql);
            }

            // tickets
            $sql = "SHOW TABLES LIKE 'tickets_3'";
            $result = $this->db->get_single($sql);
            
            // преимуществом такого подхода является то, что независимо от количества типов билетов, можно добавлять новые и новые билеты, 
            // также в том, что мы храним отдельно информацию по каждому билету и его barcode 
            if ($result === false) {
                $sql = "CREATE TABLE `" . mysql_connect['db'] . "`.`tickets_3` (
                    `id` INT(10) NOT NULL AUTO_INCREMENT, 
                    `event_id` INT(10) NOT NULL,
                    `order_id` INT(11) NOT NULL,
                    `type` INT(10) NOT NULL,
                    `price` INT(11) NOT NULL DEFAULT 0,
                    `barcode` VARCHAR(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE (`barcode`(120))
                ) ENGINE = InnoDB;";
                $this->db->send($sql);
            }
        }

        public function add_order (array $data = []) : array  // основная функция для добавления заказа
        {
            if (empty($data)) return ['error' => 'empty order'];
            $equal_price = 0;
            $quantity = 0;
            $tickets = [];
            // разделяем массив с билетами для генерации баркода под каждый билет отдельно
            foreach ($data['tickets'] as $key => $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    array_push($tickets, [
                        'type' => $item['type'],
                        'price' => $item['price']
                    ]);
                }
            }

            // Добавляем пустой заказ в БД 
            $sql = "INSERT INTO `orders_3`(
                        `event_id`, 
                        `user_id`, 
                        `equal_price`
                    ) VALUES (
                        '" . intval($data['event_id']) . "',
                        '" . intval($data['user_id']) . "',
                        '" . intval($equal_price) . "'
                    )";
            $this->db->send($sql);
            // получаем id заказа
            $order_id = $this->db->last_insert_id('orders_3');

            // для каждого билета отдельно получаем баркод
            // считаем полную стоимость 
            // и отправляем в апи 
            foreach ($tickets as $key => &$ticket) {
                $barcode = 0;
                do {
                    // генерируем новый баркод и проверяем его
                    $barcode = $this->barcodeGeneration();
                    // формируем данные для отправки в api
                    $temp = [
                        'event_id' => $data['event_id'],
                        'user_id' => $data['user_id'],
                        'type' => $ticket['type'],
                        'price' => $ticket['price'],
                        'barcode' => $barcode,
                    ];
                    $book = $this->api->book($temp); // api.site.com/book 
                } while (empty($book['message']));
                // api.site.com/approve
                $approve = $this->api->approve($barcode);

                if (empty($approve['message'])) continue;
                // если апи вернуло успешный результат, то считаем считаем общую сумму заказа и добавляем в билет баркод
                // костыль в том, что при покупке может оказаться билетов меньше, чем добавил в корзину покупатель, так как api вернет случайное состояние
                // необходимо вначале написать проверки на количество билетов разных типов (но не стал, так как нужно создавать отдельно еще таблицу с мероприятиями)
                $ticket['barcode'] = $barcode;
                $equal_price += $ticket['price'];
                $quantity++;

                // добавляем билет в БД
                $sql = "INSERT INTO `tickets_3`(
                            `event_id`, 
                            `order_id`, 
                            `type`, 
                            `price`,
                            `barcode`
                        ) VALUES (
                            '" . intval($data['event_id']) . "',
                            '" . intval($order_id) . "',
                            '" . intval($ticket['type']) . "',
                            '" . intval($ticket['price']) . "',
                            '" . intval($ticket['barcode']) . "'
                        )";
                $this->db->send($sql);
            }

            if ($quantity <= 0) return ['error' => 'no tickets'];
            if ($equal_price > 0) {
                $sql = "UPDATE `orders_3` SET `equal_price`='" . intval($equal_price). "' WHERE `id` = " . intval($order_id);
                $this->db->send($sql);
            }

            if ($quantity <= 0) return ['error' => 'no tickets'];
            return ['message' => 'order successfully aproved'];
        }
        public function barcodeGeneration (): string // генератор для баркода
        {
            // в БД указано кличество символов до 120, можно поставить ограничение в rand()
            // для генерации больших чисел 
            $barcode = rand(0, 99999999);
            return $barcode;
        } 
    }
    