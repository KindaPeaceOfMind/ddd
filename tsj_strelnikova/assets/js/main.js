function initTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') document.body.classList.add('dark');
    const btn = document.getElementById('themeToggleBtn');
    if (btn) {
        btn.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
        btn.onclick = () => {
            document.body.classList.toggle('dark');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
            btn.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
        };
    }
}

function getBaseUrl() {
    // Пробуем получить базовый URL из data-атрибута скрипта
    const scriptEl = document.querySelector('script[src*="main.js"]');
    if (scriptEl) {
        const dataBaseUrl = scriptEl.getAttribute('data-base-url');
        if (dataBaseUrl !== null) {
            return dataBaseUrl;
        }
        // Если data-атрибута нет, извлекаем путь из src
        const url = new URL(scriptEl.src);
        return url.pathname.substring(0, url.pathname.lastIndexOf('/'));
    }
    // Фоллбэк: используем текущий путь
    const path = window.location.pathname;
    return path.substring(0, path.lastIndexOf('/'));
}

async function loadStats() {
    const canvas = document.getElementById('statsChart');
    if (!canvas) return;
    
    const baseUrl = getBaseUrl();
    const res = await fetch(baseUrl + '/api/get_stats.php');
    const data = await res.json();
    if (window.statsChart) window.statsChart.destroy();
    window.statsChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['Новые', 'Назначенные', 'Выполненные', 'Закрытые'],
            datasets: [{
                label: 'Количество заявок',
                data: [data.new, data.assigned, data.completed, data.closed],
                backgroundColor: ['#f39c12', '#3498db', '#2ecc71', '#95a5a6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    if (document.getElementById('statsChart')) loadStats();
});
