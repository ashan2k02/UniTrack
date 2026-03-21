<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$username = sanitize_input($_POST['username'] ?? '');
$firstName = sanitize_input($_POST['first_name'] ?? '');
$lastName = sanitize_input($_POST['last_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '') {
    $username = trim($firstName . ' ' . $lastName);
}

if ($username === '' || $email === '' || $password === '') {
    redirect_with_message('../register.php', 'danger', 'Please fill all required fields.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_message('../register.php', 'danger', 'Invalid email address.');
}

if (strlen($password) < 8) {
    redirect_with_message('../register.php', 'danger', 'Password must be at least 8 characters.');
}

$checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$checkStmt->execute(['email' => $email]);

if ($checkStmt->fetch()) {
    redirect_with_message('../register.php', 'warning', 'Email already exists. Please login.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insertStmt = $pdo->prepare(
    'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
);
$insertStmt->execute([
    'username' => $username,
    'email' => $email,
    'password' => $hashedPassword,
]);

redirect_with_message('../login.php', 'success', 'Registration successful. Please login.');
