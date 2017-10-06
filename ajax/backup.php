<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 06.10.2017
 * Time: 9:12
 */
require_once __DIR__ . '/../core/server.php';

$backup = getData() ?: [];

if (empty($backup[date('Y-m-d')])) {
    $backup[date('Y-m-d')] = $_POST;

    $save = setData($backup);
}


echo json_encode([
    'result' => $save,
    'backups' => array_keys($backup),
]);