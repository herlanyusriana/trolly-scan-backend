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
    const elOverdue = root.querySelector('[data-dashboard-overdue]');
    const elApproved = root.querySelector('[data-dashboard-approved]');
    const elPending = root.querySelector('[data-dashboard-pending]');
    const elLess3 = root.querySelector('[data-dashboard-less-3]');
    const el36 = root.querySelector('[data-dashboard-3-6]');
    const elMore6 = root.querySelector('[data-dashboard-more-6]');
    const kindEls = root.querySelectorAll('[data-dashboard-kind]');
    const kindOutEls = root.querySelectorAll('[data-dashboard-kind-out]');
    const tableEl = root.querySelector('[data-dashboard-table]');

    const updateStats = (stats) => {
        if (!stats) return;
        if (elIn) elIn.textContent = Number(stats.in ?? 0).toLocaleString('id-ID');
        if (elOut) elOut.textContent = Number(stats.out ?? 0).toLocaleString('id-ID');
        if (elOverdue) elOverdue.textContent = Number(stats.overdue ?? 0).toLocaleString('id-ID');
        if (elApproved) elApproved.textContent = Number(stats.approved ?? 0).toLocaleString('id-ID');
        if (elPending) elPending.textContent = Number(stats.pending ?? 0).toLocaleString('id-ID');
        if (stats.duration_categories) {
            if (elLess3) elLess3.textContent = Number(stats.duration_categories.less_than_3 ?? 0).toLocaleString('id-ID');
            if (el36) el36.textContent = Number(stats.duration_categories.between_3_and_6 ?? 0).toLocaleString('id-ID');
            if (elMore6) elMore6.textContent = Number(stats.duration_categories.more_than_6 ?? 0).toLocaleString('id-ID');
        }
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
                setupPaginationLinks();
            }
        } catch (error) {
            console.error('History realtime error:', error);
        }
    };

    const setupPaginationLinks = () => {
        if (!paginationEl) return;
        
        // Handle pagination links click to prevent navigating to /refresh endpoint
        paginationEl.querySelectorAll('a[href*="page="]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const href = link.getAttribute('href');
                // Navigate to the regular history page with pagination
                window.location.href = href.replace('/history/refresh', '/history');
            });
        });
    };

    refresh();
    setupPaginationLinks(); // Setup initial pagination links
    setInterval(refresh, 20000);
};

document.addEventListener('DOMContentLoaded', () => {
    initDashboardRealtime();
    initHistoryRealtime();
});
