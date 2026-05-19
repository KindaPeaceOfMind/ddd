<?php 
global $base_url;
if (!isset($base_url)) {
    $base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
}
if ($_SESSION['role'] !== 'admin') { redirect('?page=dashboard'); } ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор | ТСЖ Стрельникова</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/main.js" data-base-url="<?= $base_url ?>" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?> (Админ)</span>
            <a href="?page=logout" class="btn-small" style="background:#fff;color:#1e3c72;">Выйти</a>
            <button id="themeToggleBtn" class="theme-toggle">🌙</button>
        </div>
    </div>
    <div class="dashboard">
        <div class="card"><h3>👥 Пользователи</h3><div id="usersList"></div></div>
        <div class="card"><h3>📈 Статистика</h3>
            <button id="exportLogsBtn" class="btn-small">📎 Экспорт логов (Excel)</button>
            <button id="exportStatsPDF" class="btn-small btn-success">📄 PDF отчёт</button>
            <canvas id="adminChart" height="200"></canvas>
            <div id="adminStats"></div>
        </div>
        <div class="card"><h3>⚠️ Срочный баннер</h3>
            <input id="bannerTitle" placeholder="Заголовок">
            <textarea id="bannerMsg" placeholder="Текст сообщения" rows="3"></textarea>
            <input type="file" id="bannerPhoto" accept="image/*">
            <div id="bannerPreview"></div>
            <button id="activateBanner" class="btn-success">🔔 Активировать</button>
            <button id="deactivateBanner" class="btn-danger">❌ Скрыть</button>
        </div>
    </div>
    <div class="card full-width"><h3>📋 Журнал действий</h3>
        <div class="filter-bar">
            <input id="logSearch" placeholder="Поиск по действию">
            <select id="logRoleFilter"><option value="">Все роли</option><option value="tenant">Жилец</option><option value="operator">Оператор</option><option value="worker">Рабочий</option><option value="admin">Админ</option></select>
            <button id="filterLogs" class="btn-small">🔍 Применить</button>
        </div>
        <div id="logsTable"></div>
        <div class="pagination" id="logsPagination"></div>
    </div>
    <div class="card full-width"><h3>✏️ Управление новостями</h3>
        <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:20px;">
            <div style="flex:1;">
                <h4>➕ Добавить новость</h4>
                <input id="newTitle" placeholder="Заголовок">
                <textarea id="newContent" placeholder="Текст" rows="3"></textarea>
                <button id="addNews" class="btn-success">➕ Добавить</button>
            </div>
        </div>
        <hr>
        <div id="newsList"></div>
        <div class="pagination" id="newsPagination"></div>
    </div>
</div>
<script>
    const baseUrl = '<?= $base_url ?>';
    let currentLogPage = 1, currentNewsPage = 1, tempBannerPhoto = '';
    
    async function loadUsers() {
        const u = await fetch(baseUrl + '/api/get_users.php').then(r=>r.json());
        document.getElementById('usersList').innerHTML = `<pre>${JSON.stringify(u,null,2)}</pre>`;
    }
    
    async function loadStats() {
        const s = await fetch(baseUrl + '/api/get_stats.php').then(r=>r.json());
        document.getElementById('adminStats').innerHTML = `<p>Всего заявок: ${s.total}<br>Закрыто: ${s.closed} (${s.total?((s.closed/s.total)*100).toFixed(1):0}%)<br>Средний рейтинг рабочих: ${s.avgRating}</p>`;
        const ctx = document.getElementById('adminChart').getContext('2d');
        if (window.adminChart) window.adminChart.destroy();
        window.adminChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Новые', 'Назначенные', 'Выполненные', 'Закрытые'],
                datasets: [{
                    data: [s.new, s.assigned, s.completed, s.closed],
                    backgroundColor: ['#f39c12', '#3498db', '#2ecc71', '#95a5a6']
                }]
            }
        });
    }
    
    async function loadLogs() {
        const search = document.getElementById('logSearch').value;
        const role = document.getElementById('logRoleFilter').value;
        const res = await fetch(`${baseUrl}/api/get_logs.php?page=${currentLogPage}&search=${encodeURIComponent(search)}&role=${role}`);
        const data = await res.json();
        
        let html = '<div class="table-wrapper"><table><thead><tr><th>Дата</th><th>Пользователь</th><th>Роль</th><th>Действие</th><th>Детали</th></tr></thead><tbody>';
        data.logs.forEach(l => {
            html += `<tr>
                <td>${new Date(l.created_at).toLocaleString()}</td>
                <td>${l.user_name || '?'}</td>
                <td>${l.role}</td>
                <td>${l.action}</td>
                <td>${l.details || ''}</td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        document.getElementById('logsTable').innerHTML = html;
        
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="log-page ${i===currentLogPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('logsPagination').innerHTML = `<div class="pagination">${pag}</div>`;
        document.querySelectorAll('.log-page').forEach(b=>b.onclick=()=>{currentLogPage=parseInt(b.dataset.page);loadLogs();});
    }
    
    async function loadNews() {
        const res = await fetch(`${baseUrl}/api/get_news.php?page=${currentNewsPage}&admin=true`);
        const data = await res.json();
        
        let html = '';
        data.news.forEach(n => {
            html += `<div style="border:1px solid #ddd;margin:15px 0;padding:15px;border-radius:12px;">
                <strong>📢 ${escapeHtml(n.title)}</strong><br>
                <small style="color:#666;">${new Date(n.published_at).toLocaleDateString()}</small>
                <p style="margin:10px 0;">${escapeHtml(n.content)}</p>
                <input id="editTitle_${n.id}" value="${escapeHtml(n.title)}" style="width:100%;margin:5px 0;">
                <textarea id="editContent_${n.id}" style="width:100%;margin:5px 0;" rows="2">${escapeHtml(n.content)}</textarea>
                <button class="btn-small btn-success" onclick="updateNews(${n.id})">💾 Сохранить</button>
                <button class="btn-small btn-danger" onclick="deleteNews(${n.id})">🗑 Удалить</button>
            </div>`;
        });
        document.getElementById('newsList').innerHTML = html || '<p>Новостей нет</p>';
        
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="news-page ${i===currentNewsPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('newsPagination').innerHTML = `<div class="pagination">${pag}</div>`;
        document.querySelectorAll('.news-page').forEach(b=>b.onclick=()=>{currentNewsPage=parseInt(b.dataset.page);loadNews();});
    }
    
    window.updateNews = async (id) => {
        await fetch(baseUrl + '/api/edit_news.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id: id,
                title: document.getElementById(`editTitle_${id}`).value,
                content: document.getElementById(`editContent_${id}`).value
            })
        });
        loadNews();
    };
    
    window.deleteNews = async (id) => {
        if(confirm('Удалить новость?')) {
            await fetch(baseUrl + '/api/delete_news.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id: id})
            });
            loadNews();
        }
    };
    
    document.getElementById('exportLogsBtn').onclick = () => location.href = baseUrl + '/api/export_logs.php';
    document.getElementById('exportStatsPDF').onclick = () => {
        const element = document.getElementById('adminStats');
        html2pdf().set({filename: 'statistika.pdf'}).from(element).save();
    };
    document.getElementById('filterLogs').onclick = () => { currentLogPage = 1; loadLogs(); };
    
    document.getElementById('addNews').onclick = async() => {
        const title = document.getElementById('newTitle').value;
        const content = document.getElementById('newContent').value;
        if(!title||!content) return alert('Заполните поля');
        await fetch(baseUrl + '/api/add_news.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({title, content})
        });
        document.getElementById('newTitle').value = '';
        document.getElementById('newContent').value = '';
        currentNewsPage = 1;
        loadNews();
    };
    
    document.getElementById('bannerPhoto').onchange = async (e) => {
        if(e.target.files[0]) {
            tempBannerPhoto = await fileToBase64(e.target.files[0]);
            document.getElementById('bannerPreview').innerHTML = `<img src="${tempBannerPhoto}" style="max-width:100px;border-radius:12px;">`;
        }
    };
    
    document.getElementById('activateBanner').onclick = async() => {
        await fetch(baseUrl + '/api/update_banner.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                active: 1,
                title: document.getElementById('bannerTitle').value,
                message: document.getElementById('bannerMsg').value,
                photo: tempBannerPhoto
            })
        });
        alert('Баннер активирован');
    };
    
    document.getElementById('deactivateBanner').onclick = async() => {
        await fetch(baseUrl + '/api/update_banner.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({active: 0})
        });
        alert('Баннер скрыт');
    };
    
    function fileToBase64(file) {
        return new Promise((resolve) => {
            const fr = new FileReader();
            fr.readAsDataURL(file);
            fr.onload = () => resolve(fr.result);
        });
    }
    
    function escapeHtml(str) {
        if(!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    loadUsers();
    loadStats();
    loadLogs();
    loadNews();
</script>
</body>
</html>
