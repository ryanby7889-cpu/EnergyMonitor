<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

/*
|--------------------------------------------------------------------------
| Latest Sensor
|--------------------------------------------------------------------------
*/

$sqlSensor = "
SELECT *
FROM sensor_data
ORDER BY id DESC
LIMIT 1
";

$rSensor = $conn->query($sqlSensor);

if (!$rSensor) {

    echo json_encode([
        "alarm" => false,
        "message" => $conn->error
    ]);

    exit;
}

if ($rSensor->num_rows == 0) {

    echo json_encode([
        "alarm" => false,
        "message" => "No Sensor Data"
    ]);

    exit;
}

$sensor = $rSensor->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Alarm Setting
|--------------------------------------------------------------------------
*/

$sqlSetting = "
SELECT *
FROM alarm_settings
LIMIT 1
";

$rSetting = $conn->query($sqlSetting);

if (!$rSetting) {

    echo json_encode([
        "alarm" => false,
        "message" => $conn->error
    ]);

    exit;
}

$setting = $rSetting->fetch_assoc();

/*
|--------------------------------------------------------------------------
| Alarm Engine
|--------------------------------------------------------------------------
*/

$alarm = false;
$type = "";
$message = "";
$value = 0;

if ($sensor["power"] > $setting["power_limit"]) {

    $alarm = true;
    $type = "POWER";
    $value = $sensor["power"];
    $message = "Power melebihi batas";

}
elseif ($sensor["voltage"] < $setting["voltage_min"]) {

    $alarm = true;
    $type = "LOW VOLTAGE";
    $value = $sensor["voltage"];
    $message = "Voltage terlalu rendah";

}
elseif ($sensor["voltage"] > $setting["voltage_max"]) {

    $alarm = true;
    $type = "HIGH VOLTAGE";
    $value = $sensor["voltage"];
    $message = "Voltage terlalu tinggi";

}
elseif ($sensor["current"] > $setting["current_limit"]) {

    $alarm = true;
    $type = "CURRENT";
    $value = $sensor["current"];
    $message = "Current melebihi batas";

}
elseif (
    $sensor["power"] >= 10 &&
    $sensor["pf"] < $setting["pf_min"]
) {

    $alarm = true;
    $type = "POWER FACTOR";
    $value = $sensor["pf"];
    $message = "Power Factor rendah";

}

echo json_encode([

    "alarm" => $alarm,

    "type" => $type,

    "message" => $message,

    "value" => $value,

    "power" => $sensor["power"],

    "voltage" => $sensor["voltage"],

    "current" => $sensor["current"],

    "pf" => $sensor["pf"],

    "limit" => $setting["power_limit"]

]);

$conn->close();