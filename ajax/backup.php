<?php
/**
 * Created by PhpStorm.
 * User: semyonchick
 * Date: 06.10.2017
 * Time: 9:12
 */
require_once __DIR__ . '/../core/server.php';

$backup = getData() ?: [];
$key = date('Y-m-d H:i');

if ($_POST && empty($backup[$key])) {
    $backup[$key] = $_POST;
    $save = setData($backup);
} elseif ($_GET['date']) {
    require __DIR__ . '/../views/backup-table.php';
    die;
}

header('Content-Type: application/json');
echo json_encode([
    'result' => $save,
    'backups' => array_keys($backup),
]);