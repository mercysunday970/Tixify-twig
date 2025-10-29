<?php
session_start();

$dataFile = __DIR__ . '/../data/tickets.json';
$tickets = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

$id = $_POST['id'] ?? time();
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$status = $_POST['status'] ?? 'Open';
$createdAt = $_POST['createdAt'] ?? date('Y-m-d H:i:s');

if ($title && $description) {
    $found = false;
    foreach ($tickets as &$t) {
        if ($t['id'] == $id) {
            $t['title'] = $title;
            $t['description'] = $description;
            $t['status'] = $status;
            $found = true;
            break;
        }
    }
    unset($t);

    if (!$found) {
        $tickets[] = [
            'id' => $id,
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'createdAt' => $createdAt
        ];
    }

    file_put_contents($dataFile, json_encode(array_values($tickets), JSON_PRETTY_PRINT));
}

header('Location: tickets.php');
exit;
