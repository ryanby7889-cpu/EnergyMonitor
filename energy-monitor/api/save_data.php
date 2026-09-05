<?php

require_once __DIR__ . '/../config/database.php';

$voltage   = $_POST['voltage'] ?? 0;
$current   = $_POST['current'] ?? 0;
$power     = $_POST['power'] ?? 0;
$energy    = $_POST['energy'] ?? 0;
$frequency = $_POST['frequency'] ?? 0;
$pf        = $_POST['pf'] ?? 0;

$sql = "INSERT INTO sensor_data
(
    voltage,
    current,
    power,
    energy,
    frequency,
    pf
)
VALUES
(
    ?, ?, ?, ?, ?, ?
)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare Error : " . $conn->error);
}

$stmt->bind_param(
    "dddddd",
    $voltage,
    $current,
    $power,
    $energy,
    $frequency,
    $pf
);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "ERROR : " . $stmt->error;
}

$stmt->close();
$conn->close();