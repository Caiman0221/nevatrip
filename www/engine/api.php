<?php

    // эмуляция стороннего api.site.com
    final class api
    {
        private $db;
        public function __construct() {
            $this->db = new db;
        }
        // проверка на существование такого баркода в БД, если его нет, то возвращаем результат
        public function book (array $data) : array {
            $sql = "SELECT count(*) FROM `tickets_3` WHERE `barcode` = " . intval($data['barcode']);
            $result = $this->db->get_single($sql); 

            if ($result['count(*)'] === 0) {
                return ['message' => 'order successfully booked'];
            } else {
                return ['error' => 'barcode already exists'];
            }
        }
        // отправляем случайный ответ от стороннего апи
        public function approve (int $barcode) : array {
            $responses = [
                ['message' => 'order successfully aproved'],
                ['error' => 'event cancelled'],
                ['error' => 'no tickets'],
                ['error' => 'no seats'],
                ['error' => 'fan removed']
            ];
            $type = rand(0, 1); // true или false
            if ($type === 1) return $responses[0];
            
            $type = rand(1, 4);
            return $responses[$type];
        }
    }
    