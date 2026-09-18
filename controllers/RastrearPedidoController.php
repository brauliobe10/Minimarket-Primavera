<?php
/**
 * Controlador de Rastreo de Pedidos
 * 
 * Permite la búsqueda de pedidos de forma asíncrona mediante un número de DNI.
 * Retorna el estado, total, y detalles básicos del pedido para clientes o invitados.
 * Si el DNI pertenece a un usuario registrado, se devuelve un flag `registrado` en el JSON
 * para que el frontend delegue la cancelación del pedido al sistema de autenticación.
 */
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';
use Config\Database;
use Config\Security;

Security::initSession();
Security::setSecurityHeaders();
header('Content-Type: application/json');

$dni       = trim($_GET['dni']       ?? '');
$id_pedido = (int)($_GET['id_pedido'] ?? 0);

// Validar formato DNI: exactamente 8 dígitos numéricos
if (!Security::validarDNI($dni)) {
    echo json_encode(['success' => false, 'error' => 'Por favor, ingresa un DNI válido (8 dígitos).']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();

    if ($id_pedido > 0) {
        $stmt = $conn->prepare("
            SELECT 
                p.id_pedido, 
                p.fecha_pedido, 
                p.tipo_entrega AS metodo_entrega, 
                mp.nombre_metodo AS metodo_pago, 
                p.total, 
                p.estado_pedido AS estado,
                COALESCE(d.direccion_entrega, 'Recojo en tienda') as direccion
            FROM pedido p
            LEFT JOIN delivery d ON p.id_pedido = d.id_pedido
            LEFT JOIN pago pg ON p.id_pago = pg.id_pago
            LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE p.id_pedido = ? AND (p.dni_cliente = ? OR u.dni = ?)
            ORDER BY p.fecha_pedido DESC
        ");
        $stmt->execute([$id_pedido, $dni, $dni]);
        $pedidos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->prepare("
            SELECT 
                p.id_pedido, 
                p.fecha_pedido, 
                p.tipo_entrega AS metodo_entrega, 
                mp.nombre_metodo AS metodo_pago, 
                p.total, 
                p.estado_pedido AS estado,
                COALESCE(d.direccion_entrega, 'Recojo en tienda') as direccion
            FROM pedido p
            LEFT JOIN delivery d ON p.id_pedido = d.id_pedido
            LEFT JOIN pago pg ON p.id_pago = pg.id_pago
            LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE (p.dni_cliente = ? OR u.dni = ?)
            ORDER BY p.fecha_pedido DESC
        ");
        $stmt->execute([$dni, $dni]);
        $pedidos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (count($pedidos_db) === 0) {
        echo json_encode(['success' => false, 'error' => 'No se encontró ningún pedido con esos datos. Verifica que el DNI sea correcto.']);
        exit;
    }

    $stmtUser = $conn->prepare("SELECT id_usuario FROM usuario WHERE dni = ? LIMIT 1");
    $stmtUser->execute([$dni]);
    $isRegistered = $stmtUser->fetch() ? true : false;

    $pedidos_formateados = [];
    foreach ($pedidos_db as $pedido) {
        $pedidos_formateados[] = [
            'id_pedido' => $pedido['id_pedido'],
            'fecha' => date('d/m/Y h:i A', strtotime($pedido['fecha_pedido'])),
            'metodo_entrega' => ucfirst($pedido['metodo_entrega']),
            'metodo_pago' => ucfirst($pedido['metodo_pago']),
            'total' => number_format((float)$pedido['total'], 2),
            'direccion' => $pedido['direccion'],
            'estado_pedido' => $pedido['estado']
        ];
    }

    echo json_encode([
        'success' => true,
        'registrado' => $isRegistered,
        'pedidos' => $pedidos_formateados
    ]);

} catch (Exception $e) {
    error_log("Error en RastrearPedidoController: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Hubo un problema al consultar la base de datos.']);
}
