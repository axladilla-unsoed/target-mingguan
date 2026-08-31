<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Target Mingguan</title>
  <style>
    :root {
      --bg: #f4f7fb;
      --panel: #ffffff;
      --panel-alt: #eef4ff;
      --primary: #2148c5;
      --primary-soft: #dfe7ff;
      --success: #1d9d63;
      --success-soft: #dff8ed;
      --warning: #f4a340;
      --danger: #d9485f;
      --text: #1b2430;
      --muted: #5d6b7d;
      --border: #dfe6f0;
      --shadow: 0 14px 36px rgba(26, 35, 58, 0.08);
      --radius: 18px;
    }

    * { box-sizing: border-box; }

    html, body {
      margin: 0;
      min-height: 100%;
      font-family: Inter, "Segoe UI", sans-serif;
      background: linear-gradient(180deg, #edf4ff 0%, var(--bg) 18%, var(--bg) 100%);
      color: var(--text);
    }

    body {
      padding: 32px 18px 60px;
    }

    .app {
      max-width: 1200px;
      margin: 0 auto;
    }

    .topbar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 24px;
      margin-bottom: 24px;
      flex-wrap: wrap;
    }

    .eyebrow {
      margin: 0 0 10px;
      font-size: 11px;
      letter-spacing: 0.18em;
      text-transform: uppercase;
      color: var(--primary);
      font-weight: 700;
    }

    h1 {
      margin: 0;
      font-size: clamp(2.3rem, 2.5vw + 1rem, 4rem);
      line-height: 1.02;
      letter-spacing: -0.06em;
    }

    .summary {
      min-width: 260px;
      background: rgba(255,255,255,0.7);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px 20px 16px;
      box-shadow: var(--shadow);
    }

    .summary .eyebrow {
      letter-spacing: 0.14em;
      color: var(--muted);
      margin-bottom: 12px;
    }

    .summary-value {
      font-size: clamp(1.3rem, 1rem + 1vw, 2.2rem);
      font-weight: 700;
      line-height: 1.1;
    }

    .summary-value span:first-child {
      color: var(--primary);
    }

    .progress {
      height: 8px;
      border-radius: 999px;
      background: #e8edf6;
      overflow: hidden;
      margin-top: 14px;
    }

    .progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, var(--primary), #6c89f5);
      border-radius: inherit;
      transition: width 0.2s ease;
    }

    .toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 22px;
    }

    .db-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
      color: var(--muted);
      font-weight: 500;
    }

    .db-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background-color: var(--success);
    }

    button,
    input,
    select,
    textarea {
      font: inherit;
    }

    button {
      appearance: none;
      border: none;
      border-radius: 12px;
      padding: 11px 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.15s ease, opacity 0.15s ease;
    }

    button:hover {
      transform: translateY(-1px);
    }

    .primary-btn {
      background: var(--primary);
      color: white;
    }

    .secondary-btn {
      background: var(--panel);
      color: var(--text);
      border: 1px solid var(--border);
    }

    .danger-btn {
      background: #fff0f2;
      color: var(--danger);
      border: 1px solid #f7d6dc;
    }

    .danger-btn:disabled {
      opacity: 0.45;
      cursor: not-allowed;
      transform: none;
    }

    .task-form {
      background: rgba(255,255,255,0.72);
      backdrop-filter: blur(4px);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      padding: 18px;
      margin-bottom: 22px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: minmax(220px, 1.6fr) 180px 180px;
      gap: 12px;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-width: 0;
    }

    .field label {
      font-size: 12px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--muted);
      font-weight: 700;
    }

    .field input,
    .field select,
    .field textarea {
      width: 100%;
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 11px 12px;
      color: var(--text);
    }

    .field textarea {
      min-height: 76px;
      resize: vertical;
    }

    .form-submit {
      display: flex;
      align-items: end;
      justify-content: flex-end;
      padding-top: 8px;
    }

    .days-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(220px, 1fr));
      gap: 18px;
      align-items: start;
      width: 100%;
    }

    .day-card {
      background: rgba(255,255,255,0.8);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 14px 12px 12px;
      box-shadow: var(--shadow);
      min-height: 260px;
      display: flex;
      flex-direction: column;
    }

    .day-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
      margin-bottom: 10px;
    }

    .day-header h3 {
      margin: 0;
      font-size: 1.05rem;
    }

    .chip {
      display: inline-block;
      border-radius: 999px;
      background: var(--primary-soft);
      color: var(--primary);
      font-size: 11px;
      font-weight: 700;
      padding: 5px 9px;
      letter-spacing: 0.04em;
    }

    .task-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .task-item {
      display: flex;
      gap: 10px;
      align-items: flex-start;
      padding: 10px 8px;
      border-radius: 12px;
      background: #f9fbff;
      border: 1px solid #edf2fb;
      min-width: 0;
      transition: background 0.15s ease, border-color 0.15s ease;
    }

    .task-item.done {
      background: var(--success-soft);
      border-color: #cfeedc;
    }

    .check-box {
      width: 20px;
      height: 20px;
      margin-top: 2px;
      accent-color: var(--success);
      cursor: pointer;
      flex-shrink: 0;
    }

    .task-body {
      flex: 1;
      min-width: 0;
    }

    .task-title {
      font-weight: 700;
      line-height: 1.4;
      word-break: break-word;
      color: var(--text);
    }

    .task-item.done .task-title {
      text-decoration: line-through;
      opacity: 0.7;
    }

    .task-meta {
      margin-top: 4px;
      font-size: 12px;
      color: var(--muted);
      line-height: 1.4;
      word-break: break-word;
    }

    .task-actions {
      display: flex;
      flex-direction: column;
      gap: 8px;
      min-width: 90px;
    }

    .move-select {
      padding: 8px 10px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: #fff;
      color: var(--text);
      font-size: 12px;
    }

    .delete-btn {
      background: #fff1f3;
      border: 1px solid #f7d8de;
      color: var(--danger);
      padding: 7px 8px;
      border-radius: 10px;
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
    }

    .empty-state {
      border: 1px dashed var(--border);
      border-radius: 12px;
      background: #f8fbff;
      color: var(--muted);
      padding: 12px 10px;
      font-size: 13px;
      line-height: 1.4;
    }

    .toast {
      position: fixed;
      right: 18px;
      bottom: 18px;
      background: rgba(27, 36, 48, 0.92);
      color: white;
      border-radius: 12px;
      padding: 11px 14px;
      box-shadow: var(--shadow);
      font-size: 14px;
      opacity: 0;
      transform: translateY(12px);
      transition: opacity 0.2s ease, transform 0.2s ease;
      pointer-events: none;
      max-width: 320px;
      z-index: 999;
    }

    .toast.show {
      opacity: 1;
      transform: translateY(0);
    }

    .toast.error {
      background: rgba(217, 72, 95, 0.95);
    }

    @media (max-width: 1180px) {
      .days-grid {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
      }
    }

    @media (max-width: 860px) {
      body {
        padding: 24px 14px 48px;
      }

      .topbar {
        align-items: stretch;
      }

      .summary {
        width: 100%;
      }

      .days-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 560px) {
      .form-grid {
        grid-template-columns: 1fr;
      }

      .days-grid {
        grid-template-columns: 1fr;
      }

      .task-item {
        flex-direction: column;
        gap: 8px;
      }

      .task-actions {
        width: 100%;
        min-width: unset;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 8px;
      }

      .move-select {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="app">
    <header class="topbar">
      <div>
        <p class="eyebrow">Jadwal Target · 1 Minggu</p>
        <h1>Target Harian<br>Senin – Minggu</h1>
      </div>

      <aside class="summary" aria-live="polite">
        <p class="eyebrow">Progres Minggu Ini</p>
        <div class="summary-value"><span id="done-count">0</span> / <span id="total-count">0</span> tugas</div>
        <div class="progress" aria-label="Progress minggu">
          <div id="progress-bar" class="progress-bar"></div>
        </div>
      </aside>
    </header>

    <div class="toolbar">
      <button id="reset-week" class="danger-btn" type="button" disabled>Reset minggu</button>
      <div class="db-status">
        <span class="db-dot"></span> Terhubung Database MySQL
      </div>
    </div>

    <form id="task-form" class="task-form">
      <div class="form-grid">
        <div class="field">
          <label for="task-title">Nama target</label>
          <input id="task-title" name="title" type="text" placeholder="Contoh: Materi Statistik" required>
        </div>

        <div class="field">
          <label for="task-day">Hari</label>
          <select id="task-day" name="day"></select>
        </div>

        <div class="field">
          <label for="task-hours">Waktu / estimasi</label>
          <input id="task-hours" name="hours" type="text" placeholder="Contoh: 2 jam / 08:00-10:00">
        </div>
      </div>

      <div class="field" style="margin-top: 12px;">
        <label for="task-note">Catatan</label>
        <textarea id="task-note" name="note" placeholder="Catatan tambahan atau deadline"></textarea>
      </div>

      <div class="form-submit">
        <button class="primary-btn" type="submit">Tambah workload</button>
      </div>
    </form>

    <section id="days-grid" class="days-grid" aria-live="polite"></section>
  </div>

  <div id="toast" class="toast" aria-live="polite"></div>

  <script>
    const DAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    const state = {
      tasks: []
    };

    const taskForm = document.getElementById('task-form');
    const taskTitle = document.getElementById('task-title');
    const taskDay = document.getElementById('task-day');
    const taskHours = document.getElementById('task-hours');
    const taskNote = document.getElementById('task-note');
    const daysGrid = document.getElementById('days-grid');
    const doneCountEl = document.getElementById('done-count');
    const totalCountEl = document.getElementById('total-count');
    const progressBar = document.getElementById('progress-bar');
    const resetBtn = document.getElementById('reset-week');
    const toastEl = document.getElementById('toast');

    populateDayOptions();
    fetchTasks();

    // Tambah tugas baru
    taskForm.addEventListener('submit', async function (event) {
      event.preventDefault();
      const title = taskTitle.value.trim();
      if (!title) {
        showToast('Judul target harus diisi.');
        taskTitle.focus();
        return;
      }

      const newTask = {
        title,
        day: taskDay.value,
        hours: taskHours.value.trim(),
        note: taskNote.value.trim()
      };

      try {
        const response = await fetch('api.php?action=create', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newTask)
        });
        const result = await response.json();

        if (!result.success) {
          throw new Error(result.message || 'Gagal menambahkan target.');
        }

        state.tasks.push(result.task);
        taskForm.reset();
        taskDay.value = 'Senin';
        taskTitle.focus();
        render();
        showToast(result.message || 'Workload berhasil ditambahkan.');
      } catch (error) {
        console.error(error);
        showToast(error.message, true);
      }
    });

    // Checkbox toggle & Ubah Hari
    daysGrid.addEventListener('change', async function (event) {
      const target = event.target;

      if (target.matches('input[type="checkbox"]')) {
        const taskId = target.dataset.id;
        const isDone = target.checked;
        const task = state.tasks.find(item => item.id === taskId);
        if (!task) return;

        // Optimistic UI update
        const prevDone = task.done;
        task.done = isDone;
        render();

        try {
          const response = await fetch('api.php?action=toggle_done', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: taskId, done: isDone ? 1 : 0 })
          });
          const result = await response.json();
          if (!result.success) {
            throw new Error(result.message);
          }
        } catch (error) {
          console.error(error);
          task.done = prevDone;
          render();
          showToast('Gagal mengubah status: ' + error.message, true);
        }
      }

      if (target.matches('.move-select')) {
        const taskId = target.dataset.id;
        const newDay = target.value;
        const task = state.tasks.find(item => item.id === taskId);
        if (!task) return;

        const prevDay = task.day;
        task.day = newDay;
        render();

        try {
          const response = await fetch('api.php?action=move_day', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: taskId, day: newDay })
          });
          const result = await response.json();
          if (!result.success) {
            throw new Error(result.message);
          }
        } catch (error) {
          console.error(error);
          task.day = prevDay;
          render();
          showToast('Gagal memindahkan hari: ' + error.message, true);
        }
      }
    });

    // Hapus tugas
    daysGrid.addEventListener('click', async function (event) {
      const target = event.target;
      if (!target.matches('.delete-btn')) return;

      const taskId = target.dataset.id;
      const targetIndex = state.tasks.findIndex(item => item.id === taskId);
      if (targetIndex === -1) return;

      const deletedTask = state.tasks[targetIndex];
      state.tasks.splice(targetIndex, 1);
      render();

      try {
        const response = await fetch('api.php?action=delete', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: taskId })
        });
        const result = await response.json();
        if (!result.success) {
          throw new Error(result.message);
        }
        showToast('Tugas berhasil dihapus.');
      } catch (error) {
        console.error(error);
        state.tasks.splice(targetIndex, 0, deletedTask);
        render();
        showToast('Gagal menghapus: ' + error.message, true);
      }
    });

    // Reset Minggu
    resetBtn.addEventListener('click', async function () {
      const total = state.tasks.length;
      const done = state.tasks.filter(item => item.done).length;

      if (total === 0 || done !== total) {
        showToast('Semua target belum tercapai, belum bisa reset.');
        return;
      }

      if (!window.confirm('Semua target minggu ini sudah selesai. Ingin reset checklist untuk minggu baru?')) {
        return;
      }

      try {
        const response = await fetch('api.php?action=reset_week', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' }
        });
        const result = await response.json();

        if (!result.success) {
          throw new Error(result.message);
        }

        state.tasks.forEach(task => { task.done = false; });
        render();
        showToast(result.message || 'Checklist minggu berhasil direset.');
      } catch (error) {
        console.error(error);
        showToast('Gagal mereset minggu: ' + error.message, true);
      }
    });

    // Ambil data awal dari PHP Database
    async function fetchTasks() {
      try {
        const response = await fetch('api.php?action=get_tasks');
        const result = await response.json();
        if (result.success && Array.isArray(result.tasks)) {
          state.tasks = result.tasks;
          render();
        } else {
          showToast(result.message || 'Gagal memuat data dari database.', true);
        }
      } catch (error) {
        console.error(error);
        showToast('Gagal menghubungkan ke server database.', true);
      }
    }

    function render() {
      const total = state.tasks.length;
      const done = state.tasks.filter(item => item.done).length;
      const percentage = total ? Math.round((done / total) * 100) : 0;

      doneCountEl.textContent = String(done);
      totalCountEl.textContent = String(total);
      progressBar.style.width = percentage + '%';
      resetBtn.disabled = !(total > 0 && done === total);

      daysGrid.innerHTML = '';

      DAYS.forEach(day => {
        const tasksForDay = state.tasks.filter(task => task.day === day);
        const card = document.createElement('article');
        card.className = 'day-card';

        const header = document.createElement('div');
        header.className = 'day-header';
        header.innerHTML = `<h3>${day}</h3><span class="chip">${tasksForDay.length}</span>`;

        const list = document.createElement('div');
        list.className = 'task-list';

        if (!tasksForDay.length) {
          const empty = document.createElement('div');
          empty.className = 'empty-state';
          empty.textContent = 'Belum ada workload di hari ini.';
          list.appendChild(empty);
        } else {
          tasksForDay.forEach(task => {
            const item = document.createElement('div');
            item.className = `task-item${task.done ? ' done' : ''}`;

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.checked = !!task.done;
            checkbox.className = 'check-box';
            checkbox.dataset.id = task.id;

            const body = document.createElement('div');
            body.className = 'task-body';

            const title = document.createElement('div');
            title.className = 'task-title';
            title.textContent = task.title;

            const meta = document.createElement('div');
            meta.className = 'task-meta';
            const metaParts = [];
            if (task.hours) metaParts.push(task.hours);
            if (task.note) metaParts.push(task.note);
            meta.textContent = metaParts.join(' • ');

            body.appendChild(title);
            if (metaParts.length) body.appendChild(meta);

            const actions = document.createElement('div');
            actions.className = 'task-actions';

            const select = document.createElement('select');
            select.className = 'move-select';
            select.dataset.id = task.id;
            DAYS.forEach(optionDay => {
              const option = document.createElement('option');
              option.value = optionDay;
              option.textContent = optionDay;
              if (optionDay === task.day) option.selected = true;
              select.appendChild(option);
            });

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'delete-btn';
            deleteBtn.dataset.id = task.id;
            deleteBtn.textContent = 'Hapus';

            actions.appendChild(select);
            actions.appendChild(deleteBtn);

            item.appendChild(checkbox);
            item.appendChild(body);
            item.appendChild(actions);
            list.appendChild(item);
          });
        }

        card.appendChild(header);
        card.appendChild(list);
        daysGrid.appendChild(card);
      });
    }

    function populateDayOptions() {
      DAYS.forEach(day => {
        const option = document.createElement('option');
        option.value = day;
        option.textContent = day;
        taskDay.appendChild(option);
      });
      taskDay.value = 'Senin';
    }

    function showToast(message, isError = false) {
      toastEl.textContent = message;
      toastEl.className = isError ? 'toast show error' : 'toast show';
      clearTimeout(showToast.timeoutId);
      showToast.timeoutId = setTimeout(() => {
        toastEl.classList.remove('show');
      }, 2400);
    }
  </script>
</body>
</html>
