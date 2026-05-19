<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | ТСЖ Стрельникова</title>
    <?php
    global $base_url;
    if (!isset($base_url)) {
        $base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
    }
    ?>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <script src="<?= $base_url ?>/assets/js/main.js" data-base-url="<?= $base_url ?>" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info"><button id="themeToggleBtn" class="theme-toggle">🌙</button></div>
    </div>
    <div class="card">
        <h3>🔐 Вход / Регистрация</h3>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 1;">
                <h4>Вход</h4>
                <input type="text" id="loginEmail" placeholder="Email">
                <input type="password" id="loginPassword" placeholder="Пароль">
                <button id="doLoginBtn">Войти</button>
            </div>
            <div style="flex: 1;">
                <h4>Регистрация</h4>
                <input type="text" id="regFullname" placeholder="ФИО">
                <input type="text" id="regEmail" placeholder="Email">
                <input type="text" id="regApartment" placeholder="Квартира">
                <select id="regRole"><option value="tenant">Жилец</option><option value="operator">Оператор</option><option value="worker">Рабочий</option></select>
                <input type="password" id="regPassword" placeholder="Пароль">
                <button id="doRegisterBtn">Зарегистрироваться</button>
            </div>
        </div>
        <p style="margin-top:10px;font-size:12px;">*Демо: tenant@example.com / 123, operator@example.com / 123, worker@example.com / 123, admin@example.com / 123</p>
    </div>
    <div class="card">
        <h3>🎮 Демо-режим</h3>
        <div class="role-selector">
            <button data-role="tenant" class="role-btn">🏠 Жилец</button>
            <button data-role="operator" class="role-btn">📞 Оператор</button>
            <button data-role="worker" class="role-btn">🔧 Рабочий</button>
            <button data-role="admin" class="role-btn">👑 Админ</button>
        </div>
    </div>
</div>
<script>
    const baseUrl = '<?= $base_url ?>';
    async function login(email, password) {
        const res = await fetch(baseUrl + '/api/login.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({email, password})
        });
        const data = await res.json();
        if (data.success) { location.href = baseUrl + '/'; }
        else alert(data.error);
    }
    async function register(full_name, email, apartment, role, password) {
        const res = await fetch(baseUrl + '/api/register.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({full_name, email, apartment, role, password})
        });
        const data = await res.json();
        if (data.success) alert('Регистрация успешна! Теперь войдите.');
        else alert(data.error);
    }
    function setDemoRole(role) {
        fetch(baseUrl + '/api/demo_login.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({role})
        }).then(() => location.href = baseUrl + '/');
    }
    document.getElementById('doLoginBtn').onclick = () => login(loginEmail.value, loginPassword.value);
    document.getElementById('doRegisterBtn').onclick = () => register(regFullname.value, regEmail.value, regApartment.value, regRole.value, regPassword.value);
    document.querySelectorAll('.role-btn').forEach(btn => btn.onclick = () => setDemoRole(btn.dataset.role));
</script>
</body>
</html>
