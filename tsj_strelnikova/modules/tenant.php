<?php if ($_SESSION['role'] !== 'tenant') { redirect('?page=dashboard'); } ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Жилец | ТСЖ Стрельникова</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo"><h1>🏘️ ТСЖ «Стрельникова»</h1><p>г. Хабаровск, ул. Стрельникова, 6а</p></div>
        <div class="user-info">
            <span><?= htmlspecialchars($_SESSION['user_name']) ?> (Жилец)</span>
            <a href="?page=logout" class="btn-small" style="background:#fff;color:#1e3c72;">Выйти</a>
            <button id="themeToggleBtn" class="theme-toggle">🌙</button>
        </div>
    </div>
    <div class="dashboard">
        <div class="card"><h3>📝 Новая заявка</h3>
            <select id="catId"><option value="">Категория</option><?php foreach(getCategories() as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option><?php endforeach; ?></select>
            <textarea id="desc" rows="3" placeholder="Опишите проблему..."></textarea>
            <select id="priority"><option value="low">Низкий</option><option value="medium">Средний</option><option value="high">Высокий</option></select>
            <input type="file" id="photos" multiple accept="image/*">
            <div id="preview" class="photo-gallery"></div>
            <button id="submitBtn">✅ Подать</button>
        </div>
        <div class="card"><h3>💰 Квитанции</h3><div id="invoicesList"></div></div>
    </div>
    <div class="card full-width"><h3>📋 Мои заявки</h3><div id="myRequests"></div><div class="pagination" id="pagination"></div></div>
    <div class="card full-width"><h3>📢 Новости</h3><div id="newsBlock"></div><div class="pagination" id="newsPagination"></div></div>
</div>
<script>
    let currentPage = 1, newsPage = 1;
    async function loadRequests() {
        const res = await fetch(`/api/get_my_requests.php?page=${currentPage}`);
        const data = await res.json();
        let html = '<div class="table-wrapper"><table><thead><tr><th>№</th><th>Категория</th><th>Описание</th><th>Статус</th><th>Рейтинг</th><th>Действия</th></tr></thead><tbody>';
        data.requests.forEach(r => {
            let ratingHtml = '';
            if (r.status === 'completed' && !r.rating) {
                ratingHtml = `<div class="rating-stars" data-id="${r.id}">${[1,2,3,4,5].map(s=>`<span class="star" data-val="${s}">★</span>`).join('')}</div>`;
            } else if (r.rating) {
                ratingHtml = `⭐ ${r.rating}/5`;
            } else {
                ratingHtml = '—';
            }
            html += `<tr>
                <td>${r.id}</td>
                <td>${r.category_name}</td>
                <td>${r.description.substring(0,50)}${r.description.length>50?'...':''}</td>
                <td>${getStatusBadge(r.status)}</td>
                <td>${ratingHtml}</td>
                <td><button class="btn-small view-btn" data-id="${r.id}">👁️ Детали</button></td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        document.getElementById('myRequests').innerHTML = html;
        
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="page-btn ${i===currentPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('pagination').innerHTML = `<div class="pagination">${pag}</div>`;
        
        document.querySelectorAll('.page-btn').forEach(b=>b.onclick=()=>{currentPage=parseInt(b.dataset.page);loadRequests();});
        document.querySelectorAll('.view-btn').forEach(b=>b.onclick=()=>alert('Детали заявки (расширенная информация)'));
        
        // Обработка оценки
        document.querySelectorAll('.rating-stars').forEach(el => {
            const id = parseInt(el.dataset.id);
            const stars = el.querySelectorAll('.star');
            stars.forEach(star => {
                star.onclick = async () => {
                    const val = parseInt(star.dataset.val);
                    await fetch('/api/rate_request.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({request_id: id, rating: val})
                    });
                    loadRequests();
                };
            });
        });
    }
    
    async function loadNews() {
        const res = await fetch(`/api/get_news.php?page=${newsPage}`);
        const data = await res.json();
        document.getElementById('newsBlock').innerHTML = data.news.map(n=>`<div style="border-bottom:1px solid #ddd;padding:15px 0;"><strong>${n.title}</strong><br><small>${new Date(n.published_at).toLocaleDateString()}</small><p>${n.content}</p></div>`).join('');
        let pag = '';
        for(let i=1;i<=data.totalPages;i++) pag += `<button class="news-page-btn ${i===newsPage?'btn-success':''}" data-page="${i}">${i}</button>`;
        document.getElementById('newsPagination').innerHTML = `<div class="pagination">${pag}</div>`;
        document.querySelectorAll('.news-page-btn').forEach(b=>b.onclick=()=>{newsPage=parseInt(b.dataset.page);loadNews();});
    }
    
    async function loadInvoices() {
        const inv = await fetch('/api/get_invoices.php').then(r=>r.json());
        document.getElementById('invoicesList').innerHTML = inv.map(i=>`<div style="border:1px solid #ddd;border-radius:16px;padding:15px;margin:10px 0;"><div><strong>${i.month}</strong><br>Сумма: ${i.amount} руб.<br>Статус: ${i.paid?'✅ Оплачено':'⏳ Ожидает'}</div><button class="btn-small" onclick="alert('Скачивание PDF (демо)')">📄 PDF</button></div>`).join('');
    }
    
    function getStatusBadge(status) {
        const classes = {new:'status-new',assigned:'status-assigned',completed:'status-completed',closed:'status-closed'};
        const texts = {new:'Новая',assigned:'Назначена',completed:'Выполнена',closed:'Закрыта'};
        return `<span class="status-badge ${classes[status]}">${texts[status]}</span>`;
    }
    
    document.getElementById('photos').onchange = async (e) => {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        for(let f of e.target.files) {
            const b64 = await fileToBase64(f);
            preview.innerHTML += `<img src="${b64}" style="width:80px;height:80px;object-fit:cover;border-radius:12px;">`;
        }
    };
    
    document.getElementById('submitBtn').onclick = async () => {
        const cat = document.getElementById('catId').value;
        const desc = document.getElementById('desc').value;
        if(!cat||!desc) return alert('Заполните поля');
        const files = document.getElementById('photos').files;
        let photos = [];
        for(let f of files) photos.push(await fileToBase64(f));
        await fetch('/api/add_request.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                category_id:cat,
                description:desc,
                priority:document.getElementById('priority').value,
                photos:JSON.stringify(photos)
            })
        });
        alert('Заявка подана');
        location.reload();
    };
    
    function fileToBase64(file) {
        return new Promise((resolve) => {
            const fr = new FileReader();
            fr.readAsDataURL(file);
            fr.onload = () => resolve(fr.result);
        });
    }
    
    loadRequests();
    loadNews();
    loadInvoices();
</script>
</body>
</html>
