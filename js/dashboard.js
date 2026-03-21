'use strict';

const $ = id => document.getElementById(id);

function updateTaskEmptyState() {
  const list = $('taskList');
  const empty = $('taskEmpty');
  if (!list || !empty) return;
  empty.classList.toggle('d-none', list.children.length > 0);
}

function updateGPAEmptyState(subjects) {
  const emptyEl = $('gpaEmpty');
  const tableEl = $('gpaTable');
  if (!emptyEl || !tableEl) return;
  if (subjects.length === 0) {
    emptyEl.classList.remove('d-none');
    tableEl.classList.add('d-none');
  } else {
    emptyEl.classList.add('d-none');
    tableEl.classList.remove('d-none');
  }
}

function daysUntil(dateStr) {
  if (!dateStr) return null;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const deadline = new Date(dateStr);
  deadline.setHours(0, 0, 0, 0);
  return Math.ceil((deadline - today) / 86400000);
}

function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(`${dateStr}T00:00:00`);
  return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function escapeHTML(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

async function apiRequest(url, method = 'GET', body = null) {
  const options = { method };

  if (body) {
    options.headers = {
      'Content-Type': 'application/x-www-form-urlencoded',
    };
    options.body = new URLSearchParams(body).toString();
  }

  const response = await fetch(url, options);
  const payload = await response.json().catch(() => ({ ok: false, message: 'Invalid server response.' }));

  if (!response.ok || payload.ok === false) {
    throw new Error(payload.message || 'Request failed.');
  }

  return payload;
}

(function initTopbarDate() {
  const el = $('topbarDate');
  if (!el) return;

  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const now = new Date();
  el.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
})();

(function initSidebar() {
  const sidebar = $('sidebar');
  const toggleBtn = $('sidebarToggle');
  const toggleIcon = $('toggleIcon');
  const mobileToggle = $('mobileSidebarToggle');
  const overlay = $('sidebarOverlay');

  if (!sidebar) return;

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      toggleIcon.className = sidebar.classList.contains('collapsed')
        ? 'bi bi-chevron-right'
        : 'bi bi-chevron-left';
    });
  }

  function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (mobileToggle) mobileToggle.addEventListener('click', openMobileSidebar);
  if (overlay) overlay.addEventListener('click', closeMobileSidebar);

  const sections = ['tasks', 'timetable', 'gpa'];
  const navLinks = {
    tasks: $('navTasks'),
    timetable: $('navTimetable'),
    gpa: $('navGPA'),
  };

  function updateActiveLink() {
    let current = 'tasks';
    sections.forEach(id => {
      const section = document.getElementById(id);
      if (!section) return;
      const rect = section.getBoundingClientRect();
      if (rect.top <= 120) current = id;
    });

    Object.entries(navLinks).forEach(([id, link]) => {
      if (!link) return;
      link.classList.toggle('active', id === current);
    });
  }

  window.addEventListener('scroll', updateActiveLink, { passive: true });

  sidebar.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', closeMobileSidebar);
  });
})();

(function initTaskManager() {
  let tasks = [];
  let filter = 'all';

  function updateStats() {
    const totalEl = $('statTotalTasks');
    const completedEl = $('statCompleted');
    if (totalEl) totalEl.textContent = String(tasks.length);
    if (completedEl) completedEl.textContent = String(tasks.filter(t => t.is_done === 1).length);
  }

  function updateProgress() {
    const bar = $('taskProgressBar');
    const label = $('progressLabel');
    const percent = $('progressPercent');
    if (!bar) return;

    const total = tasks.length;
    const done = tasks.filter(t => t.is_done === 1).length;
    const pct = total === 0 ? 0 : Math.round((done / total) * 100);

    bar.style.width = `${pct}%`;
    bar.setAttribute('aria-valuenow', String(pct));
    if (label) label.textContent = `${done} / ${total} done`;
    if (percent) percent.textContent = `${pct}% complete`;

    bar.className = 'progress-bar progress-bar-striped progress-bar-animated';
    if (pct === 100) {
      bar.style.background = 'linear-gradient(90deg,#34d399,#6ee7b7)';
    } else if (pct >= 50) {
      bar.style.background = 'linear-gradient(135deg,#7c5de8,#a27cff)';
    } else {
      bar.style.background = 'linear-gradient(90deg,#f97316,#fbbf24)';
    }
  }

  function renderTask(task) {
    const days = daysUntil(task.deadline);
    const isDone = Number(task.is_done) === 1;
    const isWarning = !isDone && days !== null && days <= 2;

    const li = document.createElement('li');
    li.className = `task-item${isDone ? ' completed' : ''}${isWarning ? ' deadline-warning' : ''}`;
    li.dataset.id = String(task.id);

    let warningHtml = '';
    if (isWarning && days >= 0) {
      warningHtml = `<span class="task-deadline-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i>${days === 0 ? 'Due today!' : `Due in ${days} day${days === 1 ? '' : 's'}!`}</span>`;
    } else if (!isDone && days !== null && days < 0) {
      warningHtml = '<span class="task-deadline-warning"><i class="bi bi-x-circle me-1"></i>Overdue</span>';
    }

    const priorityClass = {
      High: 'priority-high',
      Medium: 'priority-medium',
      Low: 'priority-low',
    }[task.priority] || 'priority-medium';

    li.innerHTML = `
      <div class="task-checkbox${isDone ? ' checked' : ''}" data-id="${task.id}" role="checkbox" aria-checked="${isDone}" aria-label="Mark task complete" tabindex="0"></div>
      <div class="task-info">
        <div class="task-name">${escapeHTML(task.title)}</div>
        <div class="task-meta">
          <span>Due: ${formatDate(task.deadline)}</span>
          <span class="priority-badge ${priorityClass}">${escapeHTML(task.priority)}</span>
          ${warningHtml}
        </div>
      </div>
      <button class="task-delete-btn" data-id="${task.id}" aria-label="Delete task" title="Delete">
        <i class="bi bi-trash3"></i>
      </button>
    `;

    return li;
  }

  function renderAllTasks() {
    const list = $('taskList');
    if (!list) return;

    list.innerHTML = '';

    const filtered = tasks.filter(task => {
      if (filter === 'pending') return Number(task.is_done) !== 1;
      if (filter === 'done') return Number(task.is_done) === 1;
      return true;
    });

    filtered.forEach(task => list.appendChild(renderTask(task)));
    updateTaskEmptyState();
    updateProgress();
    updateStats();
  }

  async function loadTasks() {
    try {
      const payload = await apiRequest('api/tasks.php');
      tasks = payload.data || [];
      renderAllTasks();
    } catch (error) {
      console.error(error);
    }
  }

  const form = $('taskForm');
  if (form) {
    const deadlineInput = $('taskDeadline');
    if (deadlineInput) {
      deadlineInput.setAttribute('min', new Date().toISOString().split('T')[0]);
    }

    form.addEventListener('submit', async event => {
      event.preventDefault();
      form.classList.add('was-validated');

      const nameEl = $('taskName');
      const deadlineEl = $('taskDeadline');
      const priorityEl = $('taskPriority');

      if (!nameEl.value.trim() || !deadlineEl.value || !priorityEl.value) return;

      try {
        await apiRequest('api/tasks.php', 'POST', {
          action: 'create',
          title: nameEl.value.trim(),
          deadline: deadlineEl.value,
          priority: priorityEl.value,
        });

        form.reset();
        form.classList.remove('was-validated');
        await loadTasks();
      } catch (error) {
        alert(error.message);
      }
    });
  }

  const taskListEl = $('taskList');
  if (taskListEl) {
    taskListEl.addEventListener('click', async event => {
      const checkbox = event.target.closest('.task-checkbox');
      if (checkbox) {
        try {
          await apiRequest('api/tasks.php', 'POST', {
            action: 'toggle',
            id: checkbox.dataset.id,
          });
          await loadTasks();
        } catch (error) {
          alert(error.message);
        }
      }

      const deleteBtn = event.target.closest('.task-delete-btn');
      if (deleteBtn) {
        try {
          await apiRequest('api/tasks.php', 'POST', {
            action: 'delete',
            id: deleteBtn.dataset.id,
          });
          await loadTasks();
        } catch (error) {
          alert(error.message);
        }
      }
    });

    taskListEl.addEventListener('keydown', event => {
      if (event.key === 'Enter' || event.key === ' ') {
        const checkbox = event.target.closest('.task-checkbox');
        if (checkbox) {
          event.preventDefault();
          checkbox.click();
        }
      }
    });
  }

  ['filterAll', 'filterPending', 'filterDone'].forEach(buttonId => {
    const button = $(buttonId);
    if (!button) return;
    button.addEventListener('click', () => {
      ['filterAll', 'filterPending', 'filterDone'].forEach(id => {
        const target = $(id);
        if (target) target.classList.remove('active');
      });
      button.classList.add('active');
      filter = button.dataset.filter;
      renderAllTasks();
    });
  });

  loadTasks();
})();

(function initTimetable() {
  let lectures = [];
  const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
  const TODAY_INDEX = (new Date().getDay() + 6) % 7;

  function updateLectureStats() {
    const el = $('statLectures');
    if (el) el.textContent = String(lectures.length);
  }

  function formatTime(time24) {
    const [h, m] = time24.split(':').map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour = h % 12 || 12;
    return `${hour}:${String(m).padStart(2, '0')} ${ampm}`;
  }

  function formatTimeRange(startTime, endTime) {
    return `${formatTime(startTime)} - ${formatTime(endTime)}`;
  }

  function renderTimetable() {
    const grid = $('timetableGrid');
    if (!grid) return;
    grid.innerHTML = '';

    DAYS.forEach((day, i) => {
      const col = document.createElement('div');
      col.className = 'tt-column';

      const header = document.createElement('div');
      header.className = `tt-day-header${i === TODAY_INDEX ? ' today' : ''}`;
      header.textContent = day.slice(0, 3).toUpperCase();
      col.appendChild(header);

      const dayLectures = lectures.filter(item => item.day_name === day);

      if (dayLectures.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'tt-empty-slot';
        empty.textContent = '-';
        col.appendChild(empty);
      } else {
        dayLectures.forEach(lecture => {
          const slot = document.createElement('div');
          slot.className = 'tt-slot';
          slot.innerHTML = `
            <span class="tt-subject">${escapeHTML(lecture.subject)}</span>
            <span class="tt-time"><i class="bi bi-clock me-1"></i>${formatTimeRange(String(lecture.start_time).slice(0, 5), String(lecture.end_time).slice(0, 5))}</span>
            <button class="tt-delete" data-id="${lecture.id}" aria-label="Remove lecture" title="Remove">
              <i class="bi bi-x-lg"></i>
            </button>
          `;
          col.appendChild(slot);
        });
      }

      grid.appendChild(col);
    });

    updateLectureStats();
  }

  async function loadLectures() {
    try {
      const payload = await apiRequest('api/lectures.php');
      lectures = payload.data || [];
      renderTimetable();
    } catch (error) {
      console.error(error);
      alert(`Lecture data load failed: ${error.message}`);
    }
  }

  const lectureForm = $('lectureForm');
  if (lectureForm) {
    lectureForm.addEventListener('submit', async event => {
      event.preventDefault();
      lectureForm.classList.add('was-validated');

      const subjectEl = $('lectureSubject');
      const dayEl = $('lectureDay');
      const startTimeEl = $('lectureStartTime');
      const endTimeEl = $('lectureEndTime');

      if (!subjectEl.value.trim() || !dayEl.value || !startTimeEl.value || !endTimeEl.value) return;

      if (endTimeEl.value <= startTimeEl.value) {
        alert('End time must be after start time.');
        return;
      }

      try {
        await apiRequest('api/lectures.php', 'POST', {
          action: 'create',
          subject: subjectEl.value.trim(),
          day_name: dayEl.value,
          start_time: startTimeEl.value,
          end_time: endTimeEl.value,
        });

        lectureForm.reset();
        lectureForm.classList.remove('was-validated');
        await loadLectures();
      } catch (error) {
        alert(error.message);
      }
    });
  }

  const grid = $('timetableGrid');
  if (grid) {
    grid.addEventListener('click', async event => {
      const btn = event.target.closest('.tt-delete');
      if (!btn) return;

      try {
        await apiRequest('api/lectures.php', 'POST', {
          action: 'delete',
          id: btn.dataset.id,
        });
        await loadLectures();
      } catch (error) {
        alert(error.message);
      }
    });
  }

  loadLectures();
})();

(function initGPACalc() {
  let subjects = [];

  const gradeLabels = {
    '4': 'A',
    '4.0': 'A',
    '3.7': 'A-',
    '3.3': 'B+',
    '3': 'B',
    '3.0': 'B',
    '2.7': 'B-',
    '2.3': 'C+',
    '2': 'C',
    '2.0': 'C',
    '1.7': 'C-',
    '1.3': 'D+',
    '1': 'D',
    '1.0': 'D',
    '0': 'E',
    '0.0': 'E',
  };

  function computeGPA() {
    if (subjects.length === 0) return null;

    const totalCredits = subjects.reduce((sum, sub) => sum + Number(sub.credits), 0);
    const weightedSum = subjects.reduce((sum, sub) => sum + Number(sub.credits) * Number(sub.grade_point), 0);

    if (totalCredits === 0) return 0;
    return weightedSum / totalCredits;
  }

  function updateGPADisplay() {
    const gpa = computeGPA();
    const circle = $('gpaCircle');
    const valEl = $('gpaDisplayVal');
    const statusEl = $('gpaStatusText');
    const summaryEl = $('gpaSummary');
    const fillEl = $('gpaProgressFill');
    const statEl = $('statGPA');

    if (gpa === null) {
      if (valEl) valEl.textContent = '-';
      if (statusEl) statusEl.textContent = 'No subjects added yet';
      if (summaryEl) summaryEl.textContent = 'Add subjects to see your GPA';
      if (circle) circle.className = 'gpa-circle';
      if (fillEl) fillEl.style.width = '0%';
      if (statEl) statEl.textContent = '-';
      return;
    }

    const gpaDisplay = gpa.toFixed(2);
    if (valEl) valEl.textContent = gpaDisplay;
    if (statEl) statEl.textContent = gpaDisplay;

    let colorClass = '';
    let statusText = '';

    if (gpa >= 3.5) {
      colorClass = 'gpa-high';
      statusText = 'Excellent Standing';
    } else if (gpa >= 2.5) {
      colorClass = 'gpa-mid';
      statusText = 'Good Standing';
    } else {
      colorClass = 'gpa-low';
      statusText = 'Needs Improvement';
    }

    if (circle) circle.className = `gpa-circle ${colorClass}`;
    if (statusEl) statusEl.textContent = statusText;

    const totalCredits = subjects.reduce((sum, sub) => sum + Number(sub.credits), 0);
    if (summaryEl) {
      summaryEl.textContent = `${subjects.length} subject${subjects.length > 1 ? 's' : ''} . ${totalCredits} credit${totalCredits > 1 ? 's' : ''}`;
    }

    if (fillEl) fillEl.style.width = `${((gpa / 4) * 100).toFixed(1)}%`;
  }

  function renderSubjectTable() {
    const tbody = $('gpaTableBody');
    const totalC = $('totalCredits');
    const totalP = $('totalPoints');
    if (!tbody) return;

    tbody.innerHTML = '';

    const totalCredits = subjects.reduce((sum, sub) => sum + Number(sub.credits), 0);
    const weightedSum = subjects.reduce((sum, sub) => sum + Number(sub.credits) * Number(sub.grade_point), 0);

    subjects.forEach((subject, i) => {
      const gradePointLabel = String(Number(subject.grade_point));
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-muted">${i + 1}</td>
        <td>${escapeHTML(subject.subject_name)}</td>
        <td>${Number(subject.credits)}</td>
        <td><span class="grade-tag">${gradeLabels[gradePointLabel] || gradePointLabel}</span></td>
        <td>${(Number(subject.credits) * Number(subject.grade_point)).toFixed(2)}</td>
        <td>
          <button class="task-delete-btn gpa-delete-btn" data-id="${subject.id}" aria-label="Remove subject" title="Remove">
            <i class="bi bi-trash3"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    if (totalC) totalC.textContent = String(totalCredits);
    if (totalP) totalP.textContent = weightedSum.toFixed(2);

    updateGPAEmptyState(subjects);
    updateGPADisplay();
  }

  async function loadSubjects() {
    try {
      const payload = await apiRequest('api/gpa.php');
      subjects = payload.data || [];
      renderSubjectTable();
    } catch (error) {
      console.error(error);
      alert(`GPA data load failed: ${error.message}`);
    }
  }

  const gpaForm = $('gpaForm');
  if (gpaForm) {
    gpaForm.addEventListener('submit', async event => {
      event.preventDefault();
      gpaForm.classList.add('was-validated');

      const nameEl = $('subjectName');
      const creditsEl = $('subjectCredits');
      const gradeEl = $('subjectGrade');
      const credits = parseInt(creditsEl.value, 10);

      if (!nameEl.value.trim() || !creditsEl.value || !gradeEl.value) return;
      if (credits < 1 || credits > 6) {
        creditsEl.setCustomValidity('invalid');
        return;
      }
      creditsEl.setCustomValidity('');

      try {
        await apiRequest('api/gpa.php', 'POST', {
          action: 'create',
          subject_name: nameEl.value.trim(),
          credits: String(credits),
          grade_point: gradeEl.value,
        });

        gpaForm.reset();
        gpaForm.classList.remove('was-validated');
        await loadSubjects();
      } catch (error) {
        alert(error.message);
      }
    });
  }

  const gpaTableEl = $('gpaTable');
  const gpaEmptyEl = $('gpaEmpty');

  [gpaTableEl, gpaEmptyEl].forEach(parent => {
    if (!parent) return;
    parent.addEventListener('click', async event => {
      const btn = event.target.closest('.gpa-delete-btn');
      if (!btn) return;

      try {
        await apiRequest('api/gpa.php', 'POST', {
          action: 'delete',
          id: btn.dataset.id,
        });
        await loadSubjects();
      } catch (error) {
        alert(error.message);
      }
    });
  });

  const clearBtn = $('clearGPABtn');
  if (clearBtn) {
    clearBtn.addEventListener('click', async () => {
      if (subjects.length === 0) return;
      if (!window.confirm('Remove all subjects and reset GPA?')) return;

      try {
        await apiRequest('api/gpa.php', 'POST', { action: 'clear' });
        await loadSubjects();
      } catch (error) {
        alert(error.message);
      }
    });
  }

  loadSubjects();
})();
