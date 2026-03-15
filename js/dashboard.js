'use strict';




const $ = id => document.getElementById(id);


function updateTaskEmptyState() {
  const list  = $('taskList');
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
  const today    = new Date();
  today.setHours(0, 0, 0, 0);
  const deadline = new Date(dateStr);
  deadline.setHours(0, 0, 0, 0);
  return Math.ceil((deadline - today) / 86_400_000);
}


function formatDate(dateStr) {
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}


function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim());
}


function uid() {
  return '_' + Math.random().toString(36).slice(2, 9);
}



(function initNavbarScroll() {
  const navbar = document.getElementById('mainNavbar');
  if (!navbar) return;

  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.style.background = 'rgba(11,13,26,0.95)';
    } else {
      navbar.style.background = 'rgba(11,13,26,0.75)';
    }
  }, { passive: true });
})();



(function initSmoothScroll() {
  document.addEventListener('click', e => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;
    const targetId = link.getAttribute('href').slice(1);
    if (!targetId) return;

    const target = document.getElementById(targetId);
    if (target) {
      e.preventDefault();
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
})();



(function initReveal() {
  const targets = document.querySelectorAll('.feature-card, .stat-chip, .cta-card');
  targets.forEach(el => el.classList.add('reveal'));

  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
        }
      });
    },
    { threshold: 0.12 }
  );

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
})();



(function initTopbarDate() {
  const el = $('topbarDate');
  if (!el) return;

  const days      = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months    = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const now       = new Date();
  const dayName   = days[now.getDay()];
  const monthName = months[now.getMonth()];
  el.textContent  = `${dayName}, ${now.getDate()} ${monthName} ${now.getFullYear()}`;
})();



(function initSidebar() {
  const sidebar       = $('sidebar');
  const dashMain      = $('dashMain');
  const toggleBtn     = $('sidebarToggle');
  const toggleIcon    = $('toggleIcon');
  const mobileToggle  = $('mobileSidebarToggle');
  const overlay       = $('sidebarOverlay');

  if (!sidebar) return;

  
  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      if (sidebar.classList.contains('collapsed')) {
        toggleIcon.className = 'bi bi-chevron-right';
      } else {
        toggleIcon.className = 'bi bi-chevron-left';
      }
    });
  }

  
  function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
  function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (mobileToggle) mobileToggle.addEventListener('click', openMobileSidebar);
  if (overlay)      overlay.addEventListener('click', closeMobileSidebar);

  
  const sections = ['tasks', 'timetable', 'gpa'];
  const navLinks  = {
    tasks:     $('navTasks'),
    timetable: $('navTimetable'),
    gpa:       $('navGPA'),
  };

  function updateActiveLink() {
    let current = '';
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
  
  let tasks = JSON.parse(localStorage.getItem('ut_tasks') || '[]');
  let filter = 'all'; 

  
  function saveTasks() {
    localStorage.setItem('ut_tasks', JSON.stringify(tasks));
  }

  
  function updateStats() {
    const totalEl    = $('statTotalTasks');
    const completedEl = $('statCompleted');
    if (totalEl)    totalEl.textContent    = tasks.length;
    if (completedEl) completedEl.textContent = tasks.filter(t => t.done).length;
  }

  
  function updateProgress() {
    const bar     = $('taskProgressBar');
    const label   = $('progressLabel');
    const percent = $('progressPercent');
    if (!bar) return;

    const total   = tasks.length;
    const done    = tasks.filter(t => t.done).length;
    const pct     = total === 0 ? 0 : Math.round((done / total) * 100);

    bar.style.width     = pct + '%';
    bar.setAttribute('aria-valuenow', pct);
    if (label)   label.textContent   = `${done} / ${total} done`;
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
    const isWarning = !task.done && days <= 2;

    const li = document.createElement('li');
    li.className = `task-item${task.done ? ' completed' : ''}${isWarning ? ' deadline-warning' : ''}`;
    li.dataset.id = task.id;

    let deadlineMeta = `Due: ${formatDate(task.deadline)}`;
    let warningHtml  = '';
    if (isWarning && days >= 0) {
      warningHtml = `<span class="task-deadline-warning">
                       <i class="bi bi-exclamation-triangle-fill me-1"></i>
                       ${days === 0 ? 'Due today!' : `Due in ${days} day${days === 1 ? '' : 's'}!`}
                     </span>`;
    } else if (days < 0 && !task.done) {
      warningHtml = `<span class="task-deadline-warning"><i class="bi bi-x-circle me-1"></i>Overdue</span>`;
    }

    const pClass = {
      High:   'priority-high',
      Medium: 'priority-medium',
      Low:    'priority-low',
    }[task.priority] || 'priority-medium';

    li.innerHTML = `
      <div class="task-checkbox${task.done ? ' checked' : ''}" data-id="${task.id}" role="checkbox"
           aria-checked="${task.done}" aria-label="Mark task complete" tabindex="0"></div>
      <div class="task-info">
        <div class="task-name">${escapeHTML(task.name)}</div>
        <div class="task-meta">
          <span>${deadlineMeta}</span>
          <span class="priority-badge ${pClass}">${task.priority}</span>
          ${warningHtml}
        </div>
      </div>
      <button class="task-delete-btn" data-id="${task.id}" aria-label="Delete task" title="Delete">
        <i class="bi bi-trash3"></i>
      </button>
    `;
    return li;
  }

  
  function escapeHTML(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  
  function renderAllTasks() {
    const list = $('taskList');
    if (!list) return;
    list.innerHTML = '';

    const filtered = tasks.filter(t => {
      if (filter === 'pending') return !t.done;
      if (filter === 'done')    return t.done;
      return true;
    });

    filtered.forEach(t => list.appendChild(renderTask(t)));
    updateTaskEmptyState();
    updateProgress();
    updateStats();
  }

  
  const form = $('taskForm');
  if (form) {
    const deadlineInput = $('taskDeadline');
    if (deadlineInput) {
      const today = new Date().toISOString().split('T')[0];
      deadlineInput.setAttribute('min', today);
    }

    form.addEventListener('submit', e => {
      e.preventDefault();
      form.classList.add('was-validated');

      const nameEl     = $('taskName');
      const deadlineEl = $('taskDeadline');
      const priorityEl = $('taskPriority');

      if (!nameEl.value.trim() || !deadlineEl.value || !priorityEl.value) return;

      const newTask = {
        id:       uid(),
        name:     nameEl.value.trim(),
        deadline: deadlineEl.value,
        priority: priorityEl.value,
        done:     false,
      };

      tasks.unshift(newTask);
      saveTasks();
      renderAllTasks();

      form.reset();
      form.classList.remove('was-validated');
    });
  }

  
  const taskListEl = $('taskList');
  if (taskListEl) {
    taskListEl.addEventListener('click', e => {
      const checkbox = e.target.closest('.task-checkbox');
      if (checkbox) {
        const id   = checkbox.dataset.id;
        const task = tasks.find(t => t.id === id);
        if (task) {
          task.done = !task.done;
          saveTasks();
          renderAllTasks();
        }
      }

      const deleteBtn = e.target.closest('.task-delete-btn');
      if (deleteBtn) {
        const id = deleteBtn.dataset.id;
        tasks = tasks.filter(t => t.id !== id);
        saveTasks();
        renderAllTasks();
      }
    });

    taskListEl.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ' ') {
        const checkbox = e.target.closest('.task-checkbox');
        if (checkbox) {
          e.preventDefault();
          checkbox.click();
        }
      }
    });
  }

  
  ['filterAll', 'filterPending', 'filterDone'].forEach(btnId => {
    const btn = $(btnId);
    if (!btn) return;
    btn.addEventListener('click', () => {
      ['filterAll', 'filterPending', 'filterDone'].forEach(id => {
        const b = $(id);
        if (b) b.classList.remove('active');
      });
      btn.classList.add('active');
      filter = btn.dataset.filter;
      renderAllTasks();
    });
  });

  
  renderAllTasks();
})();



(function initTimetable() {
  
  let lectures = JSON.parse(localStorage.getItem('ut_lectures') || '[]');
  const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
  const TODAY_INDEX = (new Date().getDay() + 6) % 7; 

  
  function saveLectures() {
    localStorage.setItem('ut_lectures', JSON.stringify(lectures));
  }

  
  function updateLectureStats() {
    const el = $('statLectures');
    if (el) el.textContent = lectures.length;
  }

  
  function formatTime(time24) {
    const [h, m] = time24.split(':').map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hour  = h % 12 || 12;
    return `${hour}:${String(m).padStart(2,'0')} ${ampm}`;
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

      const dayLectures = lectures
        .filter(l => l.day === day)
        .sort((a, b) => a.time.localeCompare(b.time));

      if (dayLectures.length === 0) {
        const empty = document.createElement('div');
        empty.className = 'tt-empty-slot';
        empty.textContent = '—';
        col.appendChild(empty);
      } else {
        dayLectures.forEach(lec => {
          const slot = document.createElement('div');
          slot.className = 'tt-slot';
          slot.innerHTML = `
            <span class="tt-subject">${escapeHTMLTT(lec.subject)}</span>
            <span class="tt-time"><i class="bi bi-clock me-1"></i>${formatTime(lec.time)}</span>
            <button class="tt-delete" data-id="${lec.id}" aria-label="Remove lecture" title="Remove">
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

  function escapeHTMLTT(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  
  const lectureForm = $('lectureForm');
  if (lectureForm) {
    lectureForm.addEventListener('submit', e => {
      e.preventDefault();
      lectureForm.classList.add('was-validated');

      const subjectEl = $('lectureSubject');
      const dayEl     = $('lectureDay');
      const timeEl    = $('lectureTime');

      if (!subjectEl.value.trim() || !dayEl.value || !timeEl.value) return;

      lectures.push({
        id:      uid(),
        subject: subjectEl.value.trim(),
        day:     dayEl.value,
        time:    timeEl.value,
      });

      saveLectures();
      renderTimetable();

      lectureForm.reset();
      lectureForm.classList.remove('was-validated');
    });
  }

  
  const grid = $('timetableGrid');
  if (grid) {
    grid.addEventListener('click', e => {
      const btn = e.target.closest('.tt-delete');
      if (!btn) return;
      const id = btn.dataset.id;
      lectures = lectures.filter(l => l.id !== id);
      saveLectures();
      renderTimetable();
    });
  }

  
  renderTimetable();
})();



(function initGPACalc() {
  
  let subjects = JSON.parse(localStorage.getItem('ut_subjects') || '[]');

  
  const gradeLabels = {
    '4.0': 'A',
    '3.7': 'A-',
    '3.3': 'B+',
    '3.0': 'B',
    '2.0': 'C',
    '0':   'F',
  };

  
  function saveSubjects() {
    localStorage.setItem('ut_subjects', JSON.stringify(subjects));
  }

  
  function computeGPA() {
    if (subjects.length === 0) return null;
    const totalCredits = subjects.reduce((s, sub) => s + sub.credits, 0);
    const weightedSum  = subjects.reduce((s, sub) => s + sub.credits * sub.gradePoint, 0);
    return totalCredits === 0 ? 0 : weightedSum / totalCredits;
  }

  
  function updateGPADisplay() {
    const gpa = computeGPA();
    const circle    = $('gpaCircle');
    const valEl     = $('gpaDisplayVal');
    const statusEl  = $('gpaStatusText');
    const summaryEl = $('gpaSummary');
    const fillEl    = $('gpaProgressFill');
    const statEl    = $('statGPA');

    if (gpa === null) {
      if (valEl)    valEl.textContent = '—';
      if (statusEl) statusEl.textContent = 'No subjects added yet';
      if (summaryEl) summaryEl.textContent = 'Add subjects to see your GPA';
      if (circle)   circle.className = 'gpa-circle';
      if (fillEl)   fillEl.style.width = '0%';
      if (statEl)   statEl.textContent = '—';
      return;
    }

    const gpaDisplay = gpa.toFixed(2);
    if (valEl) valEl.textContent = gpaDisplay;
    if (statEl) statEl.textContent = gpaDisplay;

    let colorClass = '', statusText = '';
    if (gpa >= 3.5) {
      colorClass = 'gpa-high';
      statusText = '🎉 Excellent Standing!';
    } else if (gpa >= 2.5) {
      colorClass = 'gpa-mid';
      statusText = '📈 Good Standing';
    } else {
      colorClass = 'gpa-low';
      statusText = '⚠️ Needs Improvement';
    }

    if (circle) circle.className = `gpa-circle ${colorClass}`;
    if (statusEl) statusEl.textContent = statusText;

    const totalCredits = subjects.reduce((s, sub) => s + sub.credits, 0);
    if (summaryEl) summaryEl.textContent =
      `${subjects.length} subject${subjects.length > 1 ? 's' : ''} · ${totalCredits} credit${totalCredits > 1 ? 's' : ''}`;

    if (fillEl) fillEl.style.width = ((gpa / 4) * 100).toFixed(1) + '%';
  }

  
  function renderSubjectTable() {
    const tbody      = $('gpaTableBody');
    const totalC     = $('totalCredits');
    const totalP     = $('totalPoints');
    if (!tbody) return;

    tbody.innerHTML = '';

    const totalCredits = subjects.reduce((s, sub) => s + sub.credits, 0);
    const weightedSum  = subjects.reduce((s, sub) => s + sub.credits * sub.gradePoint, 0);

    subjects.forEach((sub, i) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-muted">${i + 1}</td>
        <td>${escapeHTMLGPA(sub.name)}</td>
        <td>${sub.credits}</td>
        <td><span class="grade-tag">${gradeLabels[String(sub.gradePoint)] || sub.gradePoint}</span></td>
        <td>${(sub.credits * sub.gradePoint).toFixed(2)}</td>
        <td>
          <button class="task-delete-btn gpa-delete-btn" data-id="${sub.id}" aria-label="Remove subject" title="Remove">
            <i class="bi bi-trash3"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    if (totalC) totalC.textContent = totalCredits;
    if (totalP) totalP.textContent = weightedSum.toFixed(2);

    updateGPAEmptyState(subjects);
    updateGPADisplay();
  }

  function escapeHTMLGPA(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  
  const gpaForm = $('gpaForm');
  if (gpaForm) {
    gpaForm.addEventListener('submit', e => {
      e.preventDefault();
      gpaForm.classList.add('was-validated');

      const nameEl    = $('subjectName');
      const creditsEl = $('subjectCredits');
      const gradeEl   = $('subjectGrade');

      const credits = parseInt(creditsEl.value, 10);

      if (!nameEl.value.trim() || !creditsEl.value || !gradeEl.value) return;
      if (credits < 1 || credits > 6) { creditsEl.setCustomValidity('invalid'); return; }
      creditsEl.setCustomValidity('');

      subjects.push({
        id:         uid(),
        name:       nameEl.value.trim(),
        credits:    credits,
        gradePoint: parseFloat(gradeEl.value),
      });

      saveSubjects();
      renderSubjectTable();

      gpaForm.reset();
      gpaForm.classList.remove('was-validated');
    });
  }

  
  const gpaTableEl = $('gpaTable');
  const gpaEmptyEl = $('gpaEmpty');

  function attachDeleteListeners() {
    [gpaTableEl, gpaEmptyEl].forEach(parent => {
      if (!parent) return;
      parent.addEventListener('click', e => {
        const btn = e.target.closest('.gpa-delete-btn');
        if (!btn) return;
        subjects = subjects.filter(s => s.id !== btn.dataset.id);
        saveSubjects();
        renderSubjectTable();
      });
    });
  }
  attachDeleteListeners();

  
  const clearBtn = $('clearGPABtn');
  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (subjects.length === 0) return;
      if (window.confirm('Remove all subjects and reset GPA?')) {
        subjects = [];
        saveSubjects();
        renderSubjectTable();
      }
    });
  }

  
  renderSubjectTable();
})();



(function initContactForm() {
  const form       = $('contactForm');
  const successMsg = $('contactSuccess');
  const submitBtn  = $('contactSubmitBtn');

  if (!form) return;

  form.addEventListener('submit', e => {
    e.preventDefault();
    form.classList.add('was-validated');

    const name    = $('contactName');
    const email   = $('contactEmail');
    const subject = $('contactSubject');
    const message = $('contactMessage');

    if (email && !isValidEmail(email.value)) {
      email.setCustomValidity('Please enter a valid email address.');
    } else if (email) {
      email.setCustomValidity('');
    }

    if (message && message.value.trim().length < 20) {
      message.setCustomValidity('Too short.');
    } else if (message) {
      message.setCustomValidity('');
    }

    if (!form.checkValidity()) return;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending…';
    }

    setTimeout(() => {
      form.reset();
      form.classList.remove('was-validated');

      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send Message';
      }

      if (successMsg) {
        successMsg.classList.remove('d-none');
        setTimeout(() => successMsg.classList.add('d-none'), 6000);
        successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }, 1200);
  });

  const emailInput = $('contactEmail');
  if (emailInput) {
    emailInput.addEventListener('input', () => {
      if (emailInput.value && !isValidEmail(emailInput.value)) {
        emailInput.setCustomValidity('invalid');
      } else {
        emailInput.setCustomValidity('');
      }
    });
  }
})();
