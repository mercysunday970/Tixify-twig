<?php
// src/getStarted.php

require_once __DIR__ . '/../vendor/autoload.php'; // ✅ Correct path to autoload

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

// Load Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates'); // ✅ Correct path to templates
$twig = new Environment($loader);

// Ensure data directory exists
$dataDir = __DIR__ . '/../data'; // store data in project root
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$dataFile = $dataDir . '/users.json';
$users = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Simple validation
    if ($fname && $lname && $username && $email && $password) {
        // Check if email already exists
if (!empty($users)) {
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION['error'] = "Email already exists!";
            header('Location: getStarted.php');
            exit;
        }
    }
}

        // Add new user
        $users[] = [
            'fname' => $fname,
            'lname' => $lname,
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        file_put_contents($dataFile, json_encode($users, JSON_PRETTY_PRINT));

        $_SESSION['success'] = "Signup successful! You are now logged in.";

        // ✅ Redirect to dashboard after signup
        $_SESSION['user'] = $username; // optional: store logged-in user
        header('Location: /src/dashboard.php'); 
        exit;
    } else {
        $_SESSION['error'] = "Please fill out all fields.";
        header('Location: getStarted.php');
        exit;
    }
}

// Pass success or error message to Twig
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;

// Clear session messages
unset($_SESSION['success'], $_SESSION['error']);

// Render the page
echo $twig->render('getStarted.twig', [
    'success' => $success,
    'error' => $error
]);

