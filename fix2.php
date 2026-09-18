<?php
require 'c:/xampp/htdocs/TecW_PAF/config/Database.php';
$conn = (new Config\Database())->conectar();
$conn->query("UPDATE pedido SET estado_pedido='Pendiente' WHERE estado_pedido LIKE '%prepara%'");
echo 'OK';
