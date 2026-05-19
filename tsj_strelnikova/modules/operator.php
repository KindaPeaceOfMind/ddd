<?php 
global $base_url;
if (!isset($base_url)) {
    $base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
}
if ($_SESSION['role'] !== 'operator') { redirect('?page=dashboard'); } ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оператор | ТСЖ Стрельникова</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/main.js" data-base-url="<?= $base_url ?>" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?> (Оператор)</span>
            <a href="?page=logout" class="btn-small" style="background:#fff;color:#1e3c72;">Выйти</a>
            <button id="themeToggleBtn" class="theme-toggle">🌙</button>
        </div>
    </div>
    <div class="dashboard">
        <div class="card"><h3>⚙️ Назначить исполнителя</h3><select id="reqSelect"></select><select id="workerSelect"></select><button id="assignBtn">📌 Назначить</button></div>
        <div class="card"><h3>📊 Отчёты</h3><button id="reportBtn">👷 Показать отчёт</button><pre id="reportArea"></pre></div>
    </div>
    <div class="card full-width"><h3>📋 Все заявки</h3><div class="filter-bar"><input id="search" placeholder="Поиск"><select id="statusFilter"><option value="">Все статусы</option><option value="new">Новые</option><option value="assigned">Назначенные</option><option value="completed">Выполненные</option></select><button id="filterBtn">🔍 Применить</button></div><div id="operatorTable"></div><div class="pagination" id="pagination"></div></div>
</div>
<script>
    const baseUrl = '<?= $base_url ?>';
    let currentPage = 1, filters = {status:'',search:''};
    
    async function loadWorkers() {
        const w = await fetch(baseUrl + '/api/get_workers.php').then(r=>r.json());
        document.getElementById('workerSelect').innerHTML = w.map(w=>`<option value="${w.id}">${w.full_name} ⭐${(w.rating||0).toFixed(1)}</option>`).join('');
    }
    
    async function loadRequests() {
        const res = await fetch(`${baseUrl}/api/get_requests.php?page=${currentPage}&status=${filters.status}&search=${filters.search}`);
        const data = await res.json();
        document.getElementById('reqSelect').innerHTML = '<option>Выбрать заявку</option>' + data.requests.filter(r=>r.status==='new').map(r=>`<option value="${r.id}">#${r.id} - ${r.description.substring(0,40)}</option>`).join('');
        
        let html = '<div class="table-wrapper"><table><thead><tr><th>ID</th><th>Жилец</th><th>Категория</th><th>Описание</th><th>Статус</th><th>Действие</th></tr></thead><tbody>';
        data.requests.forEach(r => {
            html += `<tr>
                <td>${r.id}</td>
                <td>${r.tenant_name}</td>
                <td>${r.category_name}</td>
                <td>${r.description.substring(0,50)}${r.description.length>50?'...':''}</td>
                <td>${r.status}</td>
                <td>${r.status==='completed'?`<button class="btn-small closeReq" data-id="${r.id}">✔️ Закрыть</button>`:''}</td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        document.getElementById('operatorTable').innerHTML = html;
        
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="page-btn ${i===currentPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('pagination').innerHTML = `<div class="pagination">${pag}</div>`;
        
        document.querySelectorAll('.page-btn').forEach(b=>b.onclick=()=>{currentPage=parseInt(b.dataset.page);loadRequests();});
        document.querySelectorAll('.closeReq').forEach(b=>b.onclick=async()=>{
            await fetch(baseUrl + '/api/close_request.php',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({id:b.dataset.id})
            });
            loadRequests();
        });
    }
    
    document.getElementById('assignBtn').onclick = async()=>{
        await fetch(baseUrl + '/api/assign_worker.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                request_id:document.getElementById('reqSelect').value,
                worker_id:document.getElementById('workerSelect').value
            })
        });
        loadRequests();
    };
    
    document.getElementById('reportBtn').onclick = async()=>{
        const r = await fetch(baseUrl + '/api/get_workers_report.php').then(r=>r.json());
        document.getElementById('reportArea').innerText = r.text;
    };
    
    document.getElementById('filterBtn').onclick = ()=>{
        filters.status = document.getElementById('statusFilter').value;
        filters.search = document.getElementById('search').value;
        currentPage = 1;
        loadRequests();
    };
    
    loadWorkers();
    loadRequests();
</script>
</body>
</html>
