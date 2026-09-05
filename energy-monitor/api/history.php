<?php
header('Content-Type: application/json; charset=utf-8'); require_once __DIR__.'/../config/database.php';
$limit=isset($_GET['limit'])?max(10,min(240,(int)$_GET['limit'])):60;
$stmt=$conn->prepare("SELECT created_at,power FROM sensor_data ORDER BY id DESC LIMIT ?"); $stmt->bind_param('i',$limit); $stmt->execute(); $r=$stmt->get_result(); $data=[]; while($x=$r->fetch_assoc())$data[]=$x; echo json_encode(array_reverse($data));
