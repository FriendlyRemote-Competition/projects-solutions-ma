<?php

$file = "table.json";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    header("Content-Type: application/json");
    echo file_get_contents($file);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = file_get_contents("php://input");
    // Debug :
    file_put_contents("debug.txt", $data);
    file_put_contents($file, $data);
    header("Content-Type: application/json");
    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
    exit;
}