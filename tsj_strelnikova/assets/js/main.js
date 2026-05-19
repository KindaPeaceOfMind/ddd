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

async function loadStats() {
    const canvas = document.getElementById('statsChart');
    if (!canvas) return;
    const res = await fetch('/api/get_stats.php');
    const data = await res.json();
    if (window.statsChart) window.statsChart.destroy();
    window.statsChart = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: ['Новые', 'Назначенные', 'Выполненные', 'Закрытые'],
            datasets: [{
                label: 'Количество заявок',
                data: [data.new, data.assigned, data.completed, data.closed],
                backgroundColor: '#2a5298'
            }]
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    if (document.getElementById('statsChart')) loadStats();
});
