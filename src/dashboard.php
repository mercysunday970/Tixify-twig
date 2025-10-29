<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
session_start();

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

// Example: Load tickets from JSON
$ticketsFile = __DIR__ . '/../data/tickets.json';
$tickets = file_exists($ticketsFile) ? json_decode(file_get_contents($ticketsFile), true) : [];

echo $twig->render('dashboard.twig', [
    'tickets' => $tickets
]);
