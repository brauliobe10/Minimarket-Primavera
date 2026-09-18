<?php
/**
 * Controlador Principal de Procesamiento de Pedidos
 * 
 * Este archivo maneja toda la lógica transaccional de compras en el sistema.
 * Es responsable de:
 * 1. Validar el carrito y los datos del usuario (invitado o registrado).
 * 2. Calcular los montos (Subtotales, IGV, Envío y Total).
 * 3. Ejecutar la transacción en la Base de Datos (Insertar Pago, Comprobante, Pedido, Delivery, Detalles de Pedido).
 * 4. Actualizar el stock de los productos.
 * 5. Notificar por correo al cliente.
 *
 * Utiliza PDO Transactions para garantizar la integridad referencial (ACID).
 */
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');
ob_start();

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Encryption.php';

use Config\Database;
use Config\Encryption;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$items = $input['items'] ?? [];
if (!$input) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos']);
    exit;
}

$esInvitado = !estaLogueado();
$idUsuario  = ($esInvitado || !isset($_SESSION['usuario_id'])) ? null : (int) $_SESSION['usuario_id'];
$tipoEntrega = $input['tipo_entrega'] ?? 'domicilio';    // domicilio | tienda
$metodoPago  = $input['metodo_pago']  ?? 'efectivo';      // efectivo | tarjeta | yape
$items       = $input['items']        ?? [];
$subtotalOrig = (float) ($input['subtotal_original'] ?? 0);
$subtotalDesc = (float) ($input['subtotal_descuento'] ?? 0);
$igv          = (float) ($input['igv'] ?? 0);
$envio        = (float) ($input['envio'] ?? 0);
$total        = (float) ($input['total'] ?? 0);
$idDireccion  = isset($input['id_direccion']) ? (int)$input['id_direccion'] : null;
$montoEfectivo  = isset($input['monto_efectivo'])  ? (float)$input['monto_efectivo']  : null;
$vueltoEfectivo = isset($input['vuelto_efectivo']) ? (float)$input['vuelto_efectivo'] : null;
$tarjetaDatos   = $input['tarjeta_datos'] ?? null;

// Datos del invitado (vendrán en el payload si es invitado)
$guestDni      = trim($input['guest_dni']      ?? '');
$guestNombre   = trim($input['guest_nombre']   ?? '');
$guestApellido = trim($input['guest_apellido'] ?? '');
$guestTelefono = trim($input['guest_telefono'] ?? '');
$guestCorreo   = trim($input['guest_correo']   ?? '');
$guestDireccion = trim($input['guest_direccion'] ?? '');
$guestDistrito  = trim($input['guest_distrito']  ?? '');
$guestReferencia = trim($input['guest_referencia'] ?? '');

if (empty($items)) {
    echo json_encode(['ok' => false, 'mensaje' => 'El carrito está vacío']);
    exit;
}

// Mapeo método de pago → id_metodo_pago
$metodosMap = ['efectivo' => 1, 'tarjeta' => 2, 'yape' => 3];
$idMetodoPago = $metodosMap[$metodoPago] ?? 1;

$descuento = round($subtotalOrig - $subtotalDesc, 2);

try {
    $db   = new Database();
    $conn = $db->conectar();

    /* ─── -1. VALIDACIÓN DE PEDIDO PENDIENTE PREVIO ──────────── */
    if ($esInvitado && !empty($guestDni)) {
        $stmtCheck = $conn->prepare("SELECT id_pedido FROM pedido WHERE dni_cliente = ? AND estado_pedido IN ('Pendiente', 'En preparación', 'En camino', 'Asignado') LIMIT 1");
        $stmtCheck->execute([$guestDni]);
    } elseif (!$esInvitado && $idUsuario) {
        $stmtCheck = $conn->prepare("SELECT id_pedido FROM pedido WHERE id_usuario = ? AND estado_pedido IN ('Pendiente', 'En preparación', 'En camino', 'Asignado') LIMIT 1");
        $stmtCheck->execute([$idUsuario]);
    }
    
    if (isset($stmtCheck) && $stmtCheck->fetch()) {
        echo json_encode(['ok' => false, 'mensaje' => 'Ya tienes un pedido activo en curso. Por favor espera a que se entregue o cancele para realizar uno nuevo.']);
        exit;
    }

    $conn->beginTransaction();

    /* ─── 0. VALIDACIÓN DE STOCK EN BASE DE DATOS ────────────── */
    foreach ($items as $item) {
        $idProd = (int) ($item['id'] ?? 0);
        $cant   = (int) ($item['cantidad'] ?? 0);

        // Log stock check for product
        error_log("Checking product ID {$idProd} with quantity {$cant}");
        $stmtCheck = $conn->prepare("SELECT stock_actual, nombre FROM producto WHERE id_producto = ?");
        $stmtCheck->execute([$idProd]);
        $prod = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        error_log("Result for ID {$idProd}: " . print_r($prod, true));

        if (!$prod) {
            $conn->rollBack();
            echo json_encode(['ok' => false, 'mensaje' => 'Uno de los productos seleccionados no está disponible.']);
            exit;
        }

        $stockActualBD = (int) $prod['stock_actual'];
        if ($stockActualBD <= 0) {
            $conn->rollBack();
            echo json_encode([
                'ok' => false,
                'mensaje' => "El producto \"{$prod['nombre']}\" está agotado."
            ]);
            exit;
        }

        if ($cant > $stockActualBD) {
            $conn->rollBack();
            echo json_encode([
                'ok' => false,
                'mensaje' => "Stock insuficiente para \"{$prod['nombre']}\". Solo quedan {$stockActualBD} unidad(es) disponible(s)."
            ]);
            exit;
        }
    }

    /* ─── 1. CARRITO (solo usuarios registrados - Regla 2) ────────── */
    $idCarrito = null;
    if (!$esInvitado && $idUsuario) {
        require_once __DIR__ . '/../models/CarritoModel.php';
        $carritoModel = new \Models\CarritoModel($conn);

        // Obtener carrito ACTIVO del usuario
        $idCarrito = $carritoModel->obtenerOCrearCarritoActivo($idUsuario);

        // Asegurar que el detalle del carrito coincida con los ítems comprados
        $carritoModel->sincronizarItems($idUsuario, $items);

        // Cambiar estado a COMPLETADO y crear automáticamente un nuevo carrito ACTIVO vacío para futuras compras
        $carritoModel->finalizarCarritoActivo($idUsuario);
    }

    /* ─── 3. PAGO ──────────────────────────────────────────────── */
    /* ─── 3. PAGO ──────────────────────────────────────────────── */
    if ($metodoPago === 'efectivo') {
        $conn->prepare(
            "INSERT INTO pago (monto, monto_recibido, vuelto, estado_pago, id_metodo_pago)
             VALUES (?, ?, ?, 'Completado', ?)"
        )->execute([$total, $montoEfectivo, $vueltoEfectivo, $idMetodoPago]);
    } else {
        // Para tarjeta y yape solo se inserta el monto y el método de pago
        $conn->prepare(
            "INSERT INTO pago (monto, estado_pago, id_metodo_pago)
             VALUES (?, 'Completado', ?)"
        )->execute([$total, $idMetodoPago]);
    }
    $idPago = (int) $conn->lastInsertId();

    /* ─── 4. PEDIDO ────────────────────────────────────────────── */
    $conn->prepare(
        "INSERT INTO pedido
         (id_usuario, subtotal, descuento, total, estado_pedido, tipo_entrega, id_pago, dni_cliente, nombre_cliente, telefono_cliente, correo_cliente)
         VALUES (?, ?, ?, ?, 'Pendiente', ?, ?, ?, ?, ?, ?)"
    )->execute([
        $idUsuario,   // NULL para invitados -> la columna lo permite
        round($subtotalDesc, 2),
        $descuento,
        $total,
        $tipoEntrega === 'tienda' ? 'Recojo en tienda' : 'Delivery',
        $idPago,
        $esInvitado ? $guestDni : null,
        $esInvitado ? trim("{$guestNombre} {$guestApellido}") : null,
        $esInvitado ? $guestTelefono : null,
        $esInvitado ? $guestCorreo : null
    ]);
    $idPedido = (int) $conn->lastInsertId();

    /* ─── 5. DETALLE_PEDIDO + MOVIMIENTO_STOCK ─────────────────── */
    $stmtDP  = $conn->prepare(
        "INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmtMS  = $conn->prepare(
        "INSERT INTO movimiento_stock (id_producto, tipo_movimiento, cantidad, motivo)
         VALUES (?, 'SALIDA', ?, ?)"
    );
    $stmtStock = $conn->prepare(
        "UPDATE Producto SET stock_actual = stock_actual - ? WHERE id_producto = ?"
    );

    foreach ($items as $item) {
        $idProd   = (int)   $item['id'];
        $cant     = (int)   $item['cantidad'];
        $precio   = round((float) $item['precio'], 2);
        $subtotal = round($precio * $cant, 2);

        $stmtDP->execute([$idPedido, $idProd, $cant, $precio, $subtotal]);
        $stmtMS->execute([$idProd, $cant, "Pedido #{$idPedido}"]);
        $stmtStock->execute([$cant, $idProd]);
    }

    /* ─── 6. DELIVERY automático si es domicilio ───────────────── */
    if ($tipoEntrega === 'domicilio') {
        if ($esInvitado) {
            // Invitado: usar datos del formulario de contacto
            $dirEntrega = ($guestDireccion ? $guestDireccion . ', ' : '') . ($guestDistrito ?: 'Sin dirección');
            // Guardar nombre/teléfono del invitado en referencia para el admin
            $refInvitado = trim(
                ($guestNombre || $guestApellido ? "Nombre: {$guestNombre} {$guestApellido}" : '') .
                ($guestTelefono ? " | Tel: {$guestTelefono}" : '') .
                ($guestCorreo   ? " | Email: {$guestCorreo}" : '') .
                ($guestReferencia ? " | Ref: {$guestReferencia}" : '')
            );
            $referencia = $refInvitado ?: 'Pedido de invitado';
        } else {
            // Usuario logueado: buscar su dirección guardada
            $stmtDir = $conn->prepare("
                SELECT CONCAT(d.direccion, ', ', d.distrito, ', ', d.provincia) AS dir_completa,
                       d.referencia
                FROM direccion d
                WHERE d.id_usuario = ?
                " . ($idDireccion ? "AND d.id_direccion = ?" : "AND d.predeterminada = 1") . "
                LIMIT 1
            ");
            $idDireccion
                ? $stmtDir->execute([$idUsuario, $idDireccion])
                : $stmtDir->execute([$idUsuario]);
            $dirData = $stmtDir->fetch(\PDO::FETCH_ASSOC);

            $dirEntrega = $dirData['dir_completa'] ?? 'Sin dirección especificada';
            $referencia = $dirData['referencia'] ?? null;
        }

        $conn->prepare("
            INSERT INTO delivery (direccion_entrega, referencia, costo_delivery, estado_delivery, id_pedido)
            VALUES (?, ?, 5.00, 'Pendiente', ?)
        ")->execute([$dirEntrega, $referencia, $idPedido]);
    }

    /* ─── 7. COMPROBANTE ───────────────────────────────────────── */
    $serie  = 'B001';
    $stmtN  = $conn->prepare(
        "SELECT MAX(CAST(numero AS UNSIGNED)) AS ultimo FROM comprobante WHERE serie = ?"
    );
    $stmtN->execute([$serie]);
    $ultimo = (int) ($stmtN->fetchColumn() ?? 0);
    $numero = str_pad($ultimo + 1, 8, '0', STR_PAD_LEFT);

    $subtotalSinIgv = round($subtotalDesc, 2);

    $conn->prepare(
        "INSERT INTO comprobante
         (tipo_comprobante, serie, numero, subtotal, igv, total, estado, id_pedido)
         VALUES ('Boleta', ?, ?, ?, ?, ?, 'Emitido', ?)"
    )->execute([
        $serie,
        $numero,
        $subtotalSinIgv,
        round($igv, 2),
        $total,
        $idPedido
    ]);
    $idComprobante = (int) $conn->lastInsertId();

    /* ─── 8. GUARDAR TARJETA ENCRIPTADA (Si aplica) ────────────── */
    if (!$esInvitado && $metodoPago === 'tarjeta' && !empty($tarjetaDatos) && !empty($tarjetaDatos['guardar'])) {
        $idTarjetaGuardada = $tarjetaDatos['id_tarjeta'] ?? null;
        
        if ($idTarjetaGuardada) {
            // Es una tarjeta ya guardada, solo actualizamos los datos editables
            $vencEnc = Encryption::encrypt($tarjetaDatos['vencimiento'] ?? '');
            $titular = $tarjetaDatos['titular'] ?? 'Titular';
            
            $stmtUpdate = $conn->prepare("
                UPDATE tarjeta_guardada 
                SET titular = ?, vencimiento_encriptado = ? 
                WHERE id_tarjeta = ? AND id_usuario = ?
            ");
            $stmtUpdate->execute([$titular, $vencEnc, $idTarjetaGuardada, $idUsuario]);
            
        } else {
            // Es una tarjeta nueva
            $numStr = preg_replace('/\D/', '', $tarjetaDatos['numero'] ?? '');
            if (strlen($numStr) >= 14) {
                $ultimos4 = substr($numStr, -4);
                $numEnc = Encryption::encrypt($numStr);
                $vencEnc = Encryption::encrypt($tarjetaDatos['vencimiento'] ?? '');
                $titular = $tarjetaDatos['titular'] ?? 'Titular';
                $marca = $tarjetaDatos['marca'] ?? 'Desconocida';
                
                // Verificar si la tarjeta ya está guardada (mismos últimos 4 dígitos)
                // Se omite la marca para evitar problemas de mayúsculas/minúsculas
                $stmtCheck = $conn->prepare("
                    SELECT id_tarjeta 
                    FROM tarjeta_guardada 
                    WHERE id_usuario = ? AND ultimos_cuatro = ?
                ");
                $stmtCheck->execute([$idUsuario, $ultimos4]);
                
                if ($stmtCheck->rowCount() === 0) {
                    $stmtTarjeta = $conn->prepare("
                        INSERT INTO tarjeta_guardada (id_usuario, titular, numero_encriptado, vencimiento_encriptado, marca, ultimos_cuatro)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmtTarjeta->execute([$idUsuario, $titular, $numEnc, $vencEnc, $marca, $ultimos4]);
                }
            }
        }
    }

    $conn->commit();

    // Nombre para el comprobante
    $nombreCliente = $esInvitado
        ? trim("{$guestNombre} {$guestApellido}") ?: 'Invitado'
        : '';
        
    // Obtener correo del cliente para enviar el comprobante
    $correoDestino = '';
    if ($esInvitado) {
        $correoDestino = $guestCorreo;
    } else {
        $stmtCorreo = $conn->prepare("SELECT correo, nombres, apellidos FROM usuario WHERE id_usuario = ?");
        $stmtCorreo->execute([$idUsuario]);
        $rowUser = $stmtCorreo->fetch(PDO::FETCH_ASSOC);
        if ($rowUser) {
            $correoDestino = $rowUser['correo'];
            $nombreCliente = trim("{$rowUser['nombres']} {$rowUser['apellidos']}");
        }
    }

    // ─── Enviar correo (síncrono para evitar corte de proceso en Windows) ──
    if (!empty($correoDestino)) {
        try {
            require_once __DIR__ . '/../config/MailHelper.php';
            $mailHelper = new \Config\MailHelper();
            $datosPedidoMail = [
                'id_pedido'    => $idPedido,
                'tipo_entrega' => ucfirst($tipoEntrega),
                'metodo_pago'  => ucfirst($metodoPago),
                'total'        => $total
            ];
            $mailHelper->enviarComprobantePedido($correoDestino, $nombreCliente, $datosPedidoMail);
        } catch (\Throwable $mailError) {
            error_log("Error enviando correo pedido #{$idPedido}: " . $mailError->getMessage());
        }
    }

    // ─── Enviar respuesta JSON AL CLIENTE ──────────────
    ob_clean();
    header('Content-Type: application/json');
    $respuesta = json_encode([
        'ok'             => true,
        'id_pedido'      => $idPedido,
        'es_invitado'    => $esInvitado,
        'nombre_cliente' => $nombreCliente,
        'comprobante'    => [
            'tipo'   => 'Boleta',
            'serie'  => $serie,
            'numero' => $numero,
            'total'  => number_format($total, 2)
        ]
    ]);
    header('Content-Length: ' . strlen($respuesta));
    echo $respuesta;

} catch (\Throwable $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'mensaje' => 'Error al procesar: ' . $e->getMessage()]);
}
