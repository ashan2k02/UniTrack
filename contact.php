<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        redirect_with_message('contact.php', 'danger', 'Please fill all contact fields.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect_with_message('contact.php', 'danger', 'Please enter a valid email address.');
    }

    $stmt = $pdo->prepare('INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'message' => $message,
    ]);

    redirect_with_message('contact.php', 'success', 'Message sent successfully. Thank you!');
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact - UniTrack</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
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
                    <li class="nav-item"><a class="nav-link active" href="contact.php"><i class="bi bi-envelope-fill me-1"></i>Contact</a></li>
                    <?php if ($isLoggedIn = is_logged_in()): ?>
                        <li class="nav-item dropdown ms-lg-2">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars((string) ($_SESSION['username'] ?? 'Student')) ?>
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
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
                        <li class="nav-item ms-lg-2"><a class="btn btn-primary-grad" href="register.php">Get Started <i class="bi bi-arrow-right ms-1"></i></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section" style="min-height: 100vh; padding-top: 120px;">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="glass-card p-4 p-md-5">
                        <div class="section-badge mb-2">Contact UniTrack</div>
                        <h1 class="section-title mb-3">Send Us a Message</h1>

                        <?php if ($flash): ?>
                            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
                                <?= htmlspecialchars($flash['message']) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="contact.php" novalidate>
                            <div class="mb-3">
                                <label class="form-label" for="contactName">Name</label>
                                <input class="form-control custom-input" type="text" id="contactName" name="name" required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="contactEmail">Email</label>
                                <input class="form-control custom-input" type="email" id="contactEmail" name="email" required />
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="contactMessage">Message</label>
                                <textarea class="form-control custom-input" id="contactMessage" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary-grad">
                                <i class="bi bi-send me-2"></i>Send Message
                            </button>
                        </form>
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
