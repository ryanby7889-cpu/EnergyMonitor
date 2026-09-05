<?php
header('Content-Type: application/json; charset=utf-8'); require_once __DIR__.'/../config/database.php';
$r=$conn->query("SELECT *, TIMESTAMPDIFF(SECOND,created_at,NOW()) seconds_ago FROM sensor_data ORDER BY id DESC LIMIT 1");
if(!$r){http_response_code(500);echo json_encode(['status'=>'ERROR']);exit;} if(!$r->num_rows){echo json_encode(['status'=>'NO_DATA','device_status'=>'OFFLINE']);exit;}
$row=$r->fetch_assoc(); $online=(int)$row['seconds_ago']<=15; $row['device_status']=$online?'ONLINE':'OFFLINE';
if(!$online){foreach(['voltage','current','power','frequency','pf'] as $k)$row[$k]=0;}
echo json_encode($row); $conn->close();
