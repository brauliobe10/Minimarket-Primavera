<?php

namespace Models;

class Categoria {

    private \PDO $conexion;

    public function __construct(\PDO $conexion) {
        $this->conexion = $conexion;
    }

    // Obtiene las 6 categorías más populares
    public function obtenerTodas(): array {
        $sql = "
            SELECT c.id_categoria,
                   c.nombre,
                   c.descripcion,
                   c.imagen,
                   COALESCE(SUM(dp.cantidad), 0) AS total_vendido
            FROM Categoria c
            INNER JOIN Producto p ON c.id_categoria = p.id_categoria
            LEFT JOIN detalle_pedido dp ON p.id_producto = dp.id_producto
            GROUP BY c.id_categoria, c.nombre, c.descripcion, c.imagen
            ORDER BY total_vendido DESC, RAND()
            LIMIT 6
        ";

        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array {
        $sql = "SELECT * FROM Categoria WHERE id_categoria = :id";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['id' => $id]);

        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $resultado ?: null;
    }

    public function obtenerPorGrupo(string $grupo): array
    {
        $sql = "SELECT id_categoria, nombre
                FROM Categoria
                WHERE grupo = :grupo
                ORDER BY nombre";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute(['grupo' => $grupo]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}