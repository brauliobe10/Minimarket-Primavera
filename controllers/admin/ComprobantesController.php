<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();
$conn = (new Database())->conectar();
$comps = $conn->query("SELECT * FROM comprobante ORDER BY fecha_emision DESC")->fetchAll(\PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'comprobantes'=>$comps]);
