<?php

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Encryption.php';

use Config\Database;
use Config\Encryption;

// Determinar si el usuario es invitado
$esInvitado = !estaLogueado();

$usuarioDatos = [];
$direcciones  = [];
$tarjetasGuardadas = [];
$carritoItems = [];

if (!$esInvitado) {
    $db   = new Database();
    $conn = $db->conectar();

    // Datos básicos del usuario
    $stmt = $conn->prepare(
        "SELECT nombres, apellidos, telefono, dni
         FROM usuario
         WHERE id_usuario = :id"
    );
    $stmt->execute(['id' => $_SESSION['usuario_id']]);
    $usuarioDatos = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

    // Direcciones del usuario
    $stmtDir = $conn->prepare(
        "SELECT id_direccion, etiqueta, departamento, provincia, distrito,
                direccion, referencia, predeterminada
         FROM direccion
         WHERE id_usuario = :id
         ORDER BY predeterminada DESC, id_direccion ASC"
    );
    $stmtDir->execute(['id' => $_SESSION['usuario_id']]);
    $direcciones = $stmtDir->fetchAll(\PDO::FETCH_ASSOC);

    // Tarjetas guardadas
    $stmtCards = $conn->prepare("
        SELECT id_tarjeta, titular, numero_encriptado, vencimiento_encriptado, marca, ultimos_cuatro 
        FROM tarjeta_guardada 
        WHERE id_usuario = :id
        ORDER BY fecha_creacion DESC
    ");
    $stmtCards->execute(['id' => $_SESSION['usuario_id']]);
    $tarjetasRaw = $stmtCards->fetchAll(\PDO::FETCH_ASSOC);
    
    foreach ($tarjetasRaw as $t) {
        $tarjetasGuardadas[] = [
            'id_tarjeta' => $t['id_tarjeta'],
            'titular' => $t['titular'],
            'marca' => $t['marca'],
            'ultimos_cuatro' => $t['ultimos_cuatro'],
            'numero' => Encryption::decrypt($t['numero_encriptado']),
            'vencimiento' => Encryption::decrypt($t['vencimiento_encriptado'])
        ];
    }

    // Carrito de la base de datos
    require_once __DIR__ . '/../models/CarritoModel.php';
    $carritoModel = new \Models\CarritoModel($conn);
    $carritoItems = $carritoModel->obtenerItemsCarrito($_SESSION['usuario_id']);
}
