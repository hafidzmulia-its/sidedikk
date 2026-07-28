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
    const installButtons = document.querySelectorAll('[data-pwa-install]');
    const installCooldownKey = 'sidedikk:pwa-install-dismissed-at';
    const oneDayInMs = 24 * 60 * 60 * 1000;

    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    const dismissedAt = Number.parseInt(window.localStorage.getItem(installCooldownKey) ?? '0', 10);

    if (isStandalone) {
        return;
    }

    function setInstallDismissed() {
        window.localStorage.setItem(installCooldownKey, String(Date.now()));
    }

    function installRecentlyDismissed() {
        return Number.isFinite(dismissedAt) && dismissedAt > 0 && (Date.now() - dismissedAt) < oneDayInMs;
    }

    async function promptInstall(deferredPrompt) {
        if (!deferredPrompt || installRecentlyDismissed()) {
            return;
        }

        deferredPrompt.prompt();
        const choice = await deferredPrompt.userChoice.catch(() => null);

        if (!choice || choice.outcome !== 'accepted') {
            setInstallDismissed();
        }
    }

    function createIosInstallSheet() {
        if (document.querySelector('[data-pwa-ios-sheet]')) {
            return;
        }

        const overlay = document.createElement('div');
        overlay.dataset.pwaIosSheet = 'true';
        overlay.className = 'fixed inset-0 z-[80] flex items-end bg-[#221825]/40 px-4 pb-6 pt-10 sm:items-center sm:justify-center';
        overlay.innerHTML = `
            <div class="w-full max-w-sm rounded-[28px] bg-white p-5 shadow-[0_20px_60px_rgba(34,24,37,0.18)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-[#221825]">Pasang SIDEDIKK</p>
                        <p class="mt-2 text-sm leading-6 text-[#50434e]">
                            Agar lebih mudah dibuka, ketuk <strong>Bagikan</strong> lalu pilih <strong>Tambah ke Layar Utama</strong>.
                        </p>
                    </div>
                    <button type="button" data-pwa-ios-close class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f7edf9] text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>
        `;

        const dismiss = () => {
            setInstallDismissed();
            overlay.remove();
        };

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                dismiss();
            }
        });

        overlay.querySelector('[data-pwa-ios-close]')?.addEventListener('click', dismiss);
        document.body.appendChild(overlay);
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        promptInstall(event);
    });

    installButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            button.classList.add('hidden');
        });
    });

    if (isIos && !installRecentlyDismissed()) {
        window.setTimeout(() => {
            createIosInstallSheet();
        }, 600);
    }
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
