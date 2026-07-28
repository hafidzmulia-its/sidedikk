import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.start();

function initializeAdminCharts() {
    document.querySelectorAll('[data-admin-chart]').forEach((canvas) => {
        const type = canvas.dataset.adminChart;
        const labels = JSON.parse(canvas.dataset.chartLabels ?? '[]');
        const values = JSON.parse(canvas.dataset.chartValues ?? '[]');

        if (!type || labels.length === 0) {
            return;
        }

        const isLineChart = type === 'line';
        const palette = ['#9c36b5', '#e85d9e', '#346fc2', '#238a57', '#a96900', '#c83e50'];

        new Chart(canvas, {
            type,
            data: {
                labels,
                datasets: [{
                    data: values,
                    label: isLineChart ? 'Jumlah Screening' : 'Distribusi Risiko',
                    borderColor: '#9c36b5',
                    backgroundColor: isLineChart ? 'rgba(156, 54, 181, 0.14)' : palette,
                    tension: 0.35,
                    fill: isLineChart,
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: !isLineChart,
                        position: 'bottom',
                    },
                },
                scales: isLineChart ? {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                        },
                    },
                } : {},
            },
        });
    });
}

function registerPwaInstallPrompt() {
    let deferredPrompt = null;
    const installButtons = document.querySelectorAll('[data-pwa-install]');

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;

        installButtons.forEach((button) => {
            button.classList.remove('hidden');
        });
    });

    installButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            if (!deferredPrompt) {
                return;
            }

            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            installButtons.forEach((item) => item.classList.add('hidden'));
        });
    });
}

function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}

initializeAdminCharts();
registerPwaInstallPrompt();
registerServiceWorker();
