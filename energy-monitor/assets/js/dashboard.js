const $ = id => document.getElementById(id);
let chart;

function set(id, value) {
  const el = $(id);
  if (el) el.textContent = value;
}

function money(value) {
  return 'Rp ' + Number(value || 0).toLocaleString('id-ID', { maximumFractionDigits: 0 });
}

function setOnline(online) {
  set('status', online ? 'ONLINE' : 'OFFLINE');
  const status = $('status');
  if (status) status.className = 'value ' + (online ? 'text-success' : 'text-danger');
  const nav = $('navStatus');
  if (nav) {
    nav.className = 'badge ' + (online ? 'text-bg-success' : 'text-bg-danger');
    nav.textContent = online ? 'ONLINE' : 'OFFLINE';
  }
}

async function loadRealtime() {
  try {
    const response = await fetch('../api/latest.php?t=' + Date.now(), { cache: 'no-store' });
    if (!response.ok) throw new Error('latest.php HTTP ' + response.status);
    const data = await response.json();
    const online = data.device_status === 'ONLINE';

    set('voltage', Number(data.voltage || 0).toFixed(1) + ' V');
    set('current', Number(data.current || 0).toFixed(3) + ' A');
    set('power', Number(data.power || 0).toFixed(1) + ' W');
    set('energy', Number(data.energy || 0).toFixed(2) + ' kWh');
    set('frequency', Number(data.frequency || 0).toFixed(1) + ' Hz');
    set('pf', Number(data.pf || 0).toFixed(2));
    set('last_update', data.created_at || '-');
    setOnline(online);

    await Promise.all([loadHistory(), loadStats(), loadActiveAlarms()]);
  } catch (error) {
    console.error('Realtime error:', error);
    setOnline(false);
  }
}

async function loadHistory() {
  try {
    const response = await fetch('../api/history.php?limit=60&t=' + Date.now(), { cache: 'no-store' });
    if (!response.ok) throw new Error('history.php HTTP ' + response.status);
    const data = await response.json();
    if (!chart) return;

    chart.data.labels = data.map(item => {
      const date = new Date(String(item.created_at).replace(' ', 'T'));
      return Number.isNaN(date.getTime()) ? item.created_at : date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    });
    chart.data.datasets[0].data = data.map(item => Number(item.power || 0));
    chart.update('none');
  } catch (error) {
    console.error('History error:', error);
  }
}

async function loadStats() {
  try {
    const response = await fetch('../api/stats.php?t=' + Date.now(), { cache: 'no-store' });
    if (!response.ok) throw new Error('stats.php HTTP ' + response.status);
    const data = await response.json();
    set('today_cost', money(data.today_cost));
    set('today_kwh', Number(data.today_kwh || 0).toFixed(2));
    set('alarm_today', Number(data.alarm_today || 0));
    set('alarm_active', Number(data.active_alarm || 0));
    set('alarm_ack', Number(data.alarm_ack || 0));
  } catch (error) {
    console.error('Stats error:', error);
  }
}

async function loadActiveAlarms() {
  const list = $('activeAlarmList');
  if (!list) return;
  try {
    const response = await fetch('../api/alarm_status.php?t=' + Date.now(), { cache: 'no-store' });
    if (!response.ok) throw new Error('alarm_status.php HTTP ' + response.status);
    const data = await response.json();
    if (!data.alarm) {
      list.innerHTML = '<span class="text-secondary">Tidak ada alarm aktif.</span>';
      return;
    }
    list.innerHTML = '<div class="alarm-active p-3 rounded-3 bg-light-subtle">' +
      '<div class="fw-bold text-danger">' + escapeHtml(data.type || 'ALARM') + '</div>' +
      '<div class="small mt-1">' + escapeHtml(data.message || '') + '</div>' +
      '<div class="small mt-2">Value: <strong>' + Number(data.value || 0).toFixed(2) + '</strong></div>' +
      '</div>';
  } catch (error) {
    console.error('Alarm error:', error);
  }
}

function escapeHtml(value) {
  return String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[char]));
}

document.addEventListener('DOMContentLoaded', () => {
  const canvas = $('powerChart');
  if (canvas && typeof Chart !== 'undefined') {
    chart = new Chart(canvas, {
      type: 'line',
      data: { labels: [], datasets: [{ label: 'Power (W)', data: [], tension: 0.3, fill: true }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Watt' } }, x: { title: { display: true, text: 'Waktu' } } }
      }
    });
  }
  loadRealtime();
  setInterval(loadRealtime, 5000);
});
