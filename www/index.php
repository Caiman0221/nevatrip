<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // дебагер
    function debug($data, $isDie = true, $html = false) {
        if ($html) echo '<pre>' . var_export($data, return: true) . '</pre>';
        else echo "\n" . var_export($data, true) . "\n";
        if ($isDie) {
            die();
        }
    }

    include (__DIR__ . '/autoload.php');

    // эмулируем данные, которые поступают через post запрос 
    $data = [
        'event_id' => rand(0, 100), // id события
        'user_id' => rand(1, 1000), // обычно хранится в сессии, но можно 
        // 'event_date' => date('Y-m-d h:m:s', rand(1577836800, 1893456000)), // дата от 2020-01-01 до 2030-01-01
        'tickets' => [] // массив с билетами, которые оформил покупатель
        
    ];
    // предположим всего 4 варианта билетов, которые могут быть, эмулируем те, которые приобрел покупатель
    for ($i = 0; $i < 4; $i ++) {
        array_push($data['tickets'], [
            'type' => $i, // тип билета
            'price' => rand(500, 2000), // цена билета
            'quantity' => rand(0, 10), // количестов купленных билетов данного типа
        ]);
    }

    // обрабатываем данные нового заказа
    $order = new order;
    $result = $order->add_order($data);
    if (!empty($result['error'])) print_r("\nerror: " . $result['error'] . "\n\n");
    if (!empty($result['message'])) print_r("\nmessage: " . $result['message'] . "\n\n");