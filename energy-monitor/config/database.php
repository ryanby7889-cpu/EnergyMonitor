<?php
$host = getenv('EM_DB_HOST') ?: 'localhost';
$user = getenv('EM_DB_USER') ?: 'root';
$pass = getenv('EM_DB_PASS') ?: '';
$dbname = getenv('EM_DB_NAME') ?: 'energy_monitor';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) { http_response_code(500); die('Database connection failed.'); }
$conn->set_charset('utf8mb4');
