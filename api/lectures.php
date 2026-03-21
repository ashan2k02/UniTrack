<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT id, subject, day_name, start_time, end_time, created_at FROM lectures WHERE user_id = :user_id ORDER BY FIELD(day_name, "Monday", "Tuesday", "Wednesday", "Thursday", "Friday"), start_time ASC');
    $stmt->execute(['user_id' => $userId]);

    echo json_encode([
        'ok' => true,
        'data' => $stmt->fetchAll(),
    ]);
    exit;
}

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = sanitize_input($_POST['action'] ?? 'create');

if ($action === 'create') {
    $subject = sanitize_input($_POST['subject'] ?? '');
    $dayName = sanitize_input($_POST['day_name'] ?? '');
    $startTime = sanitize_input($_POST['start_time'] ?? '');
    $endTime = sanitize_input($_POST['end_time'] ?? '');

    if ($subject === '' || $dayName === '' || $startTime === '' || $endTime === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Subject, day, start time and end time are required.']);
        exit;
    }

    if (!in_array($dayName, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], true)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Invalid lecture day.']);
        exit;
    }

    if ($endTime <= $startTime) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'End time must be after start time.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO lectures (user_id, subject, day_name, start_time, end_time) VALUES (:user_id, :subject, :day_name, :start_time, :end_time)');
    $stmt->execute([
        'user_id' => $userId,
        'subject' => $subject,
        'day_name' => $dayName,
        'start_time' => $startTime,
        'end_time' => $endTime,
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Invalid lecture id.']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM lectures WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $id, 'user_id' => $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(422);
echo json_encode(['ok' => false, 'message' => 'Invalid action.']);
