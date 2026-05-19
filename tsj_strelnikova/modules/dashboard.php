<?php 
global $base_url;
if (!isset($base_url)) {
    $base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
}
if (!isLoggedIn()) { redirect('?page=auth'); } ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд | ТСЖ Стрельникова</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/main.js" data-base-url="<?= $base_url ?>" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?> (<?= $_SESSION['role'] === 'tenant' ? 'Жилец' : ($_SESSION['role'] === 'operator' ? 'Оператор' : ($_SESSION['role'] === 'worker' ? 'Рабочий' : 'Админ')) ?>)</span>
            <a href="?page=logout" class="btn-small" style="background:#fff;color:#1e3c72;">Выйти</a>
            <button id="themeToggleBtn" class="theme-toggle">🌙</button>
        </div>
    </div>
    <div class="card full-width">
        <h3>📊 Общая статистика заявок</h3>
        <canvas id="statsChart" height="150"></canvas>
        <div id="simpleStats" style="margin-top:15px;display:flex;justify-content:space-around;flex-wrap:wrap;"></div>
    </div>
    <div class="dashboard" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
        <div class="card"><h3>📝 Быстрая заявка</h3><p>Перейдите в свой раздел для создания заявки.</p><a href="?page=<?= $_SESSION['role'] ?>" class="btn-small">Перейти →</a></div>
        <div class="card"><h3>📢 Последние новости</h3><div id="lastNews"></div></div>
        <div class="card"><h3>⭐ Рейтинг рабочих</h3><div id="workersRating"></div></div>
    </div>
</div>
<script>
    async function loadDashboard() {
        const stats = await fetch('<?= $base_url ?>/api/get_stats.php').then(r=>r.json());
        document.getElementById('simpleStats').innerHTML = `<div>Всего: ${stats.total}</div><div>Выполнено: ${stats.completed}</div><div>Закрыто: ${stats.closed}</div><div>В работе: ${stats.assigned}</div>`;
        const news = await fetch('<?= $base_url ?>/api/get_news.php').then(r=>r.json());
        document.getElementById('lastNews').innerHTML = news.slice(0,3).map(n=>`<div><strong>${n.title}</strong><br><small>${new Date(n.published_at).toLocaleDateString()}</small></div>`).join('');
        const workers = await fetch('<?= $base_url ?>/api/get_workers.php').then(r=>r.json());
        document.getElementById('workersRating').innerHTML = workers.map(w=>`<div>${w.full_name}: ⭐ ${(w.rating||0).toFixed(1)} (${w.rating_count||0} оценок)</div>`).join('');
    }
    loadDashboard();
</script>
</body>
</html>
