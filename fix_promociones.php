<?php
require_once __DIR__ . '/config/Database.php';

use Config\Database;

try {
    $db = new Database();
    $conexion = $db->conectar();
    
    // Extend promotions to 2030-12-31
    $sql = "UPDATE promocion SET fecha_fin = '2030-12-31'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    
    echo "Promociones actualizadas correctamente. Filas afectadas: " . $stmt->rowCount() . "\n";
} catch (Exception $e) {
    echo "Error al actualizar promociones: " . $e->getMessage() . "\n";
}
