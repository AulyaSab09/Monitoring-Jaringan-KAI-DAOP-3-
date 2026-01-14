/**
 * Network Monitoring Dashboard - JavaScript
 * Handles: Sound notifications, Tree view, Zoom/Pan, Real-time updates, Tooltips
 */

// ==========================================
// CONFIG - URL untuk fetch data
// Karena tidak bisa pakai Blade di file JS, kita ambil dari data attribute atau hardcode path
// ==========================================
const monitorDataUrl = document.body.dataset.monitorDataUrl || '/preview/data';

// ==========================================
// 0. NOTIFICATION SOUND SYSTEM
// ==========================================
const soundConnect = document.getElementById('sound-connect');
const soundDisconnect = document.getElementById('sound-disconnect');
let soundEnabled = true; // DEFAULT ON
let audioUnlocked = false;

// Simpan status sebelumnya untuk deteksi perubahan
const previousStatus = new Map();

// Track card yang sedang di-hover untuk update real-time tooltip
let hoveredCardId = null;

// Unlock audio context on first user interaction (required by browsers)
function unlockAudio() {
    if (audioUnlocked) return;

    soundConnect.volume = 0.01;
    soundConnect.play().then(() => {
        soundConnect.pause();
        soundConnect.currentTime = 0;
        soundConnect.volume = 1;
        audioUnlocked = true;
        console.log('🔊 Audio auto-unlocked!');
    }).catch(e => console.log('Audio unlock pending:', e));
}

// Unlock audio on any user interaction
document.addEventListener('click', unlockAudio, { once: true });
document.addEventListener('keydown', unlockAudio, { once: true });

// Toggle sound on/off via button
function enableSound() {
    soundEnabled = !soundEnabled;

    const btn = document.getElementById('sound-toggle');
    const iconOff = document.getElementById('sound-icon-off');
    const iconOn = document.getElementById('sound-icon-on');
    const label = document.getElementById('sound-label');

    if (soundEnabled) {
        // Unlock audio if not already
        unlockAudio();

        btn.classList.remove('bg-gray-800', 'hover:bg-gray-700');
        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
        iconOff.classList.add('hidden');
        iconOn.classList.remove('hidden');
        label.textContent = '🔊 Suara Aktif';
    } else {
        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
        btn.classList.add('bg-gray-800', 'hover:bg-gray-700');
        iconOff.classList.remove('hidden');
        iconOn.classList.add('hidden');
        label.textContent = '🔇 Suara Mati';
    }
}

// Set initial button state to ON
(function initSoundButton() {
    const btn = document.getElementById('sound-toggle');
    const iconOff = document.getElementById('sound-icon-off');
    const iconOn = document.getElementById('sound-icon-on');
    const label = document.getElementById('sound-label');

    if (btn && soundEnabled) {
        btn.classList.remove('bg-gray-800', 'hover:bg-gray-700');
        btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
        if (iconOff) iconOff.classList.add('hidden');
        if (iconOn) iconOn.classList.remove('hidden');
        if (label) label.textContent = '🔊 Suara Aktif';
    }
})();

// Inisialisasi status awal dari semua card
document.querySelectorAll('.monitor-card').forEach(card => {
    const id = card.dataset.id;
    const status = card.dataset.status;
    previousStatus.set(id, status);
    console.log(`📋 Init status: Device ${id} = ${status}`);
});

// Fungsi untuk play sound
function playNotificationSound(type) {
    if (!soundEnabled) {
        console.log('🔇 Sound disabled - klik tombol "Suara" untuk mengaktifkan');
        return;
    }

    const sound = type === 'connect' ? soundConnect : soundDisconnect;
    if (sound) {
        sound.currentTime = 0;
        sound.volume = 1;
        sound.play().then(() => {
            console.log(`🎵 Playing ${type} sound`);
        }).catch(e => {
            console.log('Sound play error:', e.message);
        });
    }
}

// Fungsi untuk cek perubahan status dan mainkan suara
function checkStatusChange(id, newStatus) {
    const oldStatus = previousStatus.get(id);

    if (oldStatus && oldStatus !== newStatus) {
        // Status BERUBAH!
        console.log(`⚡ Status berubah: Device ${id}: ${oldStatus} → ${newStatus}`);

        if (newStatus === 'connected' && oldStatus === 'disconnected') {
            console.log(`🟢 Device ${id} CONNECTED!`);
            playNotificationSound('connect');
        } else if (newStatus === 'disconnected' && oldStatus === 'connected') {
            console.log(`🔴 Device ${id} DISCONNECTED!`);
            playNotificationSound('disconnect');
        } else if (newStatus === 'disconnected' && oldStatus !== 'disconnected') {
            console.log(`🔴 Device ${id} DISCONNECTED!`);
            playNotificationSound('disconnect');
        } else if (newStatus === 'connected' && oldStatus !== 'connected') {
            console.log(`🟢 Device ${id} CONNECTED!`);
            playNotificationSound('connect');
        }
    }

    // Update status simpanan
    previousStatus.set(id, newStatus);
}

// ==========================================
// 1. FITUR BUKA TUTUP (COLLAPSE)
// ==========================================
function toggleBranch(id) {
    const childContainer = document.getElementById('children-' + id);
    const arrow = document.getElementById('arrow-' + id);

    if (childContainer) {
        if (childContainer.style.display === 'none') {
            childContainer.style.display = 'flex';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            childContainer.style.display = 'none';
            arrow.style.transform = 'rotate(-90deg)';
        }
        setTimeout(drawTreeLines, 50);
    }
}

// ==========================================
// 2. APEXCHART SETUP
// ==========================================
var chart = new ApexCharts(document.querySelector("#chart-canvas"), {
    series: [{ data: [] }],
    chart: { type: 'area', height: 80, sparkline: { enabled: true } },
    stroke: { curve: 'monotoneCubic', width: 2, colors: ['#3b82f6'] },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.1 } },
    tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
});
chart.render();

// ==========================================
// 3. ZOOM & PAN
// ==========================================
let currentZoom = 1, panX = 0, panY = 0, isDragging = false, startX, startY;
const viewport = document.getElementById('tree-viewport');
const container = document.getElementById('tree-container');
const zoomLabel = document.getElementById('zoom-level');

function updateTransform() {
    viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
    if (zoomLabel) zoomLabel.innerText = Math.round(currentZoom * 100) + '%';
    if (!isDragging) requestAnimationFrame(drawTreeLines);
}

container.addEventListener('mousedown', e => {
    if (e.target.closest('button, a')) return;
    isDragging = true; startX = e.clientX - panX; startY = e.clientY - panY;
    container.style.cursor = 'grabbing';
});

window.addEventListener('mousemove', e => {
    if (!isDragging) return;
    panX = e.clientX - startX; panY = e.clientY - startY;
    viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
});

window.addEventListener('mouseup', () => { isDragging = false; container.style.cursor = 'grab'; drawTreeLines(); });
container.addEventListener('wheel', e => { e.preventDefault(); currentZoom += e.deltaY > 0 ? -0.1 : 0.1; updateTransform(); });

// Global functions for buttons
window.zoomIn = () => { currentZoom += 0.2; updateTransform(); };
window.zoomOut = () => { currentZoom -= 0.2; updateTransform(); };
window.resetZoom = () => { currentZoom = 1; panX = 0; panY = 0; updateTransform(); };
window.enableSound = enableSound;
window.toggleBranch = toggleBranch;

// ==========================================
// 4. GAMBAR GARIS OTOMATIS WARNA
// ==========================================
function drawTreeLines() {
    const svg = document.getElementById('tree-lines-svg');
    const wrapper = document.getElementById('tree-wrapper');
    if (!svg || !wrapper) return;
    svg.innerHTML = '';

    const getPos = (el) => {
        let x = 0, y = 0, w = el.offsetWidth, h = el.offsetHeight;
        while (el && el !== viewport) { x += el.offsetLeft; y += el.offsetTop; el = el.offsetParent; }
        return { x, y, w, h, cx: x + w / 2, cy: y + h / 2 };
    };

    document.querySelectorAll('.tree-node').forEach(node => {
        const parentCard = node.querySelector(':scope > .tree-node-card .monitor-card');
        const childrenContainer = node.querySelector(':scope > .tree-children');

        if (parentCard && childrenContainer && childrenContainer.style.display !== 'none') {
            const pPos = getPos(parentCard);
            const children = childrenContainer.querySelectorAll(':scope > .tree-node > .tree-node-card .monitor-card');

            if (children.length > 0) {
                let minX = Infinity, maxX = -Infinity;
                const midY = pPos.y + pPos.h + 20;

                children.forEach(child => {
                    const cPos = getPos(child);
                    minX = Math.min(minX, cPos.cx);
                    maxX = Math.max(maxX, cPos.cx);

                    // AMBIL STATUS DARI CARD (CONNECTED/UNSTABLE/DISCONNECTED)
                    const status = child.dataset.status || 'pending';

                    // Garis Vertikal Anak (Mengikuti Status Anak)
                    createPath(`M ${cPos.cx} ${cPos.y} L ${cPos.cx} ${midY}`, status);
                });

                // Garis Induk Turun (Warna Netral/Pending)
                createPath(`M ${pPos.cx} ${pPos.y + pPos.h} L ${pPos.cx} ${midY}`, 'pending');

                // Garis Horizontal (Warna Netral/Pending)
                if (minX !== Infinity) createPath(`M ${minX} ${midY} L ${maxX} ${midY}`, 'pending');
            }
        }
    });

    function createPath(d, status) {
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        path.setAttribute('d', d);
        path.setAttribute('class', `tree-line status-${status}`);
        svg.appendChild(path);
    }
}

// ==========================================
// 5. REALTIME DATA UPDATE (MODIFIED)
// ==========================================
const parser = new DOMParser();

function refreshData() {
    if (isDragging) return;

    fetch(monitorDataUrl + "?t=" + new Date().getTime())
        .then(r => r.text())
        .then(html => {
            const doc = parser.parseFromString(html, 'text/html');

            doc.querySelectorAll('.monitor-card').forEach(newCard => {
                const id = newCard.dataset.id;
                const oldCard = document.getElementById('card-' + id);

                if (oldCard) {
                    // 1. Update Tampilan Dasar (Border/BG)
                    oldCard.className = newCard.className;
                    oldCard.dataset.history = newCard.dataset.history;

                    // 2. Cek Status & Mainkan Suara
                    const newStatus = newCard.dataset.status;
                    checkStatusChange(id, newStatus);
                    oldCard.dataset.status = newStatus;

                    // 3. Update Text Latency & Badge
                    const oldLat = oldCard.querySelector('[id^="latency-val-"]');
                    const newLat = newCard.querySelector('[id^="latency-val-"]');
                    if (oldLat && newLat) oldLat.innerHTML = newLat.innerHTML;

                    const oldBadge = oldCard.querySelector('[id^="badge-"]');
                    const newBadge = newCard.querySelector('[id^="badge-"]');
                    if (oldBadge && newBadge) {
                        oldBadge.innerHTML = newBadge.innerHTML;
                        oldBadge.className = newBadge.className;
                    }

                    const oldDot = oldCard.querySelector('[id^="dot-"]');
                    const newDot = newCard.querySelector('[id^="dot-"]');
                    if (oldDot && newDot) oldDot.className = newDot.className;

                    // Update Router LEDs (ambil dari div router-leds)
                    const oldRouterLeds = oldCard.querySelector('.router-leds');
                    const newRouterLeds = newCard.querySelector('.router-leds');
                    if (oldRouterLeds && newRouterLeds) {
                        oldRouterLeds.innerHTML = newRouterLeds.innerHTML;
                    }

                    // Update Access Point LED (Single LED)
                    const oldApLed = oldCard.querySelector('.ap-led');
                    const newApLed = newCard.querySelector('.ap-led');
                    if (oldApLed && newApLed) {
                        oldApLed.className = newApLed.className;
                    }

                    // 5. Update Warning Cabang
                    const oldWarn = document.getElementById('badge-hidden-' + id);
                    const newWarn = doc.getElementById('badge-hidden-' + id);
                    if (oldWarn && newWarn) oldWarn.className = newWarn.className;

                    // Update Tooltip Data Realtime
                    oldCard.dataset.latency = newCard.dataset.latency;
                    if (hoveredCardId === id) {
                        // Update chart tooltip kalau sedang dihover
                        try {
                            const history = JSON.parse(newCard.dataset.history || '[]');
                            chart.updateSeries([{ data: history }]);
                            const ttLatencyEl = document.getElementById('tt-latency');
                            if (ttLatencyEl) ttLatencyEl.textContent = (newCard.dataset.latency || 0) + ' ms';
                        } catch (err) { }
                    }
                }
            });

            updateStatusCounters();
            if (!isDragging) requestAnimationFrame(drawTreeLines);
        })
        .catch(err => console.error("Gagal refresh:", err));
}
// Fungsi untuk update status counters secara real-time
function updateStatusCounters() {
    const cards = document.querySelectorAll('.monitor-card');
    let total = 0, up = 0, warning = 0, down = 0;

    cards.forEach(card => {
        total++;
        const status = card.dataset.status;
        if (status === 'connected') up++;
        else if (status === 'unstable') warning++;
        else if (status === 'disconnected') down++;
    });

    // Update DOM
    const counterTotal = document.getElementById('counter-total');
    const counterUp = document.getElementById('counter-up');
    const counterWarning = document.getElementById('counter-warning');
    const counterDown = document.getElementById('counter-down');

    if (counterTotal) counterTotal.textContent = total;
    if (counterUp) counterUp.textContent = up;
    if (counterWarning) counterWarning.textContent = warning;
    if (counterDown) counterDown.textContent = down;
}

// Set Interval refresh (500ms = 0.5 detik)
setInterval(refreshData, 50);

// Init awal
window.onload = () => { setTimeout(drawTreeLines, 100); };
window.onresize = () => setTimeout(drawTreeLines, 100);

// ==========================================
// 6. TOOLTIP LOGIC (Updated untuk monitor-hover-tooltip component)
// ==========================================
const tooltip = document.getElementById('chart-tooltip');
const ttStation = document.getElementById('tt-station');
const ttName = document.getElementById('tt-name');
const ttType = document.getElementById('tt-type');
const ttIp = document.getElementById('tt-ip');
const ttLatency = document.getElementById('tt-latency');

document.body.addEventListener('mouseover', e => {
    const card = e.target.closest('.monitor-card');
    if (!card) return;

    // Track card yang di-hover
    hoveredCardId = card.dataset.id;

    // Update tooltip content
    if (ttStation) ttStation.textContent = card.dataset.station || '-';
    if (ttName) ttName.textContent = card.dataset.name || '-';
    if (ttType) ttType.textContent = card.dataset.type || '-';
    if (ttIp) ttIp.textContent = card.dataset.ip || '-';
    if (ttLatency) ttLatency.textContent = (card.dataset.latency || 0) + ' ms';

    // Update chart
    try {
        const history = JSON.parse(card.dataset.history || '[]');
        chart.updateSeries([{ data: history }]);
    } catch (err) {
        chart.updateSeries([{ data: [] }]);
    }

    // Show tooltip
    if (tooltip) tooltip.classList.remove('hidden');
});

document.body.addEventListener('mousemove', e => {
    if (!tooltip || tooltip.classList.contains('hidden')) return;
    tooltip.style.left = (e.clientX + 12) + 'px';
    tooltip.style.top = (e.clientY + 12) + 'px';
});

document.body.addEventListener('mouseout', e => {
    if (e.target.closest('.monitor-card') && tooltip) {
        tooltip.classList.add('hidden');
        hoveredCardId = null; // Reset hovered card
    }
});

// ==========================================
// 7. WIB CLOCK (Optional - jika ada elemen dateText/timeText)
// ==========================================
function updateWIB() {
    const dateEl = document.getElementById('dateText');
    const timeEl = document.getElementById('timeText');
    if (!dateEl || !timeEl) return;

    const now = new Date();

    const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta',
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });

    const timeFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });

    dateEl.textContent = dateFormatter.format(now);
    timeEl.textContent = timeFormatter.format(now);
}

// Jalankan clock jika elemen ada
if (document.getElementById('dateText') && document.getElementById('timeText')) {
    updateWIB();
    setInterval(updateWIB, 1000);
}