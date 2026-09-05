<?php

require_once __DIR__.'/../config/database.php';

$type=$_POST['type'] ?? '';

$sql="
UPDATE alarm_history
SET
status='ACKNOWLEDGED',
is_active=0,
acknowledged_at=NOW()
WHERE
alarm_type=?
AND
is_active=1
";

$stmt=$conn->prepare($sql);

$stmt->bind_param("s",$type);

$stmt->execute();

echo "OK";