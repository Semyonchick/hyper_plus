<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 06.10.2017
 * Time: 9:12
 */
require_once __DIR__ . '/../core/server.php';

$data = getData() ?: [];

$result = false;
if ($_POST['text']) {
    $data = $_POST['text'];
    $result = setData($data);
} else {
    $result = $data;
}

header('Content-Type: application/json');
echo json_encode([
    'result' => $result,
]);