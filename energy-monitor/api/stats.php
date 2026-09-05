<?php
header('Content-Type: application/json; charset=utf-8'); require_once __DIR__.'/../config/database.php';
$q=$conn->query("SELECT COALESCE(MAX(energy),0) energy_total, COALESCE(SUM(CASE WHEN DATE(created_at)=CURDATE() THEN GREATEST(power,0) ELSE 0 END),0) power_samples_today, COUNT(CASE WHEN DATE(created_at)=CURDATE() THEN 1 END) samples_today FROM sensor_data");
$s=$q?$q->fetch_assoc():[]; $rate=1500.0; $today=$conn->query("SELECT MIN(energy) e0, MAX(energy) e1 FROM sensor_data WHERE DATE(created_at)=CURDATE()")->fetch_assoc(); $delta=max(0,(float)($today['e1']??0)-(float)($today['e0']??0));
$active=(int)$conn->query("SELECT COUNT(*) n FROM alarm_history WHERE is_active=1")->fetch_assoc()['n']; $todayAlarm=(int)$conn->query("SELECT COUNT(*) n FROM alarm_history WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['n']; $ack=(int)$conn->query("SELECT COUNT(*) n FROM alarm_history WHERE DATE(created_at)=CURDATE() AND status='ACKNOWLEDGED'")->fetch_assoc()['n'];
echo json_encode(['energy_total'=>(float)$s['energy_total'],'today_kwh'=>$delta,'today_cost'=>$delta*$rate,'active_alarm'=>$active,'alarm_today'=>$todayAlarm,'alarm_ack'=>$ack,'rate'=>$rate]);
