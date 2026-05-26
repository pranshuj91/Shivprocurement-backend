function labStatusPillHtml(status) {
    const map = {
        pass: ['status-pill--approved', 'Pass'],
        fail: ['status-pill--rejected', 'Fail'],
        retest: ['status-pill--flagged', 'Retest'],
        pending: ['status-pill--pending', 'Pending'],
    };
    const [cls, label] = map[status] || map.pending;
    return `<span class="status-pill ${cls}"><span class="status-pill__dot"></span>${label}</span>`;
}

function parseRowEntryJson(row) {
    if (!row?.dataset?.json) return null;
    try {
        return JSON.parse(row.dataset.json);
    } catch (err) {
        console.error('Invalid row JSON', err);
        return null;
    }
}

function lookupEntryById(entryId) {
    if (!entryId || typeof logsEntriesById === 'undefined') return null;
    return logsEntriesById[entryId] || logsEntriesById[String(entryId)] || logsEntriesById[Number(entryId)] || null;
}

function getEntryForLabTest(btn) {
    const entryId = btn?.dataset?.entryId;
    const fromId = lookupEntryById(entryId);
    if (fromId) return fromId;
    const row = btn?.closest?.('.select-row');
    if (row) {
        const fromRow = lookupEntryById(row.dataset.entryId || row.dataset.id);
        if (fromRow) return fromRow;
        return parseRowEntryJson(row);
    }
    return null;
}

function labTestUrl(entryId) {
    if (typeof labTestRouteTemplate !== 'undefined') {
        return labTestRouteTemplate.replace('__ENTRY_ID__', encodeURIComponent(entryId));
    }
    return `/entries/${encodeURIComponent(entryId)}/lab-test`;
}

function openLabTestModalFromRow(btn) {
    const entry = getEntryForLabTest(btn);
    if (!entry) {
        if (typeof showToast === 'function') {
            showToast('Could not load entry. Refresh the page and try again.', 'error');
        } else {
            alert('Could not load entry. Refresh the page and try again.');
        }
        return;
    }
    openLabTestModal(entry);
}

function openLabTestModal(entry) {
    const modal = document.getElementById('lab-test-modal');
    const form = document.getElementById('lab-test-form');
    const errorsDiv = document.getElementById('lab-test-errors');
    if (!modal || !form) {
        console.error('Lab test modal not found in DOM');
        return;
    }

    document.getElementById('lab_test_entry_id').value = entry.id;
    document.getElementById('lab-test-entry-id').textContent = entry.id;
    document.getElementById('lab-test-modal-title').textContent = entry.lab_name ? 'Edit Lab Test Entry' : 'Add Lab Test Entry';

    const moisture = parseFloat(entry.moisture) || 0;
    const fm = parseFloat(entry.fm) || 0;
    const dm = parseFloat(entry.dm) || 0;
    document.getElementById('lab-test-field-readings').textContent =
        `M ${moisture.toFixed(1)}% · FM ${fm.toFixed(1)}% · DM ${dm.toFixed(1)}%`;

    form.lab_name.value = entry.lab_name || '';
    form.lab_test_status.value = entry.lab_test_status || 'pending';
    form.lab_moisture.value = entry.lab_moisture ?? entry.moisture ?? '';
    form.lab_fm.value = entry.lab_fm ?? entry.fm ?? '';
    form.lab_dm.value = entry.lab_dm ?? entry.dm ?? '';

    if (errorsDiv) {
        errorsDiv.classList.add('hidden');
        errorsDiv.innerText = '';
    }

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        const inner = modal.querySelector('.rounded-2xl');
        if (inner) inner.classList.remove('scale-95');
    });
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeLabTestModal() {
    const modal = document.getElementById('lab-test-modal');
    if (!modal) return;
    modal.classList.add('opacity-0');
    const inner = modal.querySelector('.rounded-2xl');
    if (inner) inner.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function updateRowLabBadge(row, entry) {
    if (!row) return;
    if (typeof logsEntriesById !== 'undefined' && entry.id) {
        logsEntriesById[entry.id] = entry;
    }
    const badge = row.querySelector('.lab-test-badge');
    if (entry.lab_test_status) {
        const pillHtml = `<span class="lab-test-badge inline-flex mt-1">${labStatusPillHtml(entry.lab_test_status)}</span>`;
        if (badge) {
            badge.outerHTML = pillHtml;
        } else {
            const cell = row.querySelector('.row-status-cell');
            if (cell) {
                const wrap = document.createElement('div');
                wrap.innerHTML = pillHtml;
                cell.appendChild(wrap.firstChild);
            }
        }
    }
    const btn = row.querySelector('.lab-test-row-btn');
    if (btn) {
        const label = entry.lab_name ? 'Edit Lab Test' : 'Add Lab Test';
        btn.innerHTML = `<i data-lucide="flask-conical" class="w-3.5 h-3.5" aria-hidden="true"></i><span>${label}</span>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

let currentDrawerEntry = null;

function openLabTestModalFromDrawer() {
    if (currentDrawerEntry) {
        openLabTestModal(currentDrawerEntry);
    }
}

function refreshDrawerLabSection(entry) {
    currentDrawerEntry = entry;
    if (typeof logsEntriesById !== 'undefined' && entry?.id) {
        logsEntriesById[entry.id] = entry;
    }
    const empty = document.getElementById('drawer-lab-empty');
    const content = document.getElementById('drawer-lab-content');
    const btnLabel = document.getElementById('drawer-lab-btn-label');
    if (!empty || !content) return;

    const hasLab = entry.lab_test_status || entry.lab_name;
    if (btnLabel) {
        btnLabel.textContent = hasLab ? 'Edit Lab Test' : 'Add Lab Test';
    }

    if (!hasLab) {
        empty.classList.remove('hidden');
        content.classList.add('hidden');
        return;
    }

    empty.classList.add('hidden');
    content.classList.remove('hidden');
    document.getElementById('drawer-lab-name').textContent = entry.lab_name || '—';
    document.getElementById('drawer-lab-status').innerHTML = labStatusPillHtml(entry.lab_test_status || 'pending');
    document.getElementById('drawer-lab-moisture').textContent = entry.lab_moisture != null ? parseFloat(entry.lab_moisture).toFixed(1) + '%' : '—';
    document.getElementById('drawer-lab-fm').textContent = entry.lab_fm != null ? parseFloat(entry.lab_fm).toFixed(1) + '%' : '—';
    document.getElementById('drawer-lab-dm').textContent = entry.lab_dm != null ? parseFloat(entry.lab_dm).toFixed(1) + '%' : '—';
    const recorded = entry.lab_recorded_at
        ? new Date(entry.lab_recorded_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
        : '—';
    document.getElementById('drawer-lab-recorded').textContent = recorded;
    const recorder = entry.lab_recorded_by?.name || '—';
    document.getElementById('drawer-lab-by').textContent = recorder;
}

async function submitLabTest(event) {
    event.preventDefault();
    const form = document.getElementById('lab-test-form');
    const entryId = document.getElementById('lab_test_entry_id').value;
    const errorsDiv = document.getElementById('lab-test-errors');
    const submitBtn = document.getElementById('lab-test-submit');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const data = {
        lab_name: form.lab_name.value.trim(),
        lab_test_status: form.lab_test_status.value,
        lab_moisture: parseFloat(form.lab_moisture.value),
        lab_fm: parseFloat(form.lab_fm.value),
        lab_dm: parseFloat(form.lab_dm.value),
    };

    if (errorsDiv) {
        errorsDiv.classList.add('hidden');
        errorsDiv.innerText = '';
    }
    submitBtn.disabled = true;

    try {
        const res = await fetch(labTestUrl(entryId), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        });

        const contentType = res.headers.get('content-type') || '';
        let resData = {};
        if (contentType.includes('application/json')) {
            resData = await res.json();
        } else {
            throw new Error(res.status === 419 ? 'Session expired. Please refresh the page.' : 'Unexpected server response.');
        }

        if (!res.ok) {
            let msg = resData.message || 'Validation failed.';
            if (resData.errors) {
                msg = Object.values(resData.errors).flat().join('\n');
            }
            throw new Error(msg);
        }

        if (resData.success) {
            if (typeof showToast === 'function') {
                showToast(resData.message || 'Lab test saved.', 'success');
            }
            const entry = resData.entry;
            if (typeof logsEntriesById !== 'undefined' && entry?.id) {
                logsEntriesById[entry.id] = entry;
            }
            const row = document.querySelector(`.select-row[data-id="${entry.id}"]`);
            updateRowLabBadge(row, entry);
            if (typeof currentEntryId !== 'undefined' && currentEntryId === entry.id) {
                refreshDrawerLabSection(entry);
                currentDrawerEntry = entry;
            }
            closeLabTestModal();
        }
    } catch (err) {
        if (errorsDiv) {
            errorsDiv.innerText = err.message || 'Network error.';
            errorsDiv.classList.remove('hidden');
        }
    } finally {
        submitBtn.disabled = false;
    }
}

window.openLabTestModalFromRow = openLabTestModalFromRow;
window.openLabTestModal = openLabTestModal;
window.closeLabTestModal = closeLabTestModal;
window.submitLabTest = submitLabTest;
