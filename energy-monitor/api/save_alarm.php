<?php

require_once __DIR__ . '/../config/database.php';

$type = $_POST['type'];
$value = $_POST['value'];
$message = $_POST['message'];

/*
|--------------------------------------------------------------------------
| Cek apakah alarm yang sama masih aktif
|--------------------------------------------------------------------------
*/

$sql = "
SELECT id
FROM alarm_history
WHERE alarm_type=?
AND is_active=1
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$type);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    $insert="
    INSERT INTO alarm_history
    (
        device_id,
        alarm_type,
        alarm_value,
        message,
        status,
        is_active
    )
    VALUES
    (
        1,
        ?,
        ?,
        ?,
        'ACTIVE',
        1
    )
    ";

    $save=$conn->prepare($insert);

    $save->bind_param(
        "sds",
        $type,
        $value,
        $message
    );

    $save->execute();

}

echo "OK";