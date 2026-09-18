<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$id_pedido = (int)($_GET['id'] ?? 0);
if (!$id_pedido) die("ID de pedido no especificado");

$conn = (new \Config\Database())->conectar();

// Verificar que el pedido exista antes de autorizar el acceso
$stmtOwner = $conn->prepare("SELECT id_usuario, dni_cliente FROM pedido WHERE id_pedido = ?");
$stmtOwner->execute([$id_pedido]);
$owner = $stmtOwner->fetch(PDO::FETCH_ASSOC);
if (!$owner) die("Comprobante / Pedido no encontrado");

// Control de acceso: dueño de la cuenta, admin/trabajador, o invitado con el DNI usado en la compra
$dniSolicitado = trim((string)($_GET['dni'] ?? ''));
$accesoPermitido =
    (estaLogueado() && (int)($owner['id_usuario'] ?? 0) === (int)($_SESSION['usuario_id'] ?? 0)) ||
    (estaLogueado() && (esAdmin() || esTrabajador())) ||
    (!empty($dniSolicitado) && !empty($owner['dni_cliente']) && strtolower($dniSolicitado) === strtolower($owner['dni_cliente']));
if (!$accesoPermitido) {
    http_response_code(403);
    die("Acceso denegado a este comprobante.");
}

// Obtener datos del pedido y comprobante
$stmt = $conn->prepare("
    SELECT p.*, c.tipo_comprobante, c.serie as comp_serie, c.numero as comp_numero,
           c.igv as comp_igv, c.subtotal as comp_subtotal,
           COALESCE(NULLIF(CONCAT(u.nombres,' ',u.apellidos), ' '), p.nombre_cliente, 'N/A') AS cliente,
           COALESCE(u.dni, p.dni_cliente, 'N/A') AS dni_cliente,
           COALESCE(u.telefono, p.telefono_cliente, 'N/A') AS telefono_cliente,
           COALESCE(d.direccion_entrega, 'Recojo en tienda') AS direccion_entrega
    FROM pedido p 
    LEFT JOIN comprobante c ON p.id_pedido = c.id_pedido
    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
    LEFT JOIN delivery d ON p.id_pedido = d.id_pedido
    WHERE p.id_pedido = ?
");
$stmt->execute([$id_pedido]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) die("Comprobante / Pedido no encontrado");

$clienteObj = [
    'nombre_completo' => $pedido['cliente'],
    'dni'             => $pedido['dni_cliente'],
    'telefono'        => $pedido['telefono_cliente'],
];

// Obtener detalle
$stmtDet = $conn->prepare("
    SELECT d.*, pr.nombre
    FROM detalle_pedido d
    JOIN producto pr ON d.id_producto = pr.id_producto
    WHERE d.id_pedido = ?
");
$stmtDet->execute([$id_pedido]);
$pedido['detalle'] = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante #<?= htmlspecialchars($id_pedido) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; background: #525659; font-family: sans-serif; overflow: hidden; }
        .loading { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: white; }
        .spinner { border: 4px solid rgba(255,255,255,0.3); border-top: 4px solid #fff; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin-bottom: 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div id="loader" class="loading">
        <div class="spinner"></div>
        <h2>Generando PDF...</h2>
    </div>
    
    <script>
    window.onload = function() {
        const ped = <?= json_encode($pedido) ?>;
        const idPedido = ped.id_pedido;
        const { jsPDF } = window.jspdf;
        
        const doc  = new jsPDF({ unit: 'mm', format: 'a4' });
        const ancho = 210;
        let y = 18;

        // Cabecera principal roja
        doc.setFillColor(227, 6, 19);
        doc.rect(0, 0, ancho, 28, 'F');
        
        // Logo o Nombre de la Empresa
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('Market Primavera', 14, 12);
        
        doc.setFontSize(9);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(255, 255, 255);
        doc.text('RUC: 20612345678  |  Urb. Los Sauces 6448 – Lambayeque', 14, 19);
        doc.text('Tel: (074) 123456  |  contacto@marketprimavera.pe', 14, 25);

        // Cuadro del Tipo de Comprobante
        doc.setFillColor(245, 245, 245);
        doc.setDrawColor(227, 6, 19);
        doc.setLineWidth(0);
        doc.rect(130, 4, 68, 20, 'F'); // Solo Fill
        
        doc.setTextColor(227, 6, 19);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text(ped.tipo_comprobante?.toUpperCase() || 'BOLETA ELECTRÓNICA', 164, 12, { align: 'center' });
        
        doc.setFontSize(12);
        const compRef = ped.comp_serie
            ? `${ped.comp_serie}-${String(ped.comp_numero).padStart(8,'0')}`
            : `B001-${String(idPedido).padStart(8,'0')}`;
        doc.text(compRef, 164, 20, { align: 'center' });

        y = 38;
        const isDelivery = ped.tipo_entrega === 'Delivery';
        const boxHeight = isDelivery ? 46 : 38;

        // ================= DATOS DEL CLIENTE Y PEDIDO =================
        doc.setFillColor(245, 247, 250);
        doc.rect(14, y, ancho - 28, boxHeight, 'F'); // Caja contenedora gris claro

        // Títulos de sección
        doc.setTextColor(227, 6, 19);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.text('Datos del Cliente', 18, y + 8);
        doc.text('Datos del Pedido', 110, y + 8);
        
        // Línea separadora
        doc.setDrawColor(200, 200, 200);
        doc.setLineWidth(0.3);
        doc.line(18, y + 10, ancho - 18, y + 10);

        // Textos del Cliente
        doc.setTextColor(60, 60, 60);
        doc.setFontSize(9);
        
        const c_y = y + 16;
        const nombreCliente = (ped.cliente && ped.cliente !== 'N/A') ? ped.cliente : '<?php echo htmlspecialchars($clienteObj['nombre_completo'] ?? ''); ?>';
        const dniCliente = (ped.dni_cliente && ped.dni_cliente !== 'N/A') ? ped.dni_cliente : '<?php echo htmlspecialchars($clienteObj['dni'] ?? 'N/A'); ?>';
        const telefonoCliente = (ped.telefono_cliente && ped.telefono_cliente !== 'N/A') ? ped.telefono_cliente : '<?php echo htmlspecialchars($clienteObj['telefono'] ?? 'N/A'); ?>';

        doc.setFont('helvetica', 'normal');
        doc.text('Cliente:', 18, c_y);
        doc.setFont('helvetica', 'bold');
        doc.text(nombreCliente || 'Sin nombre', 18, c_y + 4);
        
        doc.setFont('helvetica', 'normal');
        doc.text('DNI:', 18, c_y + 10);
        doc.setFont('helvetica', 'bold');
        doc.text(dniCliente || 'N/A', 18, c_y + 14);

        let nextY = c_y + 20;

        if (isDelivery) {
            doc.setFont('helvetica', 'normal');
            doc.text('Dirección:', 18, nextY);
            doc.setFont('helvetica', 'bold');
            const dirText = ped.direccion_entrega || 'Sin dirección especificada';
            doc.text(dirText.slice(0, 42), 18, nextY + 4);
            nextY += 10;
        }

        doc.setFont('helvetica', 'normal');
        doc.text('Teléfono:', 18, nextY);
        doc.setFont('helvetica', 'bold');
        doc.text(telefonoCliente || 'N/A', 18, nextY + 4);

        // Textos del Pedido
        const p_y = y + 16;
        const fechaObj = ped.fecha_pedido ? new Date(ped.fecha_pedido) : new Date();
        const fecha = fechaObj.toLocaleDateString('es-PE', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
        
        doc.setFont('helvetica', 'normal');
        doc.text('N° Pedido:', 110, p_y);
        doc.setFont('helvetica', 'bold');
        doc.text(`#${idPedido}`, 110, p_y + 4);

        doc.setFont('helvetica', 'normal');
        doc.text('Fecha de emisión:', 110, p_y + 10);
        doc.setFont('helvetica', 'bold');
        doc.text(fecha, 110, p_y + 14);

        doc.setFont('helvetica', 'normal');
        doc.text('Entrega:', 110, p_y + 20);
        doc.setFont('helvetica', 'bold');
        doc.text(isDelivery ? 'Delivery' : 'Recojo en tienda', 110, p_y + 24);

        doc.setFont('helvetica', 'normal');
        doc.text('Pago:', 110, p_y + 30);
        doc.setFont('helvetica', 'bold');
        doc.text(ped.metodo_pago || '—', 110, p_y + 34);

        y += 58;


        // ================= TABLA DE PRODUCTOS =================
        // Encabezado tabla
        doc.setFillColor(227, 6, 19);
        doc.rect(14, y, ancho - 28, 9, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.text('PRODUCTO',  18, y + 6);
        doc.text('CANTIDAD', 125, y + 6, { align: 'center' });
        doc.text('P. UNITARIO', 158, y + 6, { align: 'right' });
        doc.text('SUBTOTAL',  192, y + 6, { align: 'right' });
        y += 9;

        // Filas de la tabla
        doc.setFontSize(9);
        (ped.detalle || []).forEach((item, idx) => {
            if (idx % 2 === 0) {
                doc.setFillColor(250, 250, 250);
                doc.rect(14, y, ancho - 28, 9, 'F');
            }
            doc.setTextColor(60, 60, 60);
            doc.setFont('helvetica', 'normal');
            
            doc.text((item.nombre || '').slice(0, 48), 18, y + 6);
            doc.text(String(item.cantidad), 125, y + 6, { align: 'center' });
            doc.text('S/ ' + parseFloat(item.precio_unitario).toFixed(2), 158, y + 6, { align: 'right' });
            
            doc.setFont('helvetica', 'bold');
            doc.text('S/ ' + parseFloat(item.subtotal || (item.cantidad * item.precio_unitario)).toFixed(2), 192, y + 6, { align: 'right' });
            
            y += 9;
            if (y > 250) { 
                doc.addPage(); 
                y = 20; 
            }
        });

        // Borde inferior tabla
        doc.setDrawColor(227, 6, 19);
        doc.setLineWidth(0.5);
        doc.line(14, y, ancho - 14, y);
        y += 8;

        // ================= TOTALES =================
        const subtotalOrig = parseFloat(ped.subtotal || ped.comp_subtotal || 0) + parseFloat(ped.descuento || 0);
        const descuento    = parseFloat(ped.descuento || 0);
        const igv          = parseFloat(ped.comp_igv || 0);
        const total        = parseFloat(ped.total    || 0);
        const envio        = ped.tipo_entrega === 'Delivery' ? 5.00 : 0;

        const filasTotales = [
            ['Subtotal:',                 `S/ ${subtotalOrig.toFixed(2)}`],
            descuento > 0 ? ['Descuento:', `- S/ ${descuento.toFixed(2)}`] : null,
            ['IGV (18%):',                `S/ ${igv.toFixed(2)}`],
            ['Envío:',                    envio > 0 ? `S/ ${envio.toFixed(2)}` : 'Gratis'],
        ].filter(Boolean);

        doc.setFontSize(10);
        filasTotales.forEach(([lbl, val]) => {
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(80, 80, 80);
            doc.text(lbl, 140, y);
            doc.text(val, 192, y, { align: 'right' });
            y += 7;
        });

        // Total final
        doc.setFillColor(227, 6, 19);
        doc.rect(130, y - 2, 66, 9, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('TOTAL:', 140, y + 4.5);
        doc.text(`S/ ${total.toFixed(2)}`, 194, y + 4.5, { align: 'right' });
        y += 18;

        // ================= PIE DE PÁGINA =================
        doc.setTextColor(150, 150, 150);
        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text('¡Gracias por tu compra!', ancho / 2, y, { align: 'center' });
        doc.text('Comprobante electrónico generado por Market Primavera.', ancho / 2, y + 5, { align: 'center' });
        doc.text('Representación impresa de comprobante de pago electrónico.', ancho / 2, y + 10, { align: 'center' });

        // Mostrar PDF a pantalla completa usando Blob URL (evita bloqueos de navegadores con data: URIs)
        const blobUrl = doc.output('bloburl');
        document.body.innerHTML = `<iframe width="100%" height="100%" style="border:none; width:100vw; height:100vh;" src="${blobUrl}"></iframe>`;
        document.title = `Comprobante_${compRef}.pdf`;
    };
    </script>
</body>
</html>



