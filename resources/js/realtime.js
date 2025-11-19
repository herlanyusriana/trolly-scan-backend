const fetchJson = async (url) => {
    const response = await fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
        credentials: 'same-origin',
    });

    if (!response.ok) {
        throw new Error('Gagal memuat data realtime');
    }

    return response.json();
};

const initDashboardRealtime = () => {
    const root = document.querySelector('[data-dashboard-realtime]');
    if (!root) return;

    const url = root.dataset.dashboardUrl;
    if (!url) return;

    const elIn = root.querySelector('[data-dashboard-in]');
    const elOut = root.querySelector('[data-dashboard-out]');
    const elApproved = root.querySelector('[data-dashboard-approved]');
    const elPending = root.querySelector('[data-dashboard-pending]');
    const kindEls = root.querySelectorAll('[data-dashboard-kind]');
    const kindOutEls = root.querySelectorAll('[data-dashboard-kind-out]');
    const tableEl = root.querySelector('[data-dashboard-table]');

    const updateStats = (stats) => {
        if (!stats) return;
        if (elIn) elIn.textContent = Number(stats.in ?? 0).toLocaleString('id-ID');
        if (elOut) elOut.textContent = Number(stats.out ?? 0).toLocaleString('id-ID');
        if (elApproved) elApproved.textContent = Number(stats.approved ?? 0).toLocaleString('id-ID');
        if (elPending) elPending.textContent = Number(stats.pending ?? 0).toLocaleString('id-ID');
        if (stats.kinds) {
            kindEls.forEach((el) => {
                const kindKey = el.dataset.dashboardKind;
                if (kindKey && typeof stats.kinds[kindKey] !== 'undefined') {
                    el.textContent = Number(stats.kinds[kindKey]).toLocaleString('id-ID');
                }
            });
        }
        if (stats.kinds_out) {
            kindOutEls.forEach((el) => {
                const kindKey = el.dataset.dashboardKindOut;
                if (kindKey && typeof stats.kinds_out[kindKey] !== 'undefined') {
                    el.textContent = Number(stats.kinds_out[kindKey]).toLocaleString('id-ID');
                }
            });
        }
    };

    const refresh = async () => {
        try {
            const data = await fetchJson(url);
            updateStats(data.stats);
            if (data.table && tableEl) {
                tableEl.innerHTML = data.table;
            }
        } catch (error) {
            console.error('Dashboard realtime error:', error);
        }
    };

    refresh();
    setInterval(refresh, 15000);
};

const initHistoryRealtime = () => {
    const root = document.querySelector('[data-history-root]');
    if (!root) return;

    const url = root.dataset.historyUrl;
    if (!url) return;

    const statsEls = {
        total: root.querySelector('[data-history-stat="total"]'),
        out: root.querySelector('[data-history-stat="out"]'),
        in: root.querySelector('[data-history-stat="in"]'),
    };
    const tableEl = root.querySelector('[data-history-table]');
    const paginationEl = root.querySelector('[data-history-pagination]');

    const updateStats = (stats) => {
        if (!stats) return;
        Object.entries(statsEls).forEach(([key, el]) => {
            if (el && typeof stats[key] !== 'undefined') {
                el.textContent = Number(stats[key]).toLocaleString('id-ID');
            }
        });
    };

    const refresh = async () => {
        try {
            const data = await fetchJson(url);
            updateStats(data.stats);
            if (data.table && tableEl) {
                tableEl.innerHTML = data.table;
            }
            if (data.pagination && paginationEl) {
                paginationEl.innerHTML = data.pagination;
            }
        } catch (error) {
            console.error('History realtime error:', error);
        }
    };

    refresh();
    setInterval(refresh, 20000);
};

document.addEventListener('DOMContentLoaded', () => {
    initDashboardRealtime();
    initHistoryRealtime();
});
