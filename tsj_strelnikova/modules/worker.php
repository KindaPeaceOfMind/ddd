<?php 
global $base_url;
if (!isset($base_url)) {
    $base_url = dirname($_SERVER['SCRIPT_NAME']) === '/' ? '' : dirname($_SERVER['SCRIPT_NAME']);
}
if ($_SESSION['role'] !== 'worker') { redirect('?page=dashboard'); } ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рабочий | ТСЖ Стрельникова</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/main.js" data-base-url="<?= $base_url ?>" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?> (Рабочий)</span>
            <a href="?page=logout" class="btn-small" style="background:#fff;color:#1e3c72;">Выйти</a>
            <button id="themeToggleBtn" class="theme-toggle">🌙</button>
        </div>
    </div>
    <div class="dashboard">
        <div class="card"><h3>📸 Отчёт о выполнении</h3>
            <select id="taskSelect"></select>
            <textarea id="reportComment" rows="2" placeholder="Комментарий"></textarea>
            <input type="text" id="materials" placeholder="Использованные материалы">
            <input type="number" id="hours" placeholder="Часы">
            <input type="file" id="workPhotos" multiple accept="image/*">
            <div id="workPreview" class="photo-gallery"></div>
            <button id="completeBtn">✅ Завершить</button>
        </div>
    </div>
    <div class="card full-width"><h3>🔧 Мои задания</h3><div id="tasksList"></div><div class="pagination" id="pagination"></div></div>
</div>
<script>
    const baseUrl = '<?= $base_url ?>';
    let currentPage = 1, tempPhotos = [];
    
    async function loadTasks() {
        const res = await fetch(`${baseUrl}/api/get_my_tasks.php?page=${currentPage}`);
        const data = await res.json();
        document.getElementById('taskSelect').innerHTML = '<option>Выбрать заявку</option>' + data.tasks.map(t=>`<option value="${t.id}">#${t.id} - ${t.description.substring(0,40)}</option>`).join('');
        
        let html = '<ul style="list-style:none;padding:0;">';
        data.tasks.forEach(t => {
            html += `<li style="background:#f8f9fa;margin:10px 0;padding:15px;border-radius:12px;border-left:4px solid #2a5298;">
                <strong>📌 Заявка #${t.id}</strong><br>
                <small>Категория: ${t.category_name}</small><br>
                ${t.description}<br>
                <small>Приоритет: ${t.priority === 'high' ? 'Высокий' : (t.priority === 'medium' ? 'Средний' : 'Низкий')}</small>
            </li>`;
        });
        html += '</ul>';
        document.getElementById('tasksList').innerHTML = html;
        
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="page-btn ${i===currentPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('pagination').innerHTML = `<div class="pagination">${pag}</div>`;
        document.querySelectorAll('.page-btn').forEach(b=>b.onclick=()=>{currentPage=parseInt(b.dataset.page);loadTasks();});
    }
    
    document.getElementById('workPhotos').onchange = async (e) => {
        const preview = document.getElementById('workPreview');
        preview.innerHTML = '';
        tempPhotos = [];
        for(let f of e.target.files) {
            const b64 = await fileToBase64(f);
            preview.innerHTML += `<img src="${b64}" style="width:80px;height:80px;object-fit:cover;border-radius:12px;margin:5px;">`;
            tempPhotos.push(b64);
        }
    };
    
    document.getElementById('completeBtn').onclick = async()=>{
        const taskId = document.getElementById('taskSelect').value;
        const comment = document.getElementById('reportComment').value;
        if(!taskId || !comment) return alert('Заполните комментарий');
        await fetch(baseUrl + '/api/complete_request.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                request_id:taskId,
                comment:comment,
                photos:JSON.stringify(tempPhotos)
            })
        });
        alert('Заявка выполнена!');
        location.reload();
    };
    
    function fileToBase64(file) {
        return new Promise((resolve) => {
            const fr = new FileReader();
            fr.readAsDataURL(file);
            fr.onload = () => resolve(fr.result);
        });
    }
    
    loadTasks();
</script>
</body>
</html>
