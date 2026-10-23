<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TaskLite</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f4f6fa;
            color: #182238;
            font-family: Arial, sans-serif;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 5%;
            border-bottom: 1px solid #e1e6ef;
            background: white;
        }

        .brand { color: #3157d5; font-size: 21px; font-weight: bold; }
        .user-area { display: flex; align-items: center; gap: 12px; }

        .role {
            padding: 6px 10px;
            border-radius: 20px;
            background: #edf1ff;
            color: #3157d5;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }

        .button {
            padding: 10px 14px;
            border: 0;
            border-radius: 8px;
            background: #3157d5;
            color: white;
            font-weight: bold;
        }

        .button.secondary {
            border: 1px solid #d6dce7;
            background: white;
            color: #26334b;
        }

        .button.danger { background: #bd3434; }
        .button.small { padding: 7px 9px; font-size: 12px; }
        button:disabled { cursor: wait; opacity: .6; }

        main { width: min(1200px, 92%); margin: 30px auto; }
        .page-title { margin-bottom: 22px; }
        .page-title h1 { margin: 0 0 7px; font-size: 27px; }
        .muted { color: #738096; font-size: 14px; line-height: 1.5; }

        .notice {
            display: none;
            margin: 18px 0;
            padding: 13px 15px;
            border-radius: 9px;
            background: #eaf8ef;
            color: #17663a;
            font-size: 14px;
            line-height: 1.5;
        }

        .notice.visible { display: block; }
        .notice.error { background: #fff0f0; color: #a52323; }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin: 22px 0;
        }

        .stat, .panel {
            border: 1px solid #e2e7f0;
            border-radius: 13px;
            background: white;
        }

        .stat { padding: 19px; }
        .stat .label { color: #738096; font-size: 13px; }
        .stat .number { margin-top: 10px; font-size: 27px; font-weight: bold; }

        .panel { margin-top: 20px; padding: 22px; }
        .panel h2 { margin: 0 0 7px; font-size: 19px; }
        .panel-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 15px;
        }

        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 7px; font-size: 13px; font-weight: bold; }

        input, select, textarea {
            width: 100%;
            min-width: 0;
            padding: 10px 11px;
            border: 1px solid #d7deea;
            border-radius: 7px;
            background: white;
            color: #182238;
        }

        textarea { min-height: 78px; resize: vertical; }

        .form-actions { margin-top: 16px; }
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td {
            padding: 13px 11px;
            border-bottom: 1px solid #edf0f5;
            text-align: left;
            vertical-align: middle;
        }

        th { background: #f8f9fc; color: #647188; font-weight: bold; }
        td .actions { display: flex; flex-wrap: wrap; gap: 7px; }

        .status {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 6px;
            background: #f0f2f6;
            font-size: 11px;
            white-space: nowrap;
        }

        .status.completed { background: #e1f7e8; color: #19683a; }
        .status.in_progress { background: #fff1d8; color: #8b5b00; }
        .status.todo { background: #eaf0ff; color: #3157d5; }

        .empty { padding: 25px; text-align: center; color: #738096; }
        [hidden] { display: none !important; }

        @media (max-width: 700px) {
            header { align-items: flex-start; padding: 16px 4%; }
            .user-area { flex-wrap: wrap; justify-content: flex-end; }
            main { width: 94%; margin-top: 20px; }
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: auto; }
            .panel { padding: 15px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">TaskLite</div>

        <div class="user-area">
            <span id="roleBadge" class="role">Memuat...</span>
            <button class="button secondary" id="logoutButton" type="button">
                Keluar
            </button>
        </div>
    </header>

    <main>
        <section class="page-title">
            <h1 id="welcomeTitle">Dashboard</h1>
            <div id="profileInfo" class="muted">Memuat informasi pengguna...</div>
        </section>

        <div id="pageMessage" class="notice" role="status" aria-live="polite"></div>

        <section class="stats">
            <div class="stat">
                <div class="label">Total Tugas</div>
                <div class="number" id="totalCount">0</div>
            </div>
            <div class="stat">
                <div class="label">To Do</div>
                <div class="number" id="todoCount">0</div>
            </div>
            <div class="stat">
                <div class="label">In Progress</div>
                <div class="number" id="progressCount">0</div>
            </div>
            <div class="stat">
                <div class="label">Completed</div>
                <div class="number" id="completedCount">0</div>
            </div>
        </section>

        <section class="panel" id="createSection" hidden>
            <div class="panel-header">
                <div>
                    <h2>Buat Tugas Baru</h2>
                    <div class="muted">
                        Form ini hanya tersedia bagi Admin dan Manager.
                    </div>
                </div>
            </div>

            <form id="createTaskForm">
                <div class="form-grid">
                    <div class="field full">
                        <label for="title">Judul tugas</label>
                        <input id="title" maxlength="255" required>
                    </div>

                    <div class="field full">
                        <label for="description">Deskripsi</label>
                        <textarea id="description"></textarea>
                    </div>

                    <div class="field">
                        <label for="departmentId">Departemen</label>
                        <select id="departmentId" required></select>
                    </div>

                    <div class="field">
                        <label for="assigneeId">Ditugaskan kepada</label>
                        <select id="assigneeId" required></select>
                    </div>

                    <div class="field">
                        <label for="classification">Klasifikasi</label>
                        <select id="classification" required></select>
                    </div>

                    <div class="field">
                        <label for="dueDate">Tenggat waktu (opsional)</label>
                        <input id="dueDate" type="date">
                    </div>
                </div>

                <div class="form-actions">
                    <button class="button" id="createButton" type="submit">
                        Buat Tugas
                    </button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Daftar Tugas</h2>
                    <div class="muted">
                        Data yang ditampilkan mengikuti hak akses akun yang sedang login.
                    </div>
                </div>
                <button class="button secondary" id="refreshButton" type="button">
                    Muat Ulang
                </button>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tugas</th>
                            <th>Departemen</th>
                            <th>Penerima</th>
                            <th>Status</th>
                            <th>Klasifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tasksBody">
                        <tr><td colspan="6" class="empty">Memuat tugas...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const TOKEN_KEY = 'tasklite_token';

        // Ambil token hasil login Google dari URL fragment.
        // Fragment tidak dikirim ke server dalam HTTP request.
        if (window.location.hash.startsWith('#access_token=')) {
            try {
                const token = decodeURIComponent(
                    window.location.hash.slice('#access_token='.length)
                );

                if (token) {
                    sessionStorage.setItem(TOKEN_KEY, token);
                }
            } catch (error) {
                console.error('Token callback tidak dapat dibaca.');
            }

            // Hapus token dari address bar sesegera mungkin.
            history.replaceState(
                null,
                document.title,
                window.location.pathname + window.location.search
            );
        }

        let token = sessionStorage.getItem(TOKEN_KEY);
        let currentUser = null;
        let currentTasks = [];
        let departments = [];
        let assignees = [];

        const messageBox = document.getElementById('pageMessage');
        const tasksBody = document.getElementById('tasksBody');

        function showMessage(message, isError = false) {
            messageBox.textContent = message;
            messageBox.classList.toggle('error', isError);
            messageBox.classList.add('visible');
        }

        function clearMessage() {
            messageBox.textContent = '';
            messageBox.classList.remove('visible', 'error');
        }

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[char]);
        }

        function statusLabel(status) {
            return {
                todo: 'To Do',
                in_progress: 'In Progress',
                completed: 'Completed'
            }[status] || status;
        }

        async function api(path, options = {}) {
            const headers = {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`,
                ...(options.headers || {})
            };

            if (options.body) {
                headers['Content-Type'] = 'application/json';
            }

            const response = await fetch(path, {
                ...options,
                headers
            });

            const data = await response.json().catch(() => ({
                message: 'Server mengembalikan respons yang tidak dapat dibaca.'
            }));

            if (response.status === 401) {
                sessionStorage.removeItem(TOKEN_KEY);
                token = null;
                window.location.replace('/');
                throw new Error('Sesi berakhir. Silakan login kembali.');
            }

            return { response, data };
        }

        async function loadProfile() {
            const result = await api('/api/auth/me');

            if (!result.response.ok) {
                throw new Error(result.data.message || 'Profil tidak dapat dimuat.');
            }

            currentUser = result.data.user;

            document.getElementById('welcomeTitle').textContent =
                `Halo, ${currentUser.name}!`;

            document.getElementById('roleBadge').textContent =
                currentUser.role;

            document.getElementById('profileInfo').textContent =
                `Username: ${currentUser.username} · ` +
                `Departemen ID: ${currentUser.department_id ?? 'Belum ditetapkan'} · ` +
                `Clearance: ${currentUser.clearance_level}`;
        }

        function renderTasks(tasks) {
            currentTasks = tasks;

            document.getElementById('totalCount').textContent = tasks.length;
            document.getElementById('todoCount').textContent =
                tasks.filter(task => task.status === 'todo').length;
            document.getElementById('progressCount').textContent =
                tasks.filter(task => task.status === 'in_progress').length;
            document.getElementById('completedCount').textContent =
                tasks.filter(task => task.status === 'completed').length;

            if (tasks.length === 0) {
                tasksBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty">
                            Tidak ada tugas yang dapat ditampilkan untuk akun ini.
                        </td>
                    </tr>`;
                return;
            }

            tasksBody.innerHTML = tasks.map(task => {
                const nextStatus = task.status === 'todo'
                    ? 'in_progress'
                    : task.status === 'in_progress'
                        ? 'completed'
                        : 'todo';

                const canUpdateStatus =
                    currentUser.role !== 'member' ||
                    task.status !== 'completed';

                const canDelete =
                    currentUser.role === 'admin' ||
                    (
                        currentUser.role === 'manager' &&
                        task.status !== 'completed'
                    );

                let actions = '';

                if (canUpdateStatus) {
                    actions += `
                        <button
                            class="button small"
                            type="button"
                            data-action="status"
                            data-id="${Number(task.id)}"
                            data-next="${nextStatus}">
                            ${escapeHtml(statusLabel(nextStatus))}
                        </button>`;
                }

                if (canDelete) {
                    actions += `
                        <button
                            class="button danger small"
                            type="button"
                            data-action="delete"
                            data-id="${Number(task.id)}">
                            Hapus
                        </button>`;
                }

                return `
                    <tr>
                        <td>
                            <strong>${escapeHtml(task.title)}</strong>
                            <div class="muted">${escapeHtml(task.description || '')}</div>
                        </td>
                        <td>${escapeHtml(task.department?.name || '-')}</td>
                        <td>${escapeHtml(task.assignee?.username || '-')}</td>
                        <td>
                            <span class="status ${escapeHtml(task.status)}">
                                ${escapeHtml(statusLabel(task.status))}
                            </span>
                        </td>
                        <td>${escapeHtml(task.classification)}</td>
                        <td><div class="actions">${actions || '<span class="muted">-</span>'}</div></td>
                    </tr>`;
            }).join('');
        }

        async function loadTasks() {
            const result = await api('/api/tasks');

            if (!result.response.ok) {
                renderTasks([]);
                showMessage(
                    result.data.message ||
                    'Daftar tugas tidak dapat dimuat. Periksa departemen dan policy akun.',
                    true
                );
                return;
            }

            clearMessage();
            renderTasks(result.data.data || []);
        }

        function renderAssigneeOptions() {
            const departmentId = document.getElementById('departmentId').value;
            const select = document.getElementById('assigneeId');

            const filtered = assignees.filter(user =>
                String(user.department_id) === String(departmentId)
            );

            select.innerHTML = filtered.map(user => `
                <option value="${Number(user.id)}">
                    ${escapeHtml(user.name)} (@${escapeHtml(user.username)})
                </option>
            `).join('');

            if (filtered.length === 0) {
                select.innerHTML = '<option value="">Tidak ada Member di departemen ini</option>';
            }
        }

        async function loadTaskOptions() {
            const result = await api('/api/task-options');

            if (!result.response.ok) {
                showMessage(
                    result.data.message || 'Pilihan pembuatan tugas tidak dapat dimuat.',
                    true
                );
                return;
            }

            departments = result.data.departments || [];
            assignees = result.data.assignees || [];

            const departmentSelect = document.getElementById('departmentId');

            departmentSelect.innerHTML = departments.map(department => `
                <option value="${Number(department.id)}">
                    ${escapeHtml(department.name)}
                </option>
            `).join('');

            const maxClassification = currentUser.role === 'admin'
                ? 3
                : Math.min(3, Number(currentUser.clearance_level));

            const classificationSelect = document.getElementById('classification');

            classificationSelect.innerHTML = Array.from(
                { length: maxClassification },
                (_, index) => index + 1
            ).map(level => `
                <option value="${level}">Level ${level}</option>
            `).join('');

            renderAssigneeOptions();
        }

        document.getElementById('departmentId')
            .addEventListener('change', renderAssigneeOptions);

        document.getElementById('createTaskForm')
            .addEventListener('submit', async function (event) {
                event.preventDefault();
                clearMessage();

                const button = document.getElementById('createButton');
                button.disabled = true;

                const body = {
                    title: document.getElementById('title').value.trim(),
                    description: document.getElementById('description').value.trim() || null,
                    department_id: Number(document.getElementById('departmentId').value),
                    assigned_to: Number(document.getElementById('assigneeId').value),
                    classification: Number(document.getElementById('classification').value),
                    due_date: document.getElementById('dueDate').value || null
                };

                try {
                    const result = await api('/api/tasks', {
                        method: 'POST',
                        body: JSON.stringify(body)
                    });

                    if (!result.response.ok) {
                        throw new Error(result.data.message || 'Tugas gagal dibuat.');
                    }

                    this.reset();
                    renderAssigneeOptions();

                    showMessage('Tugas berhasil dibuat.');
                    await loadTasks();
                } catch (error) {
                    showMessage(error.message || 'Terjadi kesalahan.', true);
                } finally {
                    button.disabled = false;
                }
            });

        tasksBody.addEventListener('click', async function (event) {
            const button = event.target.closest('button[data-action]');

            if (!button) return;

            const id = Number(button.dataset.id);
            const action = button.dataset.action;

            clearMessage();
            button.disabled = true;

            try {
                let result;

                if (action === 'status') {
                    result = await api(`/api/tasks/${id}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify({
                            status: button.dataset.next
                        })
                    });
                } else if (action === 'delete') {
                    if (!confirm('Yakin ingin menghapus tugas ini?')) {
                        button.disabled = false;
                        return;
                    }

                    result = await api(`/api/tasks/${id}`, {
                        method: 'DELETE'
                    });
                }

                if (!result.response.ok) {
                    throw new Error(
                        result.data.message || 'Aksi tidak diizinkan.'
                    );
                }

                showMessage(result.data.message || 'Perubahan berhasil disimpan.');
                await loadTasks();
            } catch (error) {
                showMessage(error.message || 'Terjadi kesalahan.', true);
            } finally {
                button.disabled = false;
            }
        });

        document.getElementById('refreshButton').addEventListener('click', async () => {
            clearMessage();
            await loadTasks();
        });

        document.getElementById('logoutButton').addEventListener('click', async () => {
            const button = document.getElementById('logoutButton');
            button.disabled = true;

            try {
                await api('/api/auth/logout', {
                    method: 'POST'
                });
            } catch (error) {
                // Token dihapus lokal meskipun request logout gagal.
            } finally {
                sessionStorage.removeItem(TOKEN_KEY);
                window.location.replace('/');
            }
        });

        async function initDashboard() {
            if (!token) {
                window.location.replace('/');
                return;
            }

            try {
                await loadProfile();

                if (['admin', 'manager'].includes(currentUser.role)) {
                    document.getElementById('createSection').hidden = false;
                    await loadTaskOptions();
                }

                await loadTasks();
            } catch (error) {
                showMessage(error.message || 'Dashboard gagal dimuat.', true);
            }
        }

        initDashboard();
    </script>
</body>
</html>