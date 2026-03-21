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
    <meta name="description" content="UniTrack – Create your Smart Academic Dashboard account." />
    <title>UniTrack – Create Account</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/register.css" />
</head>

<body class="auth-body">


    <div class="auth-wrapper">

        <!--  Left  -->
        <div class="auth-image-panel" id="authImagePanelReg">
            <div class="auth-img-overlay"></div>

            <div class="auth-img-content">
                <a href="index.php" class="auth-brand">
                    <span class="brand-icon"><img src="images/logo.png" alt="UniTrack logo"></span>
                    Uni<span class="brand-accent">Track</span>
                </a>
                <div class="auth-img-tagline mt-auto">
                    <div class="section-badge mb-3">Join Thousands of Students</div>
                    <h2 class="auth-panel-title">
                        Start tracking<br />
                        <span class="gradient-text">smarter today</span>
                    </h2>
                    <p class="auth-panel-sub mt-3">
                        Create your free account and get instant access to task
                        management, interactive timetables and live GPA tracking.
                    </p>
                    <div class="auth-stats-row mt-5">
                        <div class="auth-stat-item">
                            <span class="auth-stat-num gradient-text">3</span>
                            <span class="auth-stat-label">Core Modules</span>
                        </div>
                        <div class="auth-stat-divider"></div>
                        <div class="auth-stat-item">
                            <span class="auth-stat-num gradient-text">∞</span>
                            <span class="auth-stat-label">Tasks &amp; Subjects</span>
                        </div>
                        <div class="auth-stat-divider"></div>
                        <div class="auth-stat-item">
                            <span class="auth-stat-num gradient-text">4.0</span>
                            <span class="auth-stat-label">Target GPA</span>
                        </div>
                    </div>
                    <div class="auth-steps mt-5">
                        <div class="auth-step">
                            <span class="auth-step-num">01</span>
                            <span class="auth-step-text">Create your free account</span>
                        </div>
                        <div class="auth-step">
                            <span class="auth-step-num">02</span>
                            <span class="auth-step-text">Set up your semester modules</span>
                        </div>
                        <div class="auth-step">
                            <span class="auth-step-num">03</span>
                            <span class="auth-step-text">Track and achieve your goals</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right -->
        <div class="auth-form-panel" id="authRegFormPanel">

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


                <div class="auth-form-header mb-4">
                    <div class="auth-form-icon mb-3" style="background: linear-gradient(135deg, #22d3ee, #0ea5e9);">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h1 class="auth-form-title">Create account</h1>
                    <p class="auth-form-sub">Join UniTrack and take control of your academics</p>
                </div>


                <!-- Register Form -->
                <form id="registerForm" action="auth/register.php" method="POST" novalidate>


                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <div class="auth-field-group">
                                <label class="auth-label" for="regFirstName">First Name</label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-person-fill auth-input-icon"></i>
                                    <input type="text" class="auth-input" id="regFirstName" placeholder="Ashan"
                                        autocomplete="given-name" name="first_name" required />
                                </div>
                                <div class="auth-error-msg" id="firstNameError"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="auth-field-group">
                                <label class="auth-label" for="regLastName">Last Name</label>
                                <div class="auth-input-wrap">
                                    <i class="bi bi-person-fill auth-input-icon"></i>
                                    <input type="text" class="auth-input" id="regLastName" placeholder="Eranga"
                                        autocomplete="family-name" name="last_name" required />
                                </div>
                                <div class="auth-error-msg" id="lastNameError"></div>
                            </div>
                        </div>
                    </div>

                    <div class="auth-field-group mb-3">
                        <label class="auth-label" for="regEmail">University Email</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-envelope-fill auth-input-icon"></i>
                            <input type="email" class="auth-input" id="regEmail" placeholder="you@university.edu"
                                autocomplete="email" name="email" required />
                        </div>
                        <div class="auth-error-msg" id="regEmailError"></div>
                    </div>

                    <div class="auth-field-group mb-3">
                        <label class="auth-label" for="regYear">Year of Study</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-mortarboard-fill auth-input-icon"></i>
                            <select class="auth-input auth-select" id="regYear" required>
                                <option value="" disabled selected>Select your year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                                <option value="pg">Postgraduate</option>
                            </select>
                        </div>
                        <div class="auth-error-msg" id="yearError"></div>
                    </div>

                    <div class="auth-field-group mb-3">
                        <label class="auth-label" for="regPassword">Password</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-shield-lock-fill auth-input-icon"></i>
                            <input type="password" class="auth-input" id="regPassword" placeholder="Min. 8 characters"
                                autocomplete="new-password" name="password" required />
                            <button type="button" class="auth-toggle-pw" id="toggleRegPw"
                                aria-label="Toggle password visibility">
                                <i class="bi bi-eye-fill" id="toggleRegPwIcon"></i>
                            </button>
                        </div>
                        <div class="auth-error-msg" id="regPasswordError"></div>
                        <div class="auth-strength-wrap mt-2" id="strengthWrap">
                            <div class="auth-strength-bar">
                                <div class="auth-strength-fill" id="strengthFill"></div>
                            </div>
                            <span class="auth-strength-label" id="strengthLabel">—</span>
                        </div>
                    </div>

                    <div class="auth-field-group mb-3">
                        <label class="auth-label" for="regConfirmPw">Confirm Password</label>
                        <div class="auth-input-wrap">
                            <i class="bi bi-shield-check auth-input-icon"></i>
                            <input type="password" class="auth-input" id="regConfirmPw"
                                placeholder="Repeat your password" autocomplete="new-password" required />
                            <button type="button" class="auth-toggle-pw" id="toggleConfirmPw"
                                aria-label="Toggle confirm password">
                                <i class="bi bi-eye-fill" id="toggleConfirmPwIcon"></i>
                            </button>
                        </div>
                        <div class="auth-error-msg" id="confirmPwError"></div>
                    </div>

                    <div class="mb-4">
                        <label class="auth-checkbox-wrap">
                            <input type="checkbox" id="agreeTerms" required />
                            <span class="auth-checkmark"></span>
                            <span class="auth-check-label">
                                I agree to the
                                <a href="#" class="auth-switch-link">Terms of Service</a>
                                and
                                <a href="#" class="auth-switch-link">Privacy Policy</a>
                            </span>
                        </label>
                        <div class="auth-error-msg" id="termsError"></div>
                    </div>

                    <button type="submit" class="auth-submit-btn auth-submit-btn--cyan w-100 mb-3"
                        id="registerSubmitBtn">
                        <span class="btn-text">
                            <i class="bi bi-rocket-takeoff me-2"></i>Create My Account
                        </span>
                        <span class="btn-loader d-none">
                            <span class="auth-spinner"></span> Creating account…
                        </span>
                    </button>

                    <p class="auth-switch-text text-center">
                        Already have an account?
                        <a href="login.php" class="auth-switch-link" id="goToLogin">Sign in</a>
                    </p>

                    <input type="hidden" name="username" id="usernameCombined" value="" />

                </form>

            </div>

            <p class="auth-footer-note">
                <i class="bi bi-shield-check me-1"></i>
                Free forever · No credit card required
            </p>

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/register.js"></script>
</body>

</html>