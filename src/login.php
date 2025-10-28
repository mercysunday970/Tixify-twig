<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);

$errors = [];
$toast = null;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $old = ['email' => $email];

  // Validation
  if (!$email) $errors['email'] = 'Email address is required.';
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';
  if (!$password) $errors['password'] = 'Password is required.';

  if (empty($errors)) {
    // Mock user
    $mockEmail = 'test@tixify.com';
    $mockPass = 'password123';

    if ($email === $mockEmail && $password === $mockPass) {
      $toast = ['message' => 'Login successful!', 'isError' => false];
      header("refresh:2;url=/dashboard"); // redirect after 2s
    } else {
      $errors['email'] = 'Invalid email or password.';
      $errors['password'] = 'Invalid email or password.';
      $toast = ['message' => 'Invalid credentials.', 'isError' => true];
    }
  }
}

echo $twig->render('login.twig', [
  'errors' => $errors,
  'toast' => $toast,
  'old' => $old
]);
