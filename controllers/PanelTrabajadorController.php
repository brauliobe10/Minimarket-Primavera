<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

header('Content-Type: application/json');

if (!estaLogueado()) {
    echo json_encode(['ok' => false, 'mensaje' => 'No has iniciado sesión.']);
    exit;
}

sincronizarRoles();

if (!esTrabajador()) {
    echo json_encode(['ok' => false, 'mensaje' => 'No tienes permisos de personal operativo.']);
    exit;
}

$conn = (new Database())->conectar();
$idUsuario = (int)$_SESSION['usuario_id'];

// Obtener ficha de trabajador
$stmtTrab = $conn->prepare("
    SELECT t.*, u.nombres, u.apellidos, u.correo, u.telefono, u.dni
    FROM trabajador t
    JOIN usuario u ON t.id_usuario = u.id_usuario
    WHERE t.id_usuario = ?
    LIMIT 1
");
$stmtTrab->execute([$idUsuario]);
$trabajador = $stmtTrab->fetch(PDO::FETCH_ASSOC);

// Si es admin pero aún no está en tabla trabajador, crearle una ficha virtual
if (!$trabajador && esAdmin()) {
    $u = $conn->prepare("SELECT nombres, apellidos, correo, telefono, dni FROM usuario WHERE id_usuario = ?");
    $u->execute([$idUsuario]);
    $usr = $u->fetch(PDO::FETCH_ASSOC);
    $trabajador = [
        'id_trabajador' => 0,
        'id_usuario'    => $idUsuario,
        'cargo'         => 'Administrador',
        'turno'         => 'Completo',
        'sueldo'        => 0,
        'estado'        => 1,
        'nombres'       => $usr['nombres'] ?? 'Admin',
        'apellidos'     => $usr['apellidos'] ?? '',
        'correo'        => $usr['correo'] ?? '',
        'telefono'      => $usr['telefono'] ?? '',
        'dni'           => $usr['dni'] ?? ''
    ];
}

if (!$trabajador && !esAdmin()) {
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró tu ficha de colaborador activo.']);
    exit;
}

// ──────────────────────────────────────────────
// POST: OPERACIONES
// ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Petición inválida.']);
        exit;
    }

    $accion = trim($d['accion'] ?? '');

    try {
        // ── 1. DESPACHO: ACTUALIZAR ESTADO DE PEDIDO ──
        if ($accion === 'actualizar_estado_pedido') {
            $idPedido = (int)($d['id_pedido'] ?? 0);
            $nuevoEstado = trim($d['nuevo_estado'] ?? '');

            $estadosValidos = ['Pendiente', 'En preparación', 'Listo para entrega', 'Entregado', 'Cancelado'];
            if (!in_array($nuevoEstado, $estadosValidos, true)) {
                echo json_encode(['ok' => false, 'mensaje' => 'Estado no válido.']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?");
            $stmt->execute([$nuevoEstado, $idPedido]);

            // Si pasa a 'Listo para entrega', actualizar delivery si existe
            if ($nuevoEstado === 'Listo para entrega') {
                $conn->prepare("
                    UPDATE delivery 
                    SET estado_delivery = 'Asignado' 
                    WHERE id_pedido = ? AND estado_delivery = 'Pendiente'
                ")->execute([$idPedido]);
            }

            echo json_encode(['ok' => true, 'mensaje' => "Pedido #{$idPedido} actualizado a: {$nuevoEstado}"]);
            exit;

        // ── 2. ALMACÉN: REGISTRAR MOVIMIENTO DE STOCK ──
        } elseif ($accion === 'movimiento_stock') {
            $idProducto = (int)($d['id_producto'] ?? 0);
            $tipo       = strtoupper(trim($d['tipo'] ?? '')); // ENTRADA o SALIDA
            $cantidad   = (int)($d['cantidad'] ?? 0);
            $motivo     = trim($d['motivo'] ?? 'Ajuste de inventario');

            if ($idProducto <= 0 || $cantidad <= 0 || !in_array($tipo, ['ENTRADA', 'SALIDA'], true)) {
                echo json_encode(['ok' => false, 'mensaje' => 'Datos de stock inválidos.']);
                exit;
            }

            $conn->beginTransaction();

            $pStmt = $conn->prepare("SELECT stock_actual, nombre FROM producto WHERE id_producto = ? FOR UPDATE");
            $pStmt->execute([$idProducto]);
            $prod = $pStmt->fetch(PDO::FETCH_ASSOC);

            if (!$prod) {
                $conn->rollBack();
                echo json_encode(['ok' => false, 'mensaje' => 'Producto no encontrado.']);
                exit;
            }

            $stockActual = (int)$prod['stock_actual'];
            if ($tipo === 'SALIDA' && $cantidad > $stockActual) {
                $conn->rollBack();
                echo json_encode(['ok' => false, 'mensaje' => "Stock insuficiente. Hay {$stockActual} unidades disponibles."]);
                exit;
            }

            $nuevoStock = ($tipo === 'ENTRADA') ? ($stockActual + $cantidad) : ($stockActual - $cantidad);

            // Actualizar producto
            $conn->prepare("UPDATE producto SET stock_actual = ? WHERE id_producto = ?")->execute([$nuevoStock, $idProducto]);

            // Registrar movimiento
            $conn->prepare("
                INSERT INTO movimiento_stock (id_producto, tipo_movimiento, cantidad, motivo, fecha_movimiento)
                VALUES (?, ?, ?, ?, NOW())
            ")->execute([$idProducto, $tipo, $cantidad, $motivo]);

            $conn->commit();

            echo json_encode([
                'ok' => true,
                'mensaje' => "Stock de '{$prod['nombre']}' actualizado a {$nuevoStock} unidades.",
                'nuevo_stock' => $nuevoStock
            ]);
            exit;

        // ── 3. CAJA RÁPIDA: REGISTRAR VENTA EN MOSTRADOR ──
        } elseif ($accion === 'venta_caja') {
            $items     = $d['items'] ?? [];
            $dniCli    = trim($d['dni_cliente'] ?? '00000000');
            $nomCli    = trim($d['nombre_cliente'] ?? 'Venta Mostrador');
            $metodo    = trim($d['metodo_pago'] ?? 'Efectivo');
            $tipoComp  = trim($d['tipo_comprobante'] ?? 'Boleta');

            if (empty($items) || !is_array($items)) {
                echo json_encode(['ok' => false, 'mensaje' => 'No hay productos en la venta.']);
                exit;
            }

            $conn->beginTransaction();

            $subtotalCalc = 0.0;
            $itemsValidados = [];

            foreach ($items as $it) {
                $idP  = (int)($it['id_producto'] ?? 0);
                $cant = (int)($it['cantidad'] ?? 0);

                if ($idP <= 0 || $cant <= 0) continue;

                $pCheck = $conn->prepare("SELECT id_producto, nombre, precio, stock_actual FROM producto WHERE id_producto = ? FOR UPDATE");
                $pCheck->execute([$idP]);
                $p = $pCheck->fetch(PDO::FETCH_ASSOC);

                if (!$p || $p['stock_actual'] < $cant) {
                    $conn->rollBack();
                    echo json_encode(['ok' => false, 'mensaje' => "Stock insuficiente para {$p['nombre']} (Disponible: {$p['stock_actual']})."]);
                    exit;
                }

                $precioUnit = (float)$p['precio'];
                $subtotalLinea = $precioUnit * $cant;
                $subtotalCalc += $subtotalLinea;

                $itemsValidados[] = [
                    'id_producto' => $idP,
                    'nombre'      => $p['nombre'],
                    'cantidad'    => $cant,
                    'precio'      => $precioUnit,
                    'subtotal'    => $subtotalLinea,
                    'stock_prev'  => (int)$p['stock_actual']
                ];
            }

            $total = $subtotalCalc;
            $igv_calc = round($total * 0.18, 2);
            $totalConIgv = $total + $igv_calc;

            // 1. Crear pedido completado
            $insPed = $conn->prepare("
                INSERT INTO pedido (fecha_pedido, subtotal, descuento, total, estado_pedido, tipo_entrega, id_usuario, dni_cliente, nombre_cliente)
                VALUES (NOW(), ?, 0.00, ?, 'Entregado', 'Recojo en tienda', ?, ?, ?)
            ");
            $insPed->execute([$subtotalCalc, $totalConIgv, $idUsuario, $dniCli, $nomCli]);
            $idPedido = (int)$conn->lastInsertId();

            // 2. Insertar detalles y descontar stock
            $insDet = $conn->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $updStock = $conn->prepare("UPDATE producto SET stock_actual = stock_actual - ? WHERE id_producto = ?");
            $insMov = $conn->prepare("INSERT INTO movimiento_stock (id_producto, tipo_movimiento, cantidad, motivo, fecha_movimiento) VALUES (?, 'SALIDA', ?, 'Venta mostrador POS #$idPedido', NOW())");

            foreach ($itemsValidados as $iv) {
                $insDet->execute([$idPedido, $iv['id_producto'], $iv['cantidad'], $iv['precio'], $iv['subtotal']]);
                $updStock->execute([$iv['cantidad'], $iv['id_producto']]);
                $insMov->execute([$iv['id_producto'], $iv['cantidad']]);
            }

            // 3. Crear comprobante automático
            $serie = ($tipoComp === 'Factura') ? 'F001' : 'B001';
            $numStmt = $conn->prepare("SELECT IFNULL(MAX(CAST(numero AS UNSIGNED)), 0) + 1 FROM comprobante WHERE serie = ?");
            $numStmt->execute([$serie]);
            $proxNumero = (int)$numStmt->fetchColumn();
            $proxNumeroPad = str_pad($proxNumero, 8, '0', STR_PAD_LEFT);

            $subSinIgv = round($total, 2);
            $igv = round($total * 0.18, 2);
            $totalConIgv = $total + $igv;

            $insComp = $conn->prepare("
                INSERT INTO comprobante (id_pedido, tipo_comprobante, serie, numero, fecha_emision, subtotal, igv, total, estado)
                VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, 'Emitido')
            ");
            $insComp->execute([$idPedido, $tipoComp, $serie, $proxNumeroPad, $subSinIgv, $igv, $totalConIgv]);
            $idComprobante = (int)$conn->lastInsertId();

            $conn->commit();

            echo json_encode([
                'ok' => true,
                'mensaje' => "Venta completada. Pedido #{$idPedido} registrado con comprobante {$serie}-{$proxNumeroPad}.",
                'id_pedido' => $idPedido,
                'comprobante' => "{$serie}-{$proxNumeroPad}",
                'total' => number_format($total, 2)
            ]);
            exit;
        }

        echo json_encode(['ok' => false, 'mensaje' => 'Acción no reconocida.']);
        exit;

    } catch (\Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log('[PanelTrabajadorController] ' . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

// ──────────────────────────────────────────────
// GET: DATOS INICIALES PARA EL PANEL
// ──────────────────────────────────────────────
try {
    // 1. Pedidos para Despacho
    $pedidosDespacho = $conn->query("
        SELECT p.id_pedido, p.fecha_pedido, p.total, p.estado_pedido, p.tipo_entrega,
               p.nombre_cliente, p.telefono_cliente, p.dni_cliente,
               d.direccion_entrega, d.referencia, d.estado_delivery,
               r.nombres AS nombre_repartidor
        FROM pedido p
        LEFT JOIN delivery d ON p.id_pedido = d.id_pedido
        LEFT JOIN repartidor r ON d.id_repartidor = r.id_repartidor
        WHERE p.estado_pedido IN ('Pendiente', 'En preparación', 'Listo para entrega')
        ORDER BY FIELD(p.estado_pedido, 'Pendiente', 'En preparación', 'Listo para entrega'), p.fecha_pedido ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($pedidosDespacho)) {
        $idsPedidos = array_column($pedidosDespacho, 'id_pedido');
        $inClause = implode(',', array_map('intval', $idsPedidos));
        $detalles = $conn->query("
            SELECT dp.id_pedido, dp.id_producto, dp.cantidad, dp.precio_unitario, dp.subtotal,
                   pr.nombre AS nombre, pr.imagen
            FROM detalle_pedido dp
            JOIN producto pr ON dp.id_producto = pr.id_producto
            WHERE dp.id_pedido IN ($inClause)
        ")->fetchAll(PDO::FETCH_ASSOC);

        $detallesPorPedido = [];
        foreach ($detalles as $det) {
            $detallesPorPedido[$det['id_pedido']][] = $det;
        }

        foreach ($pedidosDespacho as &$pd) {
            $pd['items'] = $detallesPorPedido[$pd['id_pedido']] ?? [];
        }
    }

    // 2. Inventario para Almacén
    $inventario = $conn->query("
        SELECT p.id_producto, p.nombre, p.precio, p.stock_actual AS stock, p.imagen, p.estado,
               c.nombre AS nombre_categoria
        FROM producto p
        LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
        ORDER BY p.stock_actual ASC, p.nombre ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 3. Movimientos recientes de stock (últimos 30)
    $movimientos = $conn->query("
        SELECT m.id_movimiento, m.tipo_movimiento, m.cantidad, m.motivo, m.fecha_movimiento,
               p.nombre AS nombre_producto
        FROM movimiento_stock m
        JOIN producto p ON m.id_producto = p.id_producto
        ORDER BY m.fecha_movimiento DESC
        LIMIT 30
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Entregas asignadas (si es Repartidor o Admin)
    $entregas = [];
    $stmtRep = $conn->prepare("SELECT id_repartidor FROM repartidor WHERE id_usuario = ?");
    $stmtRep->execute([$idUsuario]);
    $idRepartidor = $stmtRep->fetchColumn();

    if ($idRepartidor || esAdmin()) {
        $sqlEntregas = "
            SELECT d.id_delivery, d.id_pedido, d.direccion_entrega, d.referencia,
                   d.costo_delivery, d.estado_delivery, d.hora_salida, d.hora_entrega,
                   p.nombre_cliente, p.telefono_cliente, p.total, p.estado_pedido
            FROM delivery d
            JOIN pedido p ON d.id_pedido = p.id_pedido
        ";
        if (!esAdmin() && $idRepartidor) {
            $sqlEntregas .= " WHERE d.id_repartidor = " . (int)$idRepartidor;
        }
        $sqlEntregas .= " ORDER BY FIELD(d.estado_delivery, 'En camino', 'Asignado', 'Pendiente', 'Entregado'), d.id_delivery DESC LIMIT 40";
        $entregas = $conn->query($sqlEntregas)->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'ok'               => true,
        'trabajador'       => $trabajador,
        'pedidos_despacho' => $pedidosDespacho,
        'inventario'       => $inventario,
        'movimientos'      => $movimientos,
        'entregas'         => $entregas
    ]);

} catch (\Exception $e) {
    error_log('[PanelTrabajadorController GET] ' . $e->getMessage());
    echo json_encode(['ok' => false, 'mensaje' => 'Error al cargar los datos operativos: ' . $e->getMessage()]);
}
