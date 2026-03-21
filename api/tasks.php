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
    $stmt = $pdo->prepare('SELECT id, title, deadline, priority, is_done, created_at FROM student_tasks WHERE user_id = :user_id ORDER BY created_at DESC');
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
    $title = sanitize_input($_POST['title'] ?? '');
    $deadline = sanitize_input($_POST['deadline'] ?? '');
    $priority = sanitize_input($_POST['priority'] ?? 'Medium');

    if ($title === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Task title is required.']);
        exit;
    }

    if (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $priority = 'Medium';
    }

    $deadlineValue = $deadline === '' ? null : $deadline;

    $stmt = $pdo->prepare('INSERT INTO student_tasks (user_id, title, deadline, priority) VALUES (:user_id, :title, :deadline, :priority)');
    $stmt->execute([
        'user_id' => $userId,
        'title' => $title,
        'deadline' => $deadlineValue,
        'priority' => $priority,
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

$taskId = (int) ($_POST['id'] ?? 0);
if ($taskId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid task id.']);
    exit;
}

if ($action === 'toggle') {
    $stmt = $pdo->prepare('UPDATE student_tasks SET is_done = 1 - is_done WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $taskId, 'user_id' => $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM student_tasks WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $taskId, 'user_id' => $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(422);
echo json_encode(['ok' => false, 'message' => 'Invalid action.']);
