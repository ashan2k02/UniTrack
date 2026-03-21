<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login('login.php');

$userId = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Student';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $deadline = sanitize_input($_POST['deadline'] ?? '');
    $priority = sanitize_input($_POST['priority'] ?? 'Medium');

    if ($title === '') {
        redirect_with_message('my_tasks.php', 'danger', 'Task title is required.');
    }

    if (!in_array($priority, ['High', 'Medium', 'Low'], true)) {
        $priority = 'Medium';
    }

    if ($deadline === '') {
        $deadline = null;
    }

    $insert = $pdo->prepare('INSERT INTO student_tasks (user_id, title, deadline, priority) VALUES (:user_id, :title, :deadline, :priority)');
    $insert->execute([
        'user_id' => $userId,
        'title' => $title,
        'deadline' => $deadline,
        'priority' => $priority,
    ]);

    redirect_with_message('my_tasks.php', 'success', 'Task saved to database.');
}

$action = sanitize_input($_GET['action'] ?? '');
$taskId = (int) ($_GET['id'] ?? 0);

if ($action !== '' && $taskId > 0) {
    if ($action === 'toggle') {
        $toggle = $pdo->prepare('UPDATE student_tasks SET is_done = 1 - is_done WHERE id = :id AND user_id = :user_id');
        $toggle->execute(['id' => $taskId, 'user_id' => $userId]);
    }

    if ($action === 'delete') {
        $delete = $pdo->prepare('DELETE FROM student_tasks WHERE id = :id AND user_id = :user_id');
        $delete->execute(['id' => $taskId, 'user_id' => $userId]);
    }

    header('Location: my_tasks.php');
    exit;
}

$listStmt = $pdo->prepare('SELECT id, title, deadline, priority, is_done, created_at FROM student_tasks WHERE user_id = :user_id ORDER BY created_at DESC');
$listStmt->execute(['user_id' => $userId]);
$tasks = $listStmt->fetchAll();

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Tasks - UniTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
</head>

<body class="dash-body">
    <nav class="navbar navbar-expand-lg fixed-top glass-nav" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand brand-logo" href="index.php" aria-label="UniTrack home">
                <span class="brand-icon"><img src="images/logo.png" alt="UniTrack logo"></span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu"
                aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="toggler-icon"><i class="bi bi-list"></i></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-fill me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid-fill me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-person-lines-fill me-1"></i>About</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php"><i class="bi bi-envelope-fill me-1"></i>Contact</a></li>
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars((string) $username) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="account.php">
                                    <i class="bi bi-gear me-2"></i>Account Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="auth/logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">My Database Tasks</h1>
                <p class="text-muted mb-0">Welcome, <?= htmlspecialchars((string) $username) ?></p>
            </div>
            <div>
                <a class="btn btn-outline-light me-2" href="dashboard.php">Dashboard</a>
                <a class="btn btn-danger" href="auth/logout.php">Logout</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h2 class="h5 mb-3">Add Task</h2>
                    <form method="POST" action="my_tasks.php">
                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input class="form-control custom-input" type="text" id="title" name="title" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="deadline">Deadline</label>
                            <input class="form-control custom-input" type="date" id="deadline" name="deadline" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="priority">Priority</label>
                            <select class="form-select custom-input" id="priority" name="priority">
                                <option value="High">High</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <button class="btn btn-primary-grad" type="submit">Save Task</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-card p-4">
                    <h2 class="h5 mb-3">Saved Tasks</h2>
                    <?php if (count($tasks) === 0): ?>
                        <p class="text-muted mb-0">No tasks saved in database yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table custom-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Deadline</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tasks as $task): ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) $task['title']) ?></td>
                                            <td><?= $task['deadline'] ? htmlspecialchars((string) $task['deadline']) : '-' ?></td>
                                            <td><?= htmlspecialchars((string) $task['priority']) ?></td>
                                            <td>
                                                <?php if ((int) $task['is_done'] === 1): ?>
                                                    <span class="badge bg-success">Done</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-outline-light" href="my_tasks.php?action=toggle&id=<?= (int) $task['id'] ?>">Toggle</a>
                                                <a class="btn btn-sm btn-danger" href="my_tasks.php?action=delete&id=<?= (int) $task['id'] ?>" onclick="return confirm('Delete this task?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <a class="footer-brand" href="index.php">
                        <i class="bi bi-mortarboard-fill me-2"></i>Uni<span class="brand-accent">Track</span>
                    </a>
                    <p class="footer-desc mt-3">
                        A modern academic companion built for university students who want to
                        track tasks, timetables and GPA all in one place.
                    </p>
                </div>
                <div class="col-sm-6 col-lg-2 offset-lg-2">
                    <h6 class="footer-heading">Pages</h6>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="bi bi-chevron-right me-1"></i>Home</a></li>
                        <li><a href="dashboard.php"><i class="bi bi-chevron-right me-1"></i>Dashboard</a></li>
                        <li><a href="about.php"><i class="bi bi-chevron-right me-1"></i>About</a></li>
                        <li><a href="contact.php"><i class="bi bi-chevron-right me-1"></i>Contact</a></li>
                    </ul>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <h6 class="footer-heading">Modules</h6>
                    <ul class="footer-links">
                        <li><a href="dashboard.php#tasks"><i class="bi bi-chevron-right me-1"></i>Task Manager</a></li>
                        <li><a href="dashboard.php#timetable"><i class="bi bi-chevron-right me-1"></i>Timetable</a></li>
                        <li><a href="dashboard.php#gpa"><i class="bi bi-chevron-right me-1"></i>GPA Calc</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="footer-heading">Developer</h6>
                    <ul class="footer-links">
                        <li><a href="about.php"><i class="bi bi-person me-1"></i>About</a></li>
                        <li><a href="#"><i class="bi bi-github me-1"></i>GitHub</a></li>
                    </ul>
                </div>
            </div>

            <hr class="footer-divider my-4" />
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <p class="footer-copy mb-0">&copy; 2025 UniTrack. All rights reserved.</p>
                <p class="footer-copy mb-0">Built by <span style="text-decoration: underline;"><a href="https://uni-track-sigma.vercel.app/" target="_blank">Ashan Eranga</a></span></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/app.js"></script>
</body>

</html>
