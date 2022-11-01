<?php

if (isset($_POST['name'])) {
    $searchword = $_POST['name'];
    $char = mb_strtoupper(substr($searchword, 0, 3), "utf-8"); // первый символ в верхний регистр
    $searchword[0] = $char[0];
    $searchword[1] = $char[1];
} else {
    echo \json_encode('Город не найден');
}

$jsonFile = 'http://127.0.0.1/data/russian-cities.json';
$content = file_get_contents($jsonFile);

if ($content == '') {
    $cities = array();
} else {
    $cities = \json_decode($content);
}

$city = [];
foreach ($cities as $item) {
    if (stripos($item->name, $searchword) === 0) {
        $city[] = [
            'name'=>$item->name,
            'lat' => $item->coords->lat,
            'lng' => $item->coords->lon,
            'district' => $item->district
        ];
    }
}

$_POST['maxRows'] = '5';

if (count($city)>0) {
    if (count($city)>$_POST['maxRows']) {
        $city = array_slice($city, 0, $_POST['maxRows']);
    }
    echo json_encode($city);
} else {
    $err[] = array('name'=>'Город не найден');
    echo json_encode($err);
}
