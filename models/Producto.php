<?php

namespace Models;

class Producto {

    private \PDO $conexion;

    public function __construct(\PDO $conexion) {
        $this->conexion = $conexion;
    }

    // Productos para la sección "Más vendido" / destacados (ordenados por mayor cantidad de ventas)
    public function obtenerDestacados(int $limite = 8): array {
        $sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen, p.descripcion, p.stock_actual,
                       COALESCE(SUM(dp.cantidad), 0) AS total_vendido,
                       MAX(pr.porcentaje_descuento) AS porcentaje_descuento
                FROM Producto p
                LEFT JOIN detalle_pedido dp ON p.id_producto = dp.id_producto
                LEFT JOIN Producto_Promocion pp ON p.id_producto = pp.id_producto
                LEFT JOIN Promocion pr ON pp.id_promocion = pr.id_promocion
                    AND pr.estado = TRUE
                    AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                WHERE p.estado = TRUE AND p.stock_actual > 0
                GROUP BY p.id_producto, p.nombre, p.precio, p.imagen, p.descripcion, p.stock_actual
                ORDER BY total_vendido DESC, p.id_producto DESC
                LIMIT :limite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Productos con promoción activa para la sección "Ofertas especiales"
    public function obtenerEnOferta(): array {
        $sql = "SELECT p.id_producto, p.nombre, p.precio, p.imagen,
                       p.descripcion, p.stock_actual, pr.porcentaje_descuento
                FROM Producto p
                INNER JOIN Producto_Promocion pp ON p.id_producto = pp.id_producto
                INNER JOIN Promocion pr ON pp.id_promocion = pr.id_promocion
                WHERE p.estado = TRUE
                  AND pr.estado = TRUE
                  AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                ORDER BY pr.porcentaje_descuento DESC, p.nombre";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function buscarPorNombre(string $termino): array {

        $sql = "SELECT
                    id_producto,
                    id_categoria,
                    nombre,
                    precio,
                    imagen,
                    stock_actual
                FROM Producto
                WHERE estado = TRUE
                AND stock_actual > 0
                AND nombre LIKE :termino
                LIMIT 10";

        $stmt = $this->conexion->prepare($sql);

        $stmt->execute([
            'termino' => '%' . $termino . '%'
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM Producto WHERE id_producto = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['id' => $id]);
        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    public function obtenerPorCategoria(int $idCategoria): array
    {
        $sql = "SELECT p.id_producto,
                    p.nombre,
                    p.precio,
                    p.imagen,
                    p.descripcion,
                    p.stock_actual,
                    MAX(pr.porcentaje_descuento) AS porcentaje_descuento,
                    COALESCE(SUM(dp.cantidad), 0) AS total_vendido
                FROM Producto p
                LEFT JOIN Producto_Promocion pp ON p.id_producto = pp.id_producto
                LEFT JOIN Promocion pr ON pp.id_promocion = pr.id_promocion
                    AND pr.estado = TRUE
                    AND CURDATE() BETWEEN pr.fecha_inicio AND pr.fecha_fin
                LEFT JOIN detalle_pedido dp ON p.id_producto = dp.id_producto
                WHERE p.estado = TRUE
                AND p.stock_actual > 0
                AND p.id_categoria = :id_categoria
                GROUP BY p.id_producto, p.nombre, p.precio, p.imagen,
                         p.descripcion, p.stock_actual
                ORDER BY p.nombre";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['id_categoria' => $idCategoria]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerRangoPreciosPorCategoria(int $idCategoria): array
    {
        $sql = "SELECT
                    MIN(precio) AS precio_min,
                    MAX(precio) AS precio_max
                FROM Producto
                WHERE estado = TRUE
                AND stock_actual > 0
                AND id_categoria = :id_categoria";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            'id_categoria' => $idCategoria
        ]);

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: ['precio_min' => 0, 'precio_max' => 0];
    }
}