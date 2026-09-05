<?php

require_once "../config/database.php";

$sql="
SELECT
created_at,
power
FROM sensor_data
ORDER BY id DESC
LIMIT 60
";

$result=$conn->query($sql);

$data=[];

while($row=$result->fetch_assoc())
{
    $data[]=$row;
}

echo json_encode(array_reverse($data));