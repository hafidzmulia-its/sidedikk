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
    const promptInstallButtons = document.querySelectorAll('[data-pwa-install-prompt]');
    const installPageStates = document.querySelectorAll('[data-pwa-install-state]');
    const installPageNotes = document.querySelectorAll('[data-pwa-install-note]');
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    let deferredPrompt = null;

    function setInstallButtonVisibility(visible) {
        installButtons.forEach((button) => {
            button.classList.toggle('hidden', !visible);
        });
    }

    function setPromptButtonEnabled(enabled, visible = true) {
        promptInstallButtons.forEach((button) => {
            button.disabled = !enabled;
            button.classList.toggle('hidden', !visible);
            button.classList.toggle('opacity-50', !enabled);
            button.classList.toggle('cursor-not-allowed', !enabled);
        });
    }

    function setInstallPageState(state) {
        installPageStates.forEach((element) => {
            element.classList.toggle('hidden', element.dataset.pwaInstallState !== state);
        });
    }

    function setInstallPageNote(state) {
        installPageNotes.forEach((element) => {
            element.classList.toggle('hidden', element.dataset.pwaInstallNote !== state);
        });
    }

    function removeInstallSheets() {
        document.querySelectorAll('[data-pwa-install-sheet]').forEach((sheet) => {
            sheet.remove();
        });
    }

    if (isStandalone) {
        setInstallButtonVisibility(false);
        setPromptButtonEnabled(false, false);
        setInstallPageState('installed');
        setInstallPageNote('installed');
        removeInstallSheets();
        return;
    }

    if (installButtons.length === 0 && promptInstallButtons.length === 0 && installPageStates.length === 0) {
        return;
    }

    function createInstallSheet({ title, description }) {
        if (document.querySelector('[data-pwa-install-sheet]')) {
            return;
        }

        const overlay = document.createElement('div');
        overlay.dataset.pwaInstallSheet = 'true';
        overlay.className = 'fixed inset-0 z-[80] flex items-end bg-[#221825]/40 px-4 pb-6 pt-10 sm:items-center sm:justify-center';
        overlay.innerHTML = `
            <div class="w-full max-w-sm rounded-[28px] bg-white p-5 shadow-[0_20px_60px_rgba(34,24,37,0.18)]">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-base font-semibold text-[#221825]">${title}</p>
                        <p class="mt-2 text-sm leading-6 text-[#50434e]">
                            ${description}
                        </p>
                    </div>
                    <button type="button" data-pwa-install-close class="flex h-10 w-10 items-center justify-center rounded-full bg-[#f7edf9] text-[var(--color-primary)]">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
            </div>
        `;

        const dismiss = () => {
            overlay.remove();
        };

        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                dismiss();
            }
        });

        overlay.querySelector('[data-pwa-install-close]')?.addEventListener('click', dismiss);
        document.body.appendChild(overlay);
    }

    function showIosInstallSheet() {
        createInstallSheet({
            title: 'Pasang SIDEDIKK',
            description: 'Agar lebih mudah dibuka, ketuk <strong>Bagikan</strong> lalu pilih <strong>Tambah ke Layar Utama</strong>.',
        });
    }

    function showBrowserInstallSheet() {
        createInstallSheet({
            title: 'Pasang SIDEDIKK',
            description: 'Jika pop-up belum muncul, buka menu browser lalu pilih <strong>Install app</strong> atau <strong>Tambahkan ke layar utama</strong>.',
        });
    }

    async function promptInstall() {
        if (isIos) {
            showIosInstallSheet();
            setInstallPageState('ios');
            setInstallPageNote('ios');
            return;
        }

        if (!deferredPrompt) {
            showBrowserInstallSheet();
            setInstallPageState('unsupported');
            setInstallPageNote('unsupported');
            return;
        }

        removeInstallSheets();

        deferredPrompt.prompt();
        const choice = await deferredPrompt.userChoice.catch(() => null);

        if (choice?.outcome !== 'accepted') {
            setPromptButtonEnabled(false);
            setInstallPageState('unsupported');
            setInstallPageNote('unsupported');
        }

        deferredPrompt = null;
    }

    setInstallButtonVisibility(true);
    setPromptButtonEnabled(false);

    if (isIos) {
        setInstallPageState('ios');
        setInstallPageNote('ios');
    } else {
        setInstallPageState('unsupported');
        setInstallPageNote('unsupported');
    }

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        setPromptButtonEnabled(true);
        setInstallPageState('ready');
        setInstallPageNote('ready');
        promptInstall();
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        removeInstallSheets();
        setInstallButtonVisibility(false);
        setPromptButtonEnabled(false, false);
        setInstallPageState('installed');
        setInstallPageNote('installed');
    });

    installButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            await promptInstall();
        });
    });

    promptInstallButtons.forEach((button) => {
        button.addEventListener('click', async () => {
            await promptInstall();
        });
    });

    if (isIos) {
        window.setTimeout(() => {
            showIosInstallSheet();
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
