<?php
require_once __DIR__ . '/includes/functions.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login('login.php');

$username = $_SESSION['username'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="UniTrack Dashboard – Manage tasks, timetable and GPA in one smart dashboard." />
    <title>Dashboard – UniTrack</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/dashboard.css" />
</head>

<body class="dash-body">



    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <span class="brand-icon me-2"><img src="images/logo.png" alt="UniTrack logo"></span>
            <span class="brand-text">Uni<span class="brand-accent">Track</span></span>
        </div>

        <button class="sidebar-toggle-btn" id="sidebarToggle" title="Collapse sidebar">
            <i class="bi bi-chevron-left" id="toggleIcon"></i>
        </button>

        <nav class="sidebar-nav mt-4" id="sidebarNav">
            <a href="index.php" class="sidebar-link">
                <i class="bi bi-house-fill"></i>
                <span class="link-text">Home</span>
            </a>
            <a href="#tasks" class="sidebar-link active smooth-scroll" id="navTasks">
                <i class="bi bi-list-task"></i>
                <span class="link-text">Tasks</span>
            </a>
            <a href="#timetable" class="sidebar-link smooth-scroll" id="navTimetable">
                <i class="bi bi-calendar-week"></i>
                <span class="link-text">Timetable</span>
            </a>
            <a href="#gpa" class="sidebar-link smooth-scroll" id="navGPA">
                <i class="bi bi-calculator"></i>
                <span class="link-text">GPA Calc</span>
            </a>
            <a href="about.php" class="sidebar-link">
                <i class="bi bi-person-lines-fill"></i>
                <span class="link-text">About</span>
            </a>
            <a href="contact.php" class="sidebar-link">
                <i class="bi bi-envelope-fill"></i>
                <span class="link-text">Contact</span>
            </a>
            <a href="auth/logout.php" class="sidebar-link">
                <i class="bi bi-box-arrow-left"></i>
                <span class="link-text">Logout</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-profile">
                <div class="profile-avatar"><i class="bi bi-person-circle"></i></div>
                <div class="profile-info">
                    <span class="profile-name link-text"><?= htmlspecialchars((string) $username) ?></span>
                    <span class="profile-role link-text">Undergraduate</span>
                </div>
            </div>
        </div>
    </aside>


    <!-- Main -->
    <div class="dash-main" id="dashMain">

        <header class="dash-topbar glass-nav">
            <button class="mobile-menu-btn d-lg-none" id="mobileSidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="topbar-title">Academic Dashboard</h1>
            <div class="topbar-right">
                <span class="topbar-date" id="topbarDate"></span>
                <a class="btn btn-sm btn-primary-grad ms-3" href="index.php">
                    <i class="bi bi-house me-1"></i>Home
                </a>
                <a class="btn btn-sm btn-outline-light ms-2" href="auth/logout.php">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </header>

        <!-- Summary-->
        <section class="dash-stats px-4 pt-4 pb-2">
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="stat-card glass-card d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box icon-purple"><i class="bi bi-list-task"></i></div>
                        <div>
                            <p class="stat-label mb-0">Total Tasks</p>
                            <h3 class="stat-val mb-0" id="statTotalTasks">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card glass-card d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box icon-green"><i class="bi bi-check2-all"></i></div>
                        <div>
                            <p class="stat-label mb-0">Completed</p>
                            <h3 class="stat-val mb-0" id="statCompleted">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card glass-card d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box icon-cyan"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <p class="stat-label mb-0">Lectures</p>
                            <h3 class="stat-val mb-0" id="statLectures">0</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card glass-card d-flex align-items-center gap-3 p-3">
                        <div class="stat-icon-box icon-orange"><i class="bi bi-graph-up-arrow"></i></div>
                        <div>
                            <p class="stat-label mb-0">Current GPA</p>
                            <h3 class="stat-val mb-0" id="statGPA">—</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Task Management -->
        <section class="dash-module px-4 py-4" id="tasks">
            <div class="module-header mb-4">
                <div class="module-title-wrap">
                    <div class="module-icon icon-purple"><i class="bi bi-list-task"></i></div>
                    <div>
                        <h2 class="module-title">Task Management</h2>
                        <p class="module-sub mb-0">Organise your assignments and deadlines</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Add Task -->
                <div class="col-lg-4">
                    <div class="glass-card p-4 h-100">
                        <h5 class="card-section-title mb-4">
                            <i class="bi bi-plus-circle me-2"></i>Add New Task
                        </h5>
                        <form id="taskForm" novalidate>
                            <div class="mb-3">
                                <label for="taskName" class="form-label">Task Name</label>
                                <input type="text" class="form-control custom-input" id="taskName"
                                    placeholder="e.g. Submit Assignment 2" required />
                                <div class="invalid-feedback">Please enter a task name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="taskDeadline" class="form-label">Deadline</label>
                                <input type="date" class="form-control custom-input" id="taskDeadline" required />
                                <div class="invalid-feedback">Please select a deadline.</div>
                            </div>
                            <div class="mb-4">
                                <label for="taskPriority" class="form-label">Priority</label>
                                <select class="form-select custom-input" id="taskPriority" required>
                                    <option value="" disabled selected>Select priority</option>
                                    <option value="High">🔴 High</option>
                                    <option value="Medium">🟡 Medium</option>
                                    <option value="Low">🟢 Low</option>
                                </select>
                                <div class="invalid-feedback">Please select a priority level.</div>
                            </div>
                            <button type="submit" class="btn btn-primary-grad w-100">
                                <i class="bi bi-plus-lg me-2"></i>Add Task
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Overall Progress</small>
                                <small class="fw-semibold" id="progressLabel">0 / 0 done</small>
                            </div>
                            <div class="progress custom-progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                    id="taskProgressBar" role="progressbar" style="width: 0%" aria-valuenow="0"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-center mt-2">
                                <small id="progressPercent" class="text-muted">0% complete</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task List -->
                <div class="col-lg-8">
                    <div class="glass-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-section-title mb-0">
                                <i class="bi bi-card-list me-2"></i>Task List
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-filter active" data-filter="all"
                                    id="filterAll">All</button>
                                <button class="btn btn-sm btn-filter" data-filter="pending"
                                    id="filterPending">Pending</button>
                                <button class="btn btn-sm btn-filter" data-filter="done" id="filterDone">Done</button>
                            </div>
                        </div>
                        <div id="taskEmpty" class="empty-state text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No tasks yet. Add your first task!</p>
                        </div>
                        <ul class="task-list list-unstyled mb-0" id="taskList"></ul>
                    </div>
                </div>
            </div>
        </section>


        <!-- Time Table -->
        <section class="dash-module px-4 py-4" id="timetable">
            <div class="module-header mb-4">
                <div class="module-title-wrap">
                    <div class="module-icon icon-cyan"><i class="bi bi-calendar-week"></i></div>
                    <div>
                        <h2 class="module-title">Lecture Timetable</h2>
                        <p class="module-sub mb-0">Your weekly class schedule at a glance</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="glass-card p-4 h-100">
                        <h5 class="card-section-title mb-4">
                            <i class="bi bi-plus-circle me-2"></i>Add Lecture
                        </h5>
                        <form id="lectureForm" novalidate>
                            <div class="mb-3">
                                <label for="lectureSubject" class="form-label">Subject</label>
                                <input type="text" class="form-control custom-input" id="lectureSubject"
                                    placeholder="e.g. Data Structures" required />
                                <div class="invalid-feedback">Please enter the subject name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="lectureDay" class="form-label">Day</label>
                                <select class="form-select custom-input" id="lectureDay" required>
                                    <option value="" disabled selected>Select day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                </select>
                                <div class="invalid-feedback">Please select a day.</div>
                            </div>
                            <div class="mb-4">
                                <label for="lectureStartTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control custom-input" id="lectureStartTime" required />
                                <div class="invalid-feedback">Please select a start time.</div>
                            </div>
                            <div class="mb-4">
                                <label for="lectureEndTime" class="form-label">End Time</label>
                                <input type="time" class="form-control custom-input" id="lectureEndTime" required />
                                <div class="invalid-feedback">Please select an end time.</div>
                            </div>
                            <button type="submit" class="btn btn-grad-cyan w-100">
                                <i class="bi bi-plus-lg me-2"></i>Add Lecture
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-9">
                    <div class="glass-card p-4 h-100 overflow-auto">
                        <h5 class="card-section-title mb-4">
                            <i class="bi bi-grid-3x3-gap me-2"></i>Weekly Schedule
                        </h5>
                        <div class="timetable-grid" id="timetableGrid">
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!--GPA Calc -->
        <section class="dash-module px-4 py-4 mb-5" id="gpa">
            <div class="module-header mb-4">
                <div class="module-title-wrap">
                    <div class="module-icon icon-orange"><i class="bi bi-calculator"></i></div>
                    <div>
                        <h2 class="module-title">GPA Calculator</h2>
                        <p class="module-sub mb-0">Calculate your weighted GPA automatically</p>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="glass-card p-4 h-100">
                        <h5 class="card-section-title mb-4">
                            <i class="bi bi-plus-circle me-2"></i>Add Subject
                        </h5>
                        <form id="gpaForm" novalidate>
                            <div class="mb-3">
                                <label for="subjectName" class="form-label">Subject Name</label>
                                <input type="text" class="form-control custom-input" id="subjectName"
                                    placeholder="e.g. Calculus III" required />
                                <div class="invalid-feedback">Please enter the subject name.</div>
                            </div>
                            <div class="mb-3">
                                <label for="subjectCredits" class="form-label">Credit Hours</label>
                                <input type="number" class="form-control custom-input" id="subjectCredits"
                                    placeholder="e.g. 3" min="1" max="6" required />
                                <div class="invalid-feedback">Enter credits between 1 and 6.</div>
                            </div>
                            <div class="mb-4">
                                <label for="subjectGrade" class="form-label">Grade</label>
                                <select class="form-select custom-input" id="subjectGrade" required>
                                    <option value="" disabled selected>Select grade</option>
                                    <option value="4.0">A+ (4.0)</option>
                                    <option value="4.0">A (4.0)</option>
                                    <option value="3.7">A- (3.7)</option>
                                    <option value="3.3">B+ (3.3)</option>
                                    <option value="3.0">B (3.0)</option>
                                    <option value="2.7">B- (2.7)</option>
                                    <option value="2.3">C+ (2.3)</option>
                                    <option value="2.0">C (2.0)</option>
                                    <option value="1.7">C- (1.7)</option>
                                    <option value="1.3">D+ (1.3)</option>
                                    <option value="1.0">D (1.0)</option>
                                    <option value="0.0">E (0.0)</option>
                                </select>
                                <div class="invalid-feedback">Please select a grade.</div>
                            </div>
                            <button type="submit" class="btn btn-grad-orange w-100">
                                <i class="bi bi-plus-lg me-2"></i>Add Subject
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                            <h6 class="text-muted small mb-3">Grade Scale Reference</h6>
                            <div class="grade-scale">
                                <div class="grade-row"><span class="grade-tag">A+</span><span>4.0</span></div>
                                <div class="grade-row"><span class="grade-tag">A</span><span>4.0</span></div>
                                <div class="grade-row"><span class="grade-tag">A-</span><span>3.7</span></div>
                                <div class="grade-row"><span class="grade-tag">B+</span><span>3.3</span></div>
                                <div class="grade-row"><span class="grade-tag">B</span><span>3.0</span></div>
                                <div class="grade-row"><span class="grade-tag">B-</span><span>2.7</span></div>
                                <div class="grade-row"><span class="grade-tag">C+</span><span>2.3</span></div>
                                <div class="grade-row"><span class="grade-tag">C</span><span>2.0</span></div>
                                <div class="grade-row"><span class="grade-tag">C-</span><span>1.7</span></div>
                                <div class="grade-row"><span class="grade-tag">D+</span><span>1.3</span></div>
                                <div class="grade-row"><span class="grade-tag">D</span><span>1.0</span></div>
                                <div class="grade-row"><span class="grade-tag grade-f">E</span><span>0.0</span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="gpa-score-card glass-card p-4 mb-4 d-flex align-items-center gap-4 flex-wrap"
                        id="gpaScoreCard">
                        <div class="gpa-circle" id="gpaCircle">
                            <span class="gpa-val" id="gpaDisplayVal">—</span>
                            <span class="gpa-out">/4.0</span>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="gpa-status-text mb-1" id="gpaStatusText">No subjects added yet</h4>
                            <p class="text-muted mb-2" id="gpaSummary">Add subjects to see your GPA</p>
                            <div class="gpa-bar-wrap">
                                <div class="gpa-progress-bar">
                                    <div class="gpa-progress-fill" id="gpaProgressFill" style="width:0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-section-title mb-0">
                                <i class="bi bi-table me-2"></i>Subjects
                            </h5>
                            <button class="btn btn-sm btn-danger-soft" id="clearGPABtn">
                                <i class="bi bi-trash me-1"></i>Clear All
                            </button>
                        </div>
                        <div id="gpaEmpty" class="empty-state text-center py-4">
                            <i class="bi bi-book fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No subjects added. Start adding above!</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table custom-table mb-0 d-none" id="gpaTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Subject</th>
                                        <th>Credits</th>
                                        <th>Grade</th>
                                        <th>Points</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="gpaTableBody"></tbody>
                                <tfoot>
                                    <tr class="table-summary-row">
                                        <td colspan="2"><strong>Total</strong></td>
                                        <td><strong id="totalCredits">0</strong></td>
                                        <td>—</td>
                                        <td><strong id="totalPoints">0.00</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--  Footer -->
        <div class="dash-footer px-4 pb-4 text-center">
            <p class="text-muted small mb-0">
                &copy; 2025 UniTrack &mdash; Smart Academic Dashboard
            </p>
        </div>

    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/dashboard.js?v=20260321b"></script>
</body>

</html>