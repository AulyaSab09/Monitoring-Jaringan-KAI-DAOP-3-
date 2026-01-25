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
const zoomInput = document.getElementById('zoom-input');

function updateTransform() {
    viewport.style.transform = `translate(${panX}px, ${panY}px) scale(${currentZoom})`;
    if (zoomInput) zoomInput.value = Math.round(currentZoom * 100);
    if (zoomLabel) zoomLabel.innerText = Math.round(currentZoom * 100) + '%';
    if (!isDragging) requestAnimationFrame(drawTreeLines);
}

// Function to set zoom manually from input
function setZoom(value) {
    let newZoom = parseInt(value);

    // Clamp values between 10% and 200%
    if (isNaN(newZoom)) newZoom = 100;
    if (newZoom < 10) newZoom = 10;
    if (newZoom > 200) newZoom = 200;

    currentZoom = newZoom / 100;
    updateTransform();
}

container.addEventListener('mousedown', e => {
    if (e.target.closest('button, a, input')) return; // Ignore clicks inputs too
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
window.setZoom = setZoom;
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

                    // ============================================================
                    // 4. FIX UTAMA: UPDATE VISUAL LITERAL DEVICE (Router/AP LEDs)
                    // ============================================================

                    // Update Router LEDs (Kita copy satu blok div router-leds)
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

                    // Update Latency Display Container Class (Fix bug: update border/bg latency box)
                    const oldLatBox = oldCard.querySelector('.latency-display');
                    const newLatBox = newCard.querySelector('.latency-display');
                    if (oldLatBox && newLatBox) {
                        oldLatBox.className = newLatBox.className;
                    }
                    // ============================================================

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
setInterval(refreshData, 1000);

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

// Kotak Bawah Down Alert

/**
 * 1. FUNGSI UPDATE ANTREAN (Last In, First Out)
 * Mendeteksi device dengan status 'disconnected' dan mengurutkannya ke antrean bawah.
 */
function updateDownQueue() {
    const container = document.getElementById('down-devices-list');
    if (!container) return;

    // Ambil semua monitor-card yang statusnya disconnected
    const downCards = Array.from(document.querySelectorAll('.monitor-card[data-status="disconnected"]'));

    // Jika tidak ada yang down, tampilkan pesan aman
    if (downCards.length === 0) {
        container.innerHTML = `
            <div id="no-down-message" class="w-full py-4 text-center bg-gray-100/50 rounded-xl border-2 border-dashed border-gray-200">
                <p class="text-gray-400 text-xs font-bold italic">Sistem Aman: Semua perangkat dalam kondisi normal.</p>
            </div>`;
        return;
    }

    // Hapus placeholder pesan aman jika ada device yang down
    const placeholder = document.getElementById('no-down-message');
    if (placeholder) placeholder.remove();

    // Urutkan: Yang baru saja DOWN (berdasarkan atribut data-down-since) diletakkan paling depan
    downCards.sort((a, b) => {
        return new Date(b.getAttribute('data-down-since')) - new Date(a.getAttribute('data-down-since'));
    });

    // Identifikasi ID perangkat yang saat ini ada di antrean visual
    const existingIds = Array.from(container.querySelectorAll('.down-alert-card')).map(el => el.getAttribute('data-alert-id'));
    const currentDownIds = downCards.map(card => card.getAttribute('data-id'));

    // A. HAPUS OTOMATIS: Jika perangkat sudah UP, hilangkan dari antrean bawah
    existingIds.forEach(id => {
        if (!currentDownIds.includes(id)) {
            const el = container.querySelector(`.down-alert-card[data-alert-id="${id}"]`);
            if (el) el.remove();
        }
    });

    // B. TAMBAH/UPDATE: Masukkan perangkat baru ke posisi TERKIRI (Index 0)
    downCards.forEach((card) => {
        const id = card.getAttribute('data-id');
        const name = card.querySelector('.device-title').innerText;
        const ip = card.getAttribute('data-ip');
        const downSince = card.getAttribute('data-down-since') || new Date().toISOString();

        let alertBox = container.querySelector(`.down-alert-card[data-alert-id="${id}"]`);

        if (!alertBox) {
            // Format jam mulai down (e.g. 14:30 WIB)
            const downDate = new Date(downSince);
            const timeString = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                timeZone: 'Asia/Jakarta'
            }).format(downDate);

            // Template kotak antrean yang sudah diselaraskan
            const template = `
    <div class="down-alert-card bg-white border-l-8 border-red-600 shadow-lg p-3 rounded-xl flex-none" 
         style="min-width: 300px; width: auto; max-width: fit-content;" 
         data-alert-id="${id}">
        
        <div class="flex items-center justify-between gap-4">
            
            <div class="flex-none whitespace-nowrap">
                <div class="device-name font-black text-slate-900 leading-none" 
                     style="font-size: 22px !important; display: block !important; margin: 0 !important;">
                    ${name}
                </div>
                <div class="text-[10px] text-red-500 font-bold italic mt-1">
                    Down: ${timeString} WIB
                </div>
            </div>

            <div class="duration-timer bg-red-700 text-white py-1.5 px-3 rounded-lg text-center font-mono font-black text-lg shadow-inner" 
                 data-start-time="${downSince}">
                00j 00m 00d
            </div>

            <div class="flex-none">
                <div class="w-2.5 h-2.5 bg-red-600 rounded-full animate-ping"></div>
            </div>
            
        </div> 
    </div>
`;

            // 'afterbegin' memastikan elemen baru masuk ke posisi paling kiri
            container.insertAdjacentHTML('afterbegin', template);
        }
    });
}

/**
 * 2. FUNGSI TIMER REAL-TIME
 * Menghitung selisih waktu dari saat mulai DOWN sampai detik ini.
 */
function runTimers() {
    document.querySelectorAll('.duration-timer').forEach(timer => {
        const startTime = new Date(timer.getAttribute('data-start-time'));
        const now = new Date();
        const diff = Math.abs(now - startTime);

        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        timer.innerText = `${hours.toString().padStart(2, '0')}j ${minutes.toString().padStart(2, '0')}m ${seconds.toString().padStart(2, '0')}d`;
    });
}

/**
 * 3. INISIALISASI LOOP
 * Jalankan pengecekan antrean dan update timer setiap 1 detik.
 */
setInterval(() => {
    updateDownQueue();
    runTimers();
}, 1000);