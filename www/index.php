<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function debug($data, $isDie = true, $html = false) {
        if ($html) echo '<pre>' . var_export($data, return: true) . '</pre>';
        else echo "\n" . var_export($data, true) . "\n";
        if ($isDie) {
            die();
        }
    }

    include (__DIR__ . '/autoload.php');

    // эмулируем данные POST запроса от клиента на сервер 
    $data = [
        'event_id' => rand(0, 100),
        'event_date' => date('Y-m-d h:m:s', rand(1577836800, 1893456000)), // дата от 2020-01-01 до 2030-01-01
        'ticket_adult_price' => rand(500, 2000),
        'ticket_adult_quantity' => rand(0, 15),
        'ticket_kid_price' => rand(0, 1000),
        'ticket_kid_quantity' => rand(0, 15)
    ];
    $data['tickets'] = [];

    // преобразовать данные по типу билетов в форму массива или изначально поменять структуру данных при post запросе с клиента
    if (!empty($data['ticket_adult_price']) && !empty($data['ticket_adult_quantity'])) {
        array_push($data['tickets'], 
            [
                'type' => 0,
                'price' => $data['ticket_adult_price'],
                'quantity' => $data['ticket_adult_quantity']
            ]
        );
    }

    if (!empty($data['ticket_kid_price']) && !empty($data['ticket_kid_quantity'])) {
        array_push($data['tickets'], 
            [
                'type' => 1,
                'price' => $data['ticket_kid_price'],
                'quantity' => $data['ticket_kid_quantity']
            ]
        );
    }

    // обрабатываем данные нового заказа
    $order = new order;
    $result = $order->add_order($data);
    if (!empty($result['error'])) print_r("\nerror: " . $result['error'] . "\n\n");
    if (!empty($result['message'])) print_r("\nmessage: " . $result['message'] . "\n\n");