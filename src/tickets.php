<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

// --- Load Twig ---
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');

$twig = new Environment($loader);

// --- Ensure data directory exists ---
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$dataFile = $dataDir . '/tickets.json';
$tickets = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// --- Handle POST (Create/Edit Ticket) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? time();
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Open';
    $createdAt = $_POST['createdAt'] ?? date('c');

    if ($title && $description) {
        // Check if ticket exists
        $exists = false;
        foreach ($tickets as &$ticket) {
            if ($ticket['id'] == $id) {
                $ticket['title'] = $title;
                $ticket['description'] = $description;
                $ticket['status'] = $status;
                $ticket['createdAt'] = $createdAt;
                $exists = true;
                break;
            }
        }
        unset($ticket);

        if (!$exists) {
            $tickets[] = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'createdAt' => $createdAt
            ];
        }

        file_put_contents($dataFile, json_encode($tickets, JSON_PRETTY_PRINT));
        $_SESSION['success'] = $exists ? 'Ticket updated!' : 'Ticket created!';
    } else {
        $_SESSION['error'] = 'Title and description are required!';
    }

    header('Location: tickets.php');
    exit;
}

// --- Handle GET Delete ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $tickets = array_filter($tickets, fn($t) => $t['id'] != $id);
    file_put_contents($dataFile, json_encode(array_values($tickets), JSON_PRETTY_PRINT));
    $_SESSION['success'] = 'Ticket deleted!';
    header('Location: tickets.php');
    exit;
}

// --- Pass messages ---
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

// --- Render Twig ---
echo $twig->render('tickets.twig', [
    'tickets' => $tickets,
    'success' => $success,
    'error' => $error,
]);

