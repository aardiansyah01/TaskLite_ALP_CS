<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaskLite</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f3f5f9;
            color: #172033;
            font-family: Arial, sans-serif;
        }

        .card {
            width: 100%;
            max-width: 420px;
            padding: 32px;
            border: 1px solid #e4e8f0;
            border-radius: 16px;
            background: white;
            box-shadow: 0 12px 36px #15213a0c;
        }

        .brand {
            margin-bottom: 8px;
            color: #3157d5;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        h1 { margin: 0 0 8px; font-size: 28px; }
        .subtitle { margin: 0 0 24px; color: #687386; line-height: 1.5; }

        label {
            display: block;
            margin: 16px 0 7px;
            font-size: 14px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d7dce5;
            border-radius: 8px;
            font-size: 15px;
        }

        button, .google-button {
            display: block;
            width: 100%;
            margin-top: 16px;
            padding: 12px;
            border: 0;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }

        button { background: #3157d5; color: white; }
        .google-button {
            border: 1px solid #d7dce5;
            background: white;
            color: #172033;
        }

        .divider {
            margin: 20px 0 4px;
            color: #8a93a3;
            font-size: 12px;
            text-align: center;
        }

        .message {
            display: none;
            margin-bottom: 16px;
            padding: 11px;
            border-radius: 8px;
            background: #fff0f0;
            color: #a52323;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.visible { display: block; }
        .hint { margin-top: 20px; color: #778195; font-size: 12px; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">TASKLITE</div>
        <h1>Selamat datang</h1>
        <p class="subtitle">
            Masuk untuk melihat dan mengelola tugas sesuai hak akses akunmu.
        </p>

        @if (session('oauth_error'))
            <div class="message visible">
                {{ session('oauth_error') }}
            </div>
        @endif

        <div id="loginMessage" class="message" role="alert"></div>

        <form id="loginForm">
            <label for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
            >

            <button type="submit" id="loginButton">Masuk</button>
        </form>

        <div class="divider">ATAU</div>

        <a class="google-button" href="/auth/google/redirect">
            Masuk dengan Google
        </a>

        <p class="hint">
            TaskLite · Sistem Manajemen Tugas dengan Kontrol Akses
        </p>
    </main>

    <script>
        const form = document.getElementById('loginForm');
        const message = document.getElementById('loginMessage');
        const loginButton = document.getElementById('loginButton');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            message.classList.remove('visible');
            loginButton.disabled = true;
            loginButton.textContent = 'Memproses...';

            try {
                const response = await fetch('/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: document.getElementById('username').value,
                        password: document.getElementById('password').value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Login gagal.');
                }

                sessionStorage.setItem('tasklite_token', data.access_token);
                window.location.assign('/dashboard');
            } catch (error) {
                message.textContent = error.message || 'Tidak dapat terhubung ke server.';
                message.classList.add('visible');
            } finally {
                loginButton.disabled = false;
                loginButton.textContent = 'Masuk';
            }
        });
    </script>
</body>
</html>