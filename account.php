<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login('login.php');

$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitize_input($_POST['action'] ?? '');

    if ($action === 'update_profile') {
        $newName = sanitize_input($_POST['username'] ?? '');
        $newEmail = sanitize_input($_POST['email'] ?? '');

        if ($newName === '' || $newEmail === '') {
            redirect_with_message('account.php', 'danger', 'Name and email are required.');
        }

        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            redirect_with_message('account.php', 'danger', 'Please enter a valid email address.');
        }

        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
        $checkStmt->execute([
            'email' => $newEmail,
            'id' => $userId,
        ]);

        if ($checkStmt->fetch()) {
            redirect_with_message('account.php', 'warning', 'That email is already used by another account.');
        }

        $updateStmt = $pdo->prepare('UPDATE users SET username = :username, email = :email WHERE id = :id');
        $updateStmt->execute([
            'username' => $newName,
            'email' => $newEmail,
            'id' => $userId,
        ]);

        $_SESSION['username'] = $newName;
        $_SESSION['email'] = $newEmail;

        redirect_with_message('account.php', 'success', 'Profile updated successfully.');
    }

    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            redirect_with_message('account.php', 'danger', 'All password fields are required.');
        }

        if (strlen($newPassword) < 8) {
            redirect_with_message('account.php', 'danger', 'New password must be at least 8 characters.');
        }

        if ($newPassword !== $confirmPassword) {
            redirect_with_message('account.php', 'danger', 'New password and confirm password do not match.');
        }

        $userStmt = $pdo->prepare('SELECT password FROM users WHERE id = :id LIMIT 1');
        $userStmt->execute(['id' => $userId]);
        $user = $userStmt->fetch();

        if (!$user) {
            redirect_with_message('account.php', 'danger', 'User account not found. Please login again.');
        }

        $storedPassword = (string) $user['password'];

        // Support secure hashes and legacy plain-text rows safely.
        $isHashedPassword = str_starts_with($storedPassword, '$2y$')
            || str_starts_with($storedPassword, '$2a$')
            || str_starts_with($storedPassword, '$argon2');

        $isCurrentPasswordValid = $isHashedPassword
            ? password_verify($currentPassword, $storedPassword)
            : hash_equals($storedPassword, $currentPassword);

        if (!$isCurrentPasswordValid) {
            redirect_with_message('account.php', 'danger', 'Current password is incorrect.');
        }

        $isSameAsCurrent = $isHashedPassword
            ? password_verify($newPassword, $storedPassword)
            : hash_equals($storedPassword, $newPassword);

        if ($isSameAsCurrent) {
            redirect_with_message('account.php', 'warning', 'New password must be different from current password.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update only if the stored password still matches what we verified above.
        $passwordStmt = $pdo->prepare('UPDATE users SET password = :new_password WHERE id = :id AND password = :current_password');
        $passwordStmt->execute([
            'new_password' => $hashedPassword,
            'id' => $userId,
            'current_password' => $storedPassword,
        ]);

        if ($passwordStmt->rowCount() !== 1) {
            redirect_with_message('account.php', 'danger', 'Password update failed. Please try again.');
        }

        redirect_with_message('account.php', 'success', 'Password changed successfully.');
    }

    redirect_with_message('account.php', 'danger', 'Invalid account action.');
}

$username = $_SESSION['username'] ?? 'Student';
$email = $_SESSION['email'] ?? '';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="UniTrack account settings" />
    <title>Account Settings - UniTrack</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
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
                        <a class="nav-link dropdown-toggle d-flex align-items-center active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        </div>
    </nav>

    <section class="hero-section" style="min-height: 100vh; padding-top: 120px;">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-10">
                    <div class="text-center mb-4">
                        <div class="section-badge mb-3">Account Settings</div>
                        <h1 class="section-title">Manage Your <span class="gradient-text">UniTrack Profile</span></h1>
                        <p class="section-sub mt-2">Update your name, email, and password securely.</p>
                    </div>

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= htmlspecialchars((string) $flash['type']) ?> mb-4" role="alert">
                            <?= htmlspecialchars((string) $flash['message']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4 pb-5">
                        <div class="col-lg-6">
                            <div class="glass-card p-4 p-md-5 h-100">
                                <h2 class="card-section-title mb-1"><i class="bi bi-person-gear me-2"></i>Profile Details</h2>
                                <p class="text-muted small mb-4">These details appear in the navigation and dashboard.</p>

                                <form action="account.php" method="POST" novalidate>
                                    <input type="hidden" name="action" value="update_profile" />

                                    <div class="mb-3">
                                        <label class="form-label" for="accountName">Full Name</label>
                                        <input
                                            class="form-control custom-input"
                                            type="text"
                                            id="accountName"
                                            name="username"
                                            value="<?= htmlspecialchars((string) $username) ?>"
                                            required
                                        />
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label" for="accountEmail">Email Address</label>
                                        <input
                                            class="form-control custom-input"
                                            type="email"
                                            id="accountEmail"
                                            name="email"
                                            value="<?= htmlspecialchars((string) $email) ?>"
                                            required
                                        />
                                    </div>

                                    <button type="submit" class="btn btn-primary-grad">
                                        <i class="bi bi-check2-circle me-1"></i>Save Profile
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="glass-card p-4 p-md-5 h-100">
                                <h2 class="card-section-title mb-1"><i class="bi bi-shield-lock me-2"></i>Change Password</h2>
                                <p class="text-muted small mb-4">Use a strong password with at least 8 characters.</p>

                                <form action="account.php" method="POST" novalidate>
                                    <input type="hidden" name="action" value="change_password" />

                                    <div class="mb-3">
                                        <label class="form-label" for="currentPassword">Current Password</label>
                                        <input
                                            class="form-control custom-input"
                                            type="password"
                                            id="currentPassword"
                                            name="current_password"
                                            autocomplete="current-password"
                                            required
                                        />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="newPassword">New Password</label>
                                        <input
                                            class="form-control custom-input"
                                            type="password"
                                            id="newPassword"
                                            name="new_password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            required
                                        />
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label" for="confirmPassword">Confirm New Password</label>
                                        <input
                                            class="form-control custom-input"
                                            type="password"
                                            id="confirmPassword"
                                            name="confirm_password"
                                            autocomplete="new-password"
                                            minlength="8"
                                            required
                                        />
                                    </div>

                                    <button type="submit" class="btn btn-grad-cyan">
                                        <i class="bi bi-key me-1"></i>Update Password
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <a class="btn btn-primary-grad" href="dashboard.php"><i class="bi bi-grid-fill me-1"></i>Back to Dashboard</a>
                                <a class="btn btn-outline-custom" href="index.php"><i class="bi bi-house me-1"></i>Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
