<?php
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

if (is_logged_in()) {
	header('Location: dashboard.php');
	exit;
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="description" content="UniTrack - Sign in to your smart academic dashboard." />
	<title>UniTrack - Login</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
		rel="stylesheet" />

	<link rel="stylesheet" href="css/login.css" />
</head>

<body class="auth-body">

	<div class="auth-wrapper">

		<div class="auth-image-panel" id="authImagePanelLogin">
			<div class="auth-img-overlay"></div>
			<div class="auth-img-content">
				<a href="index.php" class="auth-brand">
					<span class="brand-icon"><img src="images/logo.png" alt="UniTrack logo"></span>
					Uni<span class="brand-accent">Track</span>
				</a>

				<div class="mt-auto">
					<div class="section-badge mb-3">Welcome Back</div>
					<h2 class="auth-panel-title">
						Continue your<br />
						<span class="gradient-text">academic journey</span>
					</h2>
					<p class="auth-panel-sub mt-3 mb-4">
						Log in to manage tasks, review your timetable, and track GPA progress in one place.
					</p>

					<div class="auth-feature-pills">
						<span class="auth-pill"><i class="bi bi-check2-circle me-1"></i> Tasks</span>
						<span class="auth-pill"><i class="bi bi-check2-circle me-1"></i> Timetable</span>
						<span class="auth-pill"><i class="bi bi-check2-circle me-1"></i> GPA</span>
					</div>

					<div class="glass-card auth-testimonial mt-4 p-3">
						<div class="d-flex gap-3">
							<div class="auth-avatar-wrap"><i class="bi bi-person-circle"></i></div>
							<div>
								<p class="auth-quote mb-2">"UniTrack keeps my semester organized without any clutter."</p>
								<p class="auth-quote-author mb-0">- Student User</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="auth-form-panel" id="authLoginFormPanel">
			<a href="index.php" class="auth-brand auth-brand-mobile">
				<span class="brand-icon"><img src="images/logo.png" alt="UniTrack logo"></span>
				Uni<span class="brand-accent">Track</span>
			</a>

			<div class="auth-form-inner">
				<?php if ($flash): ?>
					<div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
						<?= htmlspecialchars($flash['message']) ?>
					</div>
				<?php endif; ?>

				<div class="mb-4">
					<div class="auth-form-icon mb-3">
						<i class="bi bi-box-arrow-in-right"></i>
					</div>
					<h1 class="auth-form-title">Sign in</h1>
					<p class="auth-form-sub">Welcome back. Please enter your details.</p>
				</div>




				<form id="loginForm" action="auth/login.php" method="POST" novalidate>
					<div class="auth-field-group mb-3">
						<label class="auth-label" for="loginEmail">Email</label>
						<div class="auth-input-wrap">
							<i class="bi bi-envelope-fill auth-input-icon"></i>
							<input type="email" class="auth-input" id="loginEmail" placeholder="you@university.edu"
								autocomplete="email" name="email" required />
						</div>
						<div class="auth-error-msg" id="emailError"></div>
					</div>

					<div class="auth-field-group mb-2">
						<label class="auth-label" for="loginPassword">Password</label>
						<div class="auth-input-wrap">
							<i class="bi bi-shield-lock-fill auth-input-icon"></i>
							<input type="password" class="auth-input" id="loginPassword" placeholder="Enter password"
								autocomplete="current-password" name="password" required />
							<button type="button" class="auth-toggle-pw" id="toggleLoginPw"
								aria-label="Toggle password visibility">
								<i class="bi bi-eye-fill" id="toggleLoginPwIcon"></i>
							</button>
						</div>
						<div class="auth-error-msg" id="passwordError"></div>
					</div>

					<div class="d-flex justify-content-end mb-4">
						<a href="#" class="auth-forgot">Forgot password?</a>
					</div>

					<button type="submit" class="auth-submit-btn w-100 mb-3" id="loginSubmitBtn">
						<span class="btn-text"><i class="bi bi-box-arrow-in-right me-2"></i>Sign in</span>
						<span class="btn-loader d-none"><span class="auth-spinner"></span> Signing in...</span>
					</button>

					<p class="auth-switch-text text-center mb-0">
						Don't have an account?
						<a href="register.php" class="auth-switch-link">Create one</a>
					</p>
				</form>
			</div>

			<p class="auth-footer-note">
				<i class="bi bi-shield-check me-1"></i>Secure session · Student-friendly
			</p>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="js/login.js"></script>
</body>

</html>
