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
    $stmt = $pdo->prepare('SELECT id, subject_name, credits, grade_point, created_at FROM gpa_subjects WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute(['user_id' => $userId]);
    $subjects = $stmt->fetchAll();

    $totalCredits = 0;
    $totalPoints = 0.0;
    foreach ($subjects as $subject) {
        $credits = (int) $subject['credits'];
        $gradePoint = (float) $subject['grade_point'];
        $totalCredits += $credits;
        $totalPoints += $credits * $gradePoint;
    }

    $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : null;

    echo json_encode([
        'ok' => true,
        'data' => $subjects,
        'summary' => [
            'total_credits' => $totalCredits,
            'total_points' => round($totalPoints, 2),
            'gpa' => $gpa,
        ],
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
    $subjectName = sanitize_input($_POST['subject_name'] ?? '');
    $credits = (int) ($_POST['credits'] ?? 0);
    $gradePoint = (float) ($_POST['grade_point'] ?? -1);

    if ($subjectName === '' || $credits < 1 || $credits > 6) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Invalid subject or credits.']);
        exit;
    }

    if (!in_array($gradePoint, [4.0, 3.7, 3.3, 3.0, 2.7, 2.3, 2.0, 1.7, 1.3, 1.0, 0.0], true)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Invalid grade point.']);
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO gpa_subjects (user_id, subject_name, credits, grade_point) VALUES (:user_id, :subject_name, :credits, :grade_point)');
    $stmt->execute([
        'user_id' => $userId,
        'subject_name' => $subjectName,
        'credits' => $credits,
        'grade_point' => $gradePoint,
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'message' => 'Invalid subject id.']);
        exit;
    }

    $stmt = $pdo->prepare('DELETE FROM gpa_subjects WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $id, 'user_id' => $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

if ($action === 'clear') {
    $stmt = $pdo->prepare('DELETE FROM gpa_subjects WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(422);
echo json_encode(['ok' => false, 'message' => 'Invalid action.']);
