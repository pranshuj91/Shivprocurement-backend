import Chart from 'chart.js/auto';

const chartFont = {
    family: "'Inter', ui-sans-serif, system-ui, sans-serif",
    size: 12,
};

let intakeChart = null;
let statusChart = null;

function getChartData() {
    const el = document.getElementById('analytics-chart-data');
    if (!el) return null;
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

const statusColors = {
    approved: { bg: '#10b981', light: 'rgba(16, 185, 129, 0.12)' },
    pending: { bg: '#3b82f6', light: 'rgba(59, 130, 246, 0.12)' },
    flagged: { bg: '#f59e0b', light: 'rgba(245, 158, 11, 0.12)' },
    rejected: { bg: '#ef4444', light: 'rgba(239, 68, 68, 0.12)' },
};

export function initAnalyticsCharts() {
    const data = getChartData();
    if (!data) return;

    const intakeCanvas = document.getElementById('chart-weekly-intake');
    const statusCanvas = document.getElementById('chart-verification-status');
    if (!intakeCanvas || !statusCanvas) return;

    if (intakeChart) {
        intakeChart.destroy();
        intakeChart = null;
    }
    if (statusChart) {
        statusChart.destroy();
        statusChart = null;
    }

    const weekly = data.weekly || [];
    intakeChart = new Chart(intakeCanvas, {
        type: 'bar',
        data: {
            labels: weekly.map((d) => d.label),
            datasets: [
                {
                    label: 'Entries',
                    data: weekly.map((d) => d.count),
                    backgroundColor: '#0d2818',
                    hoverBackgroundColor: '#163a23',
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 44,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#18181b',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        title: (items) => {
                            const i = items[0]?.dataIndex ?? 0;
                            return weekly[i]?.date ?? items[0]?.label ?? '';
                        },
                        label: (ctx) => `${ctx.parsed.y} ${ctx.parsed.y === 1 ? 'entry' : 'entries'}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: chartFont, color: '#71717a' },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f4f4f5' },
                    border: { display: false },
                    ticks: {
                        font: chartFont,
                        color: '#a1a1aa',
                        precision: 0,
                        stepSize: 1,
                    },
                },
            },
        },
    });

    const statusRows = data.status || [];
    statusChart = new Chart(statusCanvas, {
        type: 'bar',
        data: {
            labels: statusRows.map((r) => r.label),
            datasets: [
                {
                    label: 'Logs',
                    data: statusRows.map((r) => r.count),
                    backgroundColor: statusRows.map((r) => statusColors[r.class]?.bg ?? '#71717a'),
                    hoverBackgroundColor: statusRows.map((r) => statusColors[r.class]?.bg ?? '#52525b'),
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 18,
                },
            ],
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#18181b',
                    titleFont: chartFont,
                    bodyFont: chartFont,
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: (ctx) => {
                            const total = statusRows.reduce((s, r) => s + r.count, 0);
                            const pct = total > 0 ? Math.round((ctx.parsed.x / total) * 100) : 0;
                            return `${ctx.parsed.x} logs (${pct}%)`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#f4f4f5' },
                    border: { display: false },
                    ticks: {
                        font: chartFont,
                        color: '#a1a1aa',
                        precision: 0,
                        stepSize: 1,
                    },
                },
                y: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: { font: { ...chartFont, weight: '500' }, color: '#3f3f46' },
                },
            },
        },
    });
}

export function resizeAnalyticsCharts() {
    intakeChart?.resize();
    statusChart?.resize();
}

function closeAllModernSelectMenus(except = null) {
    document.querySelectorAll('.modern-select.is-open').forEach((wrap) => {
        if (wrap !== except) {
            wrap.classList.remove('is-open');
            wrap.querySelector('.modern-select__menu')?.classList.add('hidden');
        }
    });
}

function buildModernSelect(select) {
    if (select.dataset.modernized === '1') return;

    select.dataset.modernized = '1';
    const wrap = document.createElement('div');
    wrap.className = 'modern-select';

    select.parentNode.insertBefore(wrap, select);
    wrap.appendChild(select);

    select.classList.add('modern-select__native');

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'modern-select__trigger';
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');

    const valueSpan = document.createElement('span');
    valueSpan.className = 'modern-select__value';
    const chevron = document.createElement('span');
    chevron.className = 'modern-select__chevron';
    chevron.innerHTML =
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>';
    trigger.appendChild(valueSpan);
    trigger.appendChild(chevron);

    const menu = document.createElement('div');
    menu.className = 'modern-select__menu hidden';
    menu.setAttribute('role', 'listbox');

    function syncValue() {
        const opt = select.options[select.selectedIndex];
        valueSpan.textContent = opt?.text ?? '';
        menu.querySelectorAll('.modern-select__option').forEach((btn) => {
            const selected = btn.dataset.value === select.value;
            btn.classList.toggle('is-selected', selected);
            btn.setAttribute('aria-selected', selected ? 'true' : 'false');
        });
    }

    Array.from(select.options).forEach((opt) => {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'modern-select__option';
        item.dataset.value = opt.value;
        item.setAttribute('role', 'option');
        item.innerHTML = `<span class="modern-select__option-text">${opt.text}</span><span class="modern-select__check">✓</span>`;
        item.addEventListener('click', (e) => {
            e.preventDefault();
            select.value = opt.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            syncValue();
            wrap.classList.remove('is-open');
            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        });
        menu.appendChild(item);
    });

    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = wrap.classList.contains('is-open');
        closeAllModernSelectMenus(wrap);
        if (isOpen) {
            wrap.classList.remove('is-open');
            menu.classList.add('hidden');
            trigger.setAttribute('aria-expanded', 'false');
        } else {
            wrap.classList.add('is-open');
            menu.classList.remove('hidden');
            trigger.setAttribute('aria-expanded', 'true');
        }
    });

    wrap.appendChild(trigger);
    wrap.appendChild(menu);
    syncValue();
}

export function initModernSelects(root = document) {
    root.querySelectorAll('select.logs-filter-select').forEach(buildModernSelect);
}

document.addEventListener('click', () => closeAllModernSelectMenus());

document.addEventListener('DOMContentLoaded', () => {
    initModernSelects();
    initAnalyticsCharts();

    window.initAnalyticsCharts = initAnalyticsCharts;
    window.resizeAnalyticsCharts = resizeAnalyticsCharts;
    window.initModernSelects = initModernSelects;
});
