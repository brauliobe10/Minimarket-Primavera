<!-- Vista del comprobante electrónico — incluida por checkout.php -->
<div class="comprobante-wrap" id="comprobanteWrap" style="display:none;">
    <div class="comprobante">

        <div class="comp-header">
            <div class="comp-empresa">
                <strong style="font-size: 1.4rem;">Market Primavera</strong>
                <span style="font-size: 0.85rem; opacity: 0.9;">RUC: 20612345678 &nbsp;|&nbsp; Urb. Los Sauces 6448 – Lambayeque</span>
                <span style="font-size: 0.85rem; opacity: 0.9;">Tel: (074) 123456 &nbsp;|&nbsp; contacto@marketprimavera.pe</span>
            </div>
            <div class="comp-tipo">
                <span id="compTipo">BOLETA ELECTRÓNICA</span>
                <strong id="compSerieNum">B001-00000001</strong>
            </div>
        </div>

        <div class="comp-divider"></div>

        <style>
            @media print {
                .comp-header { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                .comp-empresa svg { stroke: #fff !important; }
            }
        </style>
        <div class="comp-meta-row">
            <div class="comp-meta-col">
                <h4>Datos del Cliente</h4>
                <div><span>Cliente:</span> <strong id="compCliente">—</strong></div>
                <div><span>DNI:</span> <strong id="compDni">—</strong></div>
                <div id="compFilaDireccion"><span>Dirección:</span> <strong id="compDireccion">—</strong></div>
                <div><span>Teléfono:</span> <strong id="compTelefono">—</strong></div>
            </div>
            <div class="comp-meta-col">
                <h4>Datos del Pedido</h4>
                <div><span>N° Pedido:</span> <strong id="compPedido">—</strong></div>
                <div><span>Fecha:</span> <strong id="compFecha">—</strong></div>
                <div><span>Entrega:</span> <strong id="compEntrega">—</strong></div>
                <div><span>Pago:</span> <strong id="compPago">—</strong></div>
            </div>
        </div>

        <div class="comp-divider"></div>

        <table class="comp-tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant.</th>
                    <th>P. Unit.</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="compItems"></tbody>
        </table>

        <div class="comp-divider"></div>

        <div class="comp-totales">
            <div class="comp-fila"><span>Subtotal:</span><span id="compSubtotal">S/ 0.00</span></div>
            <div class="comp-fila descuento" id="compFilaDesc" style="display:none;">
                <span>Descuento:</span><span id="compDescuento">- S/ 0.00</span>
            </div>
            <div class="comp-fila"><span>IGV (18%):</span><span id="compIgv">S/ 0.00</span></div>
            <div class="comp-fila" id="compFilaEnvio"><span>Envío:</span><span id="compEnvio">S/ 0.00</span></div>
            <div class="comp-fila total"><span>TOTAL:</span><span id="compTotal">S/ 0.00</span></div>
            <div class="comp-fila" id="compFilaVuelto" style="display:none;color:#27ae60;font-weight:700;">
                <span>Pagas con / Vuelto:</span><span id="compVuelto">—</span>
            </div>
        </div>

        <div class="comp-pie">
            <p>¡Gracias por tu compra! Este comprobante es válido como documento tributario electrónico.</p>
            <p id="compEstado" style="color:#27ae60;font-weight:700;">✓ Emitido correctamente</p>
        </div>

    </div>

    <div class="comp-acciones">
        <button class="btn-comp-print" onclick="window.print()">
            <i class="fa fa-print"></i> Imprimir
        </button>
        <a href="<?= BASE_URL ?>/views/inicio.php" class="btn-comp-inicio">
            <i class="fa fa-home"></i> Volver al inicio
        </a>
    </div>
</div>


