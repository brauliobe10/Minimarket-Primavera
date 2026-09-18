<?php
// Redirige al nuevo panel de administración
$qs = $_SERVER['QUERY_STRING'] ? '?sec=productos&' . $_SERVER['QUERY_STRING'] : '?sec=productos';
header("Location: " . BASE_URL . "/views/admin/index.php{$qs}");
exit;
