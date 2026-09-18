<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';

use Config\Database;

header('Content-Type: application/json');
requireAdmin();

$conn = (new Database())->conectar();

// Mapa de cargos a IDs de rol
$mapaCargos = [
    'Administrador' => 1,
    'Repartidor'    => 3,
    'Almacenero'    => 4,
    'Despachador'   => 5,
    'Cajero'        => 6
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
        exit;
    }

    $accion = trim($d['accion'] ?? '');

    try {
        // ── 1. CREAR / ASIGNAR TRABAJADOR ──
        if ($accion === 'crear') {
            $idUsuario = (int)($d['id_usuario'] ?? 0);
            $cargo     = trim($d['cargo'] ?? 'Despachador');
            $turno     = trim($d['turno'] ?? 'Mañana');
            $sueldo    = !empty($d['sueldo']) ? (float)$d['sueldo'] : null;
            $notas     = trim($d['notas'] ?? '');

            if ($idUsuario <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'Debe seleccionar un usuario válido.']);
                exit;
            }

            if (!isset($mapaCargos[$cargo])) {
                echo json_encode(['ok' => false, 'mensaje' => 'Cargo no válido.']);
                exit;
            }

            $idRol = $mapaCargos[$cargo];

            $conn->beginTransaction();

            // Insertar o actualizar registro en trabajador
            $stmt = $conn->prepare("
                INSERT INTO trabajador (id_usuario, cargo, turno, sueldo, estado, notas, fecha_ingreso)
                VALUES (?, ?, ?, ?, 1, ?, CURRENT_DATE)
                ON DUPLICATE KEY UPDATE cargo = VALUES(cargo), turno = VALUES(turno), sueldo = VALUES(sueldo), estado = 1, notas = VALUES(notas)
            ");
            $stmt->execute([$idUsuario, $cargo, $turno, $sueldo, $notas]);

            // Asignar rol en usuario_rol
            $checkRol = $conn->prepare("SELECT 1 FROM usuario_rol WHERE id_usuario = ? AND id_rol = ?");
            $checkRol->execute([$idUsuario, $idRol]);
            if (!$checkRol->fetch()) {
                $conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)")
                     ->execute([$idUsuario, $idRol]);
            }

            // Si es repartidor, mantener consistencia con tabla repartidor
            if ($cargo === 'Repartidor') {
                $checkRep = $conn->prepare("SELECT id_repartidor FROM repartidor WHERE id_usuario = ?");
                $checkRep->execute([$idUsuario]);
                if (!$checkRep->fetch()) {
                    $u = $conn->prepare("SELECT nombres, apellidos, telefono FROM usuario WHERE id_usuario = ?");
                    $u->execute([$idUsuario]);
                    $usr = $u->fetch(PDO::FETCH_ASSOC);
                    $conn->prepare("INSERT INTO repartidor (id_usuario, nombres, telefono, estado) VALUES (?, ?, ?, 1)")
                         ->execute([$idUsuario, trim(($usr['nombres'] ?? '') . ' ' . ($usr['apellidos'] ?? '')), $usr['telefono'] ?? null]);
                } else {
                    $conn->prepare("UPDATE repartidor SET estado = 1 WHERE id_usuario = ?")->execute([$idUsuario]);
                }
            }

            $conn->commit();
            echo json_encode(['ok' => true, 'mensaje' => 'Trabajador registrado exitosamente.']);
            exit;

        // ── 2. ACTUALIZAR TRABAJADOR ──
        } elseif ($accion === 'actualizar') {
            $idTrabajador = (int)($d['id_trabajador'] ?? 0);
            $cargoNuevo   = trim($d['cargo'] ?? '');
            $turnoNuevo   = trim($d['turno'] ?? 'Mañana');
            $sueldoNuevo  = !empty($d['sueldo']) ? (float)$d['sueldo'] : null;
            $notasNuevas  = trim($d['notas'] ?? '');

            if ($idTrabajador <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID de trabajador inválido.']);
                exit;
            }

            $conn->beginTransaction();

            $trabStmt = $conn->prepare("SELECT id_usuario, cargo FROM trabajador WHERE id_trabajador = ?");
            $trabStmt->execute([$idTrabajador]);
            $trabActual = $trabStmt->fetch(PDO::FETCH_ASSOC);

            if (!$trabActual) {
                $conn->rollBack();
                echo json_encode(['ok' => false, 'mensaje' => 'Trabajador no encontrado.']);
                exit;
            }

            $idUsuario = (int)$trabActual['id_usuario'];
            $cargoViejo = $trabActual['cargo'];

            // Actualizar tabla trabajador
            $upd = $conn->prepare("
                UPDATE trabajador
                SET cargo = ?, turno = ?, sueldo = ?, notas = ?
                WHERE id_trabajador = ?
            ");
            $upd->execute([$cargoNuevo, $turnoNuevo, $sueldoNuevo, $notasNuevas, $idTrabajador]);

            // Si cambió el cargo, actualizar roles correspondientes
            if ($cargoNuevo !== $cargoViejo) {
                if (isset($mapaCargos[$cargoViejo])) {
                    $conn->prepare("DELETE FROM usuario_rol WHERE id_usuario = ? AND id_rol = ?")
                         ->execute([$idUsuario, $mapaCargos[$cargoViejo]]);
                }
                if (isset($mapaCargos[$cargoNuevo])) {
                    $conn->prepare("INSERT IGNORE INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)")
                         ->execute([$idUsuario, $mapaCargos[$cargoNuevo]]);
                }

                if ($cargoNuevo === 'Repartidor') {
                    $conn->prepare("UPDATE repartidor SET estado = 1 WHERE id_usuario = ?")->execute([$idUsuario]);
                } elseif ($cargoViejo === 'Repartidor') {
                    $conn->prepare("UPDATE repartidor SET estado = 0 WHERE id_usuario = ?")->execute([$idUsuario]);
                }
            }

            $conn->commit();
            echo json_encode(['ok' => true, 'mensaje' => 'Datos del trabajador actualizados.']);
            exit;

        // ── 3. TOGGLE ESTADO ──
        } elseif ($accion === 'toggle_estado') {
            $idTrabajador = (int)($d['id_trabajador'] ?? 0);
            $nuevoEstado  = (int)($d['estado'] ?? 0);

            if ($idTrabajador <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID inválido.']);
                exit;
            }

            $conn->beginTransaction();
            $trab = $conn->prepare("SELECT id_usuario, cargo FROM trabajador WHERE id_trabajador = ?");
            $trab->execute([$idTrabajador]);
            $t = $trab->fetch(PDO::FETCH_ASSOC);

            if (!$t) {
                $conn->rollBack();
                echo json_encode(['ok' => false, 'mensaje' => 'Trabajador no encontrado.']);
                exit;
            }

            $conn->prepare("UPDATE trabajador SET estado = ? WHERE id_trabajador = ?")
                 ->execute([$nuevoEstado, $idTrabajador]);

            // Si es repartidor, sincronizar estado en tabla repartidor
            if ($t['cargo'] === 'Repartidor') {
                $conn->prepare("UPDATE repartidor SET estado = ? WHERE id_usuario = ?")
                     ->execute([$nuevoEstado, $t['id_usuario']]);
            }

            $conn->commit();
            echo json_encode(['ok' => true, 'mensaje' => 'Estado actualizado.']);
            exit;

        // ── 4. ELIMINAR TRABAJADOR ──
        } elseif ($accion === 'eliminar') {
            $idTrabajador = (int)($d['id_trabajador'] ?? 0);
            if ($idTrabajador <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID inválido.']);
                exit;
            }

            $conn->beginTransaction();
            $trab = $conn->prepare("SELECT id_usuario, cargo FROM trabajador WHERE id_trabajador = ?");
            $trab->execute([$idTrabajador]);
            $t = $trab->fetch(PDO::FETCH_ASSOC);

            if ($t) {
                $cargo = $t['cargo'];
                $idU   = (int)$t['id_usuario'];

                $conn->prepare("DELETE FROM trabajador WHERE id_trabajador = ?")->execute([$idTrabajador]);

                if (isset($mapaCargos[$cargo])) {
                    $conn->prepare("DELETE FROM usuario_rol WHERE id_usuario = ? AND id_rol = ?")
                         ->execute([$idU, $mapaCargos[$cargo]]);
                }
                if ($cargo === 'Repartidor') {
                    $conn->prepare("UPDATE repartidor SET estado = 0 WHERE id_usuario = ?")->execute([$idU]);
                }
            }

            $conn->commit();
            echo json_encode(['ok' => true, 'mensaje' => 'Trabajador eliminado de la planilla.']);
            exit;
        }

        echo json_encode(['ok' => false, 'mensaje' => 'Acción no soportada.']);
        exit;

    } catch (\Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log('[TrabajadoresController] ' . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error en el servidor: ' . $e->getMessage()]);
        exit;
    }
}

// ── GET: LISTAR TRABAJADORES Y USUARIOS DISPONIBLES ──
try {
    // Listar trabajadores con datos de usuario
    $query = "
        SELECT t.id_trabajador,
               t.id_usuario,
               t.cargo,
               t.turno,
               t.sueldo,
               t.fecha_ingreso,
               t.estado,
               t.notas,
               u.nombres,
               u.apellidos,
               u.dni,
               u.correo,
               u.telefono,
               (SELECT COUNT(*) FROM delivery d JOIN repartidor r ON d.id_repartidor = r.id_repartidor WHERE r.id_usuario = t.id_usuario AND d.estado_delivery = 'Entregado') AS entregas_completadas
        FROM trabajador t
        JOIN usuario u ON t.id_usuario = u.id_usuario
        ORDER BY t.estado DESC, t.id_trabajador ASC
    ";
    $trabajadores = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

    // Listar usuarios registrados disponibles para asignar como trabajador
    $queryUsuarios = "
        SELECT u.id_usuario, u.nombres, u.apellidos, u.dni, u.correo, u.telefono
        FROM usuario u
        WHERE u.estado = 1
        ORDER BY u.nombres ASC
    ";
    $usuarios = $conn->query($queryUsuarios)->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'ok' => true,
        'trabajadores' => $trabajadores,
        'usuarios'     => $usuarios
    ]);
} catch (\Exception $e) {
    error_log('[TrabajadoresController GET] ' . $e->getMessage());
    echo json_encode(['ok' => false, 'mensaje' => 'Error al cargar los trabajadores.']);
}
