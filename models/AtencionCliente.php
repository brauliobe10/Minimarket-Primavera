<?php

namespace Models;

require_once __DIR__ . "/../config/Database.php";

use Config\Database;

class AtencionCliente {

    private $conexion;
    private $tabla = "soporte_cliente";

    public function __construct() {
        $db = new Database();
        $this->conexion = $db->conectar();
    }

    public function guardar($datos) {
        $query = "INSERT INTO " . $this->tabla . " 
                  (usuario_id, asunto, mensaje, estado) 
                  VALUES (:usuario_id, :asunto, :mensaje, 'Pendiente')";

        $stmt = $this->conexion->prepare($query);

        $stmt->bindParam(":usuario_id", $datos['usuario_id'], \PDO::PARAM_INT);
        $stmt->bindParam(":asunto", $datos['asunto']);
        $stmt->bindParam(":mensaje", $datos['mensaje']);

        if ($stmt->execute()) {
            $soporte_id = $this->conexion->lastInsertId();
            $this->agregarMensajeHilo($soporte_id, 'cliente', $datos['mensaje']);
            return true;
        }
        return false;
    }

    public function obtenerTodos() {
        $query = "SELECT s.*, 
                         COALESCE(u.nombres, 'Usuario') AS nombres, 
                         COALESCE(u.apellidos, 'Eliminado') AS apellidos, 
                         COALESCE(u.correo, 'Sin correo') AS correo, 
                         COALESCE(u.dni, 'Sin DNI') AS dni 
                  FROM " . $this->tabla . " s
                  LEFT JOIN usuario u ON s.usuario_id = u.id_usuario
                  ORDER BY s.estado ASC, s.fecha DESC";
        $stmt = $this->conexion->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorUsuario($usuario_id) {
        $query = "SELECT * FROM " . $this->tabla . " 
                  WHERE usuario_id = :usuario_id 
                  ORDER BY fecha DESC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":usuario_id", $usuario_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $query = "SELECT s.*, 
                         COALESCE(u.nombres, 'Usuario') AS nombres, 
                         COALESCE(u.apellidos, 'Eliminado') AS apellidos, 
                         COALESCE(u.correo, 'Sin correo') AS correo 
                  FROM " . $this->tabla . " s
                  LEFT JOIN usuario u ON s.usuario_id = u.id_usuario
                  WHERE s.id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function responder($id, $respuesta) {
        $this->agregarMensajeHilo($id, 'admin', $respuesta);
        
        $query = "UPDATE " . $this->tabla . " 
                  SET estado = 'respondido', 
                      fecha_respuesta = NOW() 
                  WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function responderCliente($id, $respuesta) {
        $this->agregarMensajeHilo($id, 'cliente', $respuesta);
        
        $query = "UPDATE " . $this->tabla . " 
                  SET estado = 'pendiente' 
                  WHERE id = :id";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":id", $id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function obtenerMensajesHilo($soporte_id) {
        $query = "SELECT * FROM soporte_mensajes 
                  WHERE soporte_id = :soporte_id 
                  ORDER BY fecha ASC";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":soporte_id", $soporte_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function agregarMensajeHilo($soporte_id, $remitente, $mensaje) {
        $query = "INSERT INTO soporte_mensajes (soporte_id, remitente, mensaje) 
                  VALUES (:soporte_id, :remitente, :mensaje)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(":soporte_id", $soporte_id, \PDO::PARAM_INT);
        $stmt->bindParam(":remitente", $remitente);
        $stmt->bindParam(":mensaje", $mensaje);
        return $stmt->execute();
    }
}