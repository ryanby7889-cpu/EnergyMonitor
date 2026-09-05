<?php
require_once '../config/database.php';
include '../layouts/header.php';
include '../layouts/navbar.php';
?>
<div class="container-fluid"><div class="row"><div class="col-lg-2 p-0"><?php include '../layouts/sidebar.php'; ?></div><main class="col-lg-10 p-4">
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div><h3 class="fw-bold mb-1">Energy Dashboard</h3><div class="text-secondary">ESP8266 + PZEM-004T realtime monitoring</div></div><div class="small text-secondary">Last update: <span id="last_update">-</span></div></div>
<div class="row g-3 mb-3">
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label"><i class="bi bi-lightning"></i> Voltage</div><div class="value mt-2" id="voltage">0.0 V</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label"><i class="bi bi-plug"></i> Current</div><div class="value mt-2" id="current">0.000 A</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label"><i class="bi bi-speedometer2"></i> Power</div><div class="value mt-2" id="power">0.0 W</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label"><i class="bi bi-battery-charging"></i> Energy</div><div class="value mt-2" id="energy">0.00 kWh</div></div></div></div>
</div>
<div class="row g-3 mb-3">
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label">Frequency</div><div class="value" id="frequency">0.0 Hz</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label">Power Factor</div><div class="value" id="pf">0.00</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100"><div class="card-body"><div class="muted-label">Estimated Cost Today</div><div class="value" id="today_cost">Rp 0</div><div class="small text-secondary mt-1"><span id="today_kwh">0.00</span> kWh</div></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card kpi shadow-sm h-100" id="statusCard"><div class="card-body"><div class="muted-label">Device Status</div><div class="value" id="status">OFFLINE</div><div class="small text-secondary mt-1">Polling setiap 5 detik</div></div></div></div>
</div>
<div class="row g-3"><div class="col-xl-8"><div class="card shadow-sm h-100"><div class="card-header d-flex justify-content-between align-items-center"><span class="fw-bold">Power History</span><span class="small text-secondary">60 titik terakhir</span></div><div class="card-body"><div class="chart-box"><canvas id="powerChart"></canvas></div></div></div></div>
<div class="col-xl-4"><div class="card shadow-sm mb-3"><div class="card-header fw-bold">Alarm Summary</div><div class="card-body row text-center"><div class="col-4"><div class="muted-label">Today</div><div class="fs-3 fw-bold" id="alarm_today">0</div></div><div class="col-4"><div class="muted-label">Active</div><div class="fs-3 fw-bold text-danger" id="alarm_active">0</div></div><div class="col-4"><div class="muted-label">Ack</div><div class="fs-3 fw-bold text-success" id="alarm_ack">0</div></div></div></div><div class="card shadow-sm"><div class="card-header bg-danger text-white fw-bold">Active Alarm</div><div class="card-body" id="activeAlarmList"><span class="text-secondary">Tidak ada alarm aktif.</span></div></div></div></div>
</main></div></div>
<div class="modal fade" id="alarmModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-danger"><div class="modal-header bg-danger text-white"><h5 class="modal-title">Energy Alarm</h5></div><div class="modal-body"><h4 id="alarm_type">-</h4><p id="alarm_message">-</p><strong>Value: </strong><span id="alarm_value">0</span></div><div class="modal-footer"><button type="button" class="btn btn-success" id="btnAck">Acknowledge</button></div></div></div></div>
<?php include '../layouts/footer.php'; ?>