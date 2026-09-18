<?php

namespace Models;

class CarritoModel {

    private \PDO $conexion;

    public function __construct(\PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Busca o crea el único carrito con estado 'ACTIVO' para un usuario registrado.
     * Regla 1 & Regla 6: Garantiza 1 solo carrito ACTIVO por usuario.
     */
    public function obtenerOCrearCarritoActivo(int $idUsuario): int {
        $sql = "SELECT id_carrito FROM carrito WHERE id_usuario = :uid AND estado = 'ACTIVO' ORDER BY id_carrito DESC LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['uid' => $idUsuario]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row) {
            return (int) $row['id_carrito'];
        }

        // Crear nuevo carrito activo si no existe ninguno
        $stmtIns = $this->conexion->prepare("INSERT INTO carrito (id_usuario, estado, fecha_creacion) VALUES (:uid, 'ACTIVO', NOW())");
        $stmtIns->execute(['uid' => $idUsuario]);
        return (int) $this->conexion->lastInsertId();
    }

    /**
     * Obtiene todos los productos del carrito ACTIVO del usuario desde la BD.
     */
    public function obtenerItemsCarrito(int $idUsuario): array {
        try {
            $idCarrito = $this->obtenerOCrearCarritoActivo($idUsuario);

            $sql = "SELECT dc.id_producto AS id,
                           p.nombre,
                           p.precio,
                           p.imagen,
                           p.stock_actual AS stock,
                           dc.cantidad,
                           dc.subtotal
                    FROM detalle_carrito dc
                    INNER JOIN producto p ON dc.id_producto = p.id_producto
                    WHERE dc.id_carrito = :cid AND p.estado = 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute(['cid' => $idCarrito]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return array_map(function($item) {
                return [
                    'id'       => (int) $item['id'],
                    'nombre'   => $item['nombre'],
                    'precio'   => (float) $item['precio'],
                    'imagen'   => $item['imagen'],
                    'stock'    => (int) $item['stock'],
                    'cantidad' => (int) $item['cantidad'],
                    'subtotal' => (float) $item['subtotal']
                ];
            }, $items);

        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Agrega o actualiza la cantidad de un producto en el carrito ACTIVO del usuario.
     * Regla 6: Validaciones de stock, cantidad >= 1 e impedimento de duplicados.
     */
    public function agregarOActualizarProducto(int $idUsuario, int $idProducto, int $cantidad): array {
        if ($cantidad < 1) {
            return ['ok' => false, 'mensaje' => 'La cantidad mínima es 1.'];
        }

        // Verificar existencia y stock real en BD
        $stmtP = $this->conexion->prepare("SELECT nombre, precio, stock_actual FROM producto WHERE id_producto = :pid AND estado = 1");
        $stmtP->execute(['pid' => $idProducto]);
        $prod = $stmtP->fetch(\PDO::FETCH_ASSOC);

        if (!$prod) {
            return ['ok' => false, 'mensaje' => 'El producto no existe o no está disponible.'];
        }

        $stockDisponible = (int) $prod['stock_actual'];
        if ($stockDisponible < 1) {
            return ['ok' => false, 'mensaje' => "El producto \"{$prod['nombre']}\" no tiene stock disponible."];
        }

        if ($cantidad > $stockDisponible) {
            return ['ok' => false, 'mensaje' => "No hay suficiente stock disponible. Quedan {$stockDisponible} unidades."];
        }

        $idCarrito = $this->obtenerOCrearCarritoActivo($idUsuario);
        $precio = (float) $prod['precio'];
        $subtotal = round($precio * $cantidad, 2);

        try {
            // ON DUPLICATE KEY UPDATE evita productos duplicados en detalle_carrito
            $sql = "INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad, subtotal)
                    VALUES (:cid, :pid, :cant, :sub)
                    ON DUPLICATE KEY UPDATE cantidad = :cant2, subtotal = :sub2";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                'cid'   => $idCarrito,
                'pid'   => $idProducto,
                'cant'  => $cantidad,
                'sub'   => $subtotal,
                'cant2' => $cantidad,
                'sub2'  => $subtotal
            ]);

            return ['ok' => true, 'items' => $this->obtenerItemsCarrito($idUsuario)];

        } catch (\PDOException $e) {
            return ['ok' => false, 'mensaje' => 'Error al guardar el producto en el carrito.'];
        }
    }

    /**
     * Elimina un producto específico del carrito ACTIVO del usuario.
     */
    public function eliminarProducto(int $idUsuario, int $idProducto): array {
        $idCarrito = $this->obtenerOCrearCarritoActivo($idUsuario);

        $stmt = $this->conexion->prepare("DELETE FROM detalle_carrito WHERE id_carrito = :cid AND id_producto = :pid");
        $stmt->execute(['cid' => $idCarrito, 'pid' => $idProducto]);

        return ['ok' => true, 'items' => $this->obtenerItemsCarrito($idUsuario)];
    }

    /**
     * Vacía el detalle del carrito ACTIVO del usuario.
     */
    public function vaciarCarrito(int $idUsuario): array {
        $idCarrito = $this->obtenerOCrearCarritoActivo($idUsuario);

        $stmt = $this->conexion->prepare("DELETE FROM detalle_carrito WHERE id_carrito = :cid");
        $stmt->execute(['cid' => $idCarrito]);

        return ['ok' => true, 'items' => []];
    }

    /**
     * Sincroniza completamente la lista de ítems para un usuario.
     */
    public function sincronizarItems(int $idUsuario, array $items): bool {
        try {
            $idCarrito = $this->obtenerOCrearCarritoActivo($idUsuario);

            // Limpiar previo
            $stmtDel = $this->conexion->prepare("DELETE FROM detalle_carrito WHERE id_carrito = :cid");
            $stmtDel->execute(['cid' => $idCarrito]);

            $stmtIns = $this->conexion->prepare("
                INSERT INTO detalle_carrito (id_carrito, id_producto, cantidad, subtotal)
                VALUES (:cid, :pid, :cant, :sub)
            ");

            foreach ($items as $item) {
                $pid  = (int) ($item['id'] ?? 0);
                $cant = (int) ($item['cantidad'] ?? 1);
                $prec = (float) ($item['precio'] ?? 0);

                if ($pid > 0 && $cant > 0) {
                    $stmtIns->execute([
                        'cid'  => $idCarrito,
                        'pid'  => $pid,
                        'cant' => $cant,
                        'sub'  => round($prec * $cant, 2)
                    ]);
                }
            }

            return true;

        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Cambia el estado del carrito ACTIVO a 'COMPLETADO' y crea uno nuevo 'ACTIVO'.
     * Regla 2: Nunca elimina el carrito anterior.
     */
    public function finalizarCarritoActivo(int $idUsuario): int {
        $idCarritoActivo = $this->obtenerOCrearCarritoActivo($idUsuario);

        // Cambiar a COMPLETADO
        $stmtUpd = $this->conexion->prepare("UPDATE carrito SET estado = 'COMPLETADO' WHERE id_carrito = :cid");
        $stmtUpd->execute(['cid' => $idCarritoActivo]);

        // Crear automáticamente nuevo carrito ACTIVO vacío
        $stmtNew = $this->conexion->prepare("INSERT INTO carrito (id_usuario, estado, fecha_creacion) VALUES (:uid, 'ACTIVO', NOW())");
        $stmtNew->execute(['uid' => $idUsuario]);

        return $idCarritoActivo;
    }
}
