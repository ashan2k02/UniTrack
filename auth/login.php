<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = sanitize_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    redirect_with_message('../login.php', 'danger', 'Please enter email and password.');
}

$selectStmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE email = :email LIMIT 1');
$selectStmt->execute(['email' => $email]);
$user = $selectStmt->fetch();

if (!$user || !password_verify($password, (string) $user['password'])) {
    redirect_with_message('../login.php', 'danger', 'Invalid email or password.');
}

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];

header('Location: ../dashboard.php');
exit;
