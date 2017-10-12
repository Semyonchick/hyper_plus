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

$result = false;
if ($_POST && empty($backup[$key])) {
    $backup[$key] = $_POST;
    $result = setData($backup);
} elseif ($_GET['restore']) {
    $result = $backup[$_GET['restore']];
} elseif ($_GET['date']) {
    require __DIR__ . '/../views/backup-table.php';
    die;
}

header('Content-Type: application/json');
echo json_encode([
    'result' => $result,
    'backups' => array_keys($backup),
]);