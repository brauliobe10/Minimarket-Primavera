let soporteGlobalAdmin = [];

document.addEventListener('DOMContentLoaded', () => {
    // Escuchar el submit del form de respuesta
    const form = document.getElementById('formResponderSoporte');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarRespuestaSoporte();
        });
    }
});

function cargarSoporte() {
    const tbody = document.querySelector('#tablaSoporte tbody');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Cargando...</td></tr>';

    fetch(BASE_URL + '/controllers/admin/SoporteAdminController.php?accion=listar')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:red;">${data.message}</td></tr>`;
                return;
            }

            soporteGlobalAdmin = data.data;
            populateMotivosDropdown(soporteGlobalAdmin);
            renderizarTablaSoporte(soporteGlobalAdmin);
        })
        .catch(err => {
            tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:red;">Error de conexión.</td></tr>';
        });
}

function renderizarTablaSoporte(mensajes) {
    const tbody = document.querySelector('#tablaSoporte tbody');
    if (!tbody) return;

    if (mensajes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No se encontraron mensajes.</td></tr>';
        return;
    }

    let html = '';
    mensajes.forEach((m, index) => {
        const badgeClass = m.estado === 'respondido' ? 'adm-badge green' : 'adm-badge orange';
        const mJson = JSON.stringify(m).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        
        let btnAccion = '';
        if (m.estado === 'pendiente' || m.estado === 'Pendiente') {
            btnAccion = `<button class="adm-btn adm-btn-primary adm-btn-sm" onclick="abrirModalSoporte('${mJson}')" style="background: linear-gradient(135deg, #e30613, #b9000b); border: none; box-shadow: 0 2px 4px rgba(227,6,19,0.25);"><i class="fa fa-comments"></i> Abrir Chat</button>`;
        } else {
            btnAccion = `<button class="adm-btn adm-btn-ghost adm-btn-sm" onclick="abrirModalSoporte('${mJson}')" style="opacity: 0.8; border: 1px solid #cbd5e1; background: #f8fafc;"><i class="fa fa-history"></i> Conversación</button>`;
        }

        html += `
            <tr>
                <td>${index + 1}</td>
                <td>#${m.id}</td>
                <td>
                    <strong>${m.nombres} ${m.apellidos}</strong><br>
                    <small style="color:#64748b">${m.correo}</small>
                </td>
                <td><strong>${m.asunto}</strong></td>
                <td>${new Date(m.fecha).toLocaleDateString()}</td>
                <td><span class="${badgeClass}">${m.estado}</span></td>
                <td>${btnAccion}</td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

function populateMotivosDropdown(mensajes) {
    const select = document.getElementById('filtroSoporteMotivo');
    if (!select) return;
    
    const valorActual = select.value;
    
    const motivosEstandar = {
        "pedido": "Consulta sobre pedido",
        "producto": "Información de producto",
        "devolucion": "Devolución o cambio",
        "entrega": "Problemas de entrega",
        "facturacion": "Facturación",
        "sugerencia": "Sugerencia",
        "otro": "Otro"
    };
    
    // Identificar motivos que no están en la lista estándar (escritos manualmente en "Otro")
    const motivosDB = [...new Set(mensajes.map(m => m.asunto))].filter(m => m && !motivosEstandar[m]);
    
    let html = '<option value="">Todos los motivos</option>';
    
    // 1. Agregar los estándar
    for (const [val, label] of Object.entries(motivosEstandar)) {
        html += `<option value="${val}">${label}</option>`;
    }
    
    // 2. Agregar los personalizados (si existen)
    motivosDB.forEach(motivo => {
        html += `<option value="${motivo}">${motivo}</option>`;
    });
    
    select.innerHTML = html;
    select.value = valorActual;
}

function filtrarSoporte() {
    const term = document.getElementById('buscadorSoporte').value.toLowerCase();
    const fecha = document.getElementById('filtroSoporteFecha').value;
    const motivo = document.getElementById('filtroSoporteMotivo').value;

    const filtrados = soporteGlobalAdmin.filter(m => {
        // Texto
        const matchText = 
            (m.nombres && m.nombres.toLowerCase().includes(term)) ||
            (m.apellidos && m.apellidos.toLowerCase().includes(term)) ||
            (m.correo && m.correo.toLowerCase().includes(term)) ||
            (m.asunto && m.asunto.toLowerCase().includes(term)) ||
            (m.dni && m.dni.toString().includes(term)) ||
            (m.id && m.id.toString().includes(term));
            
        // Fecha
        let matchFecha = true;
        if (fecha) {
            const fechaMsj = new Date(m.fecha).toISOString().split('T')[0];
            matchFecha = (fechaMsj === fecha);
        }
        
        // Motivo
        let matchMotivo = true;
        if (motivo) {
            if (motivo === 'otro') {
                // Si el filtro es "Otro", mostrar todos los tickets con asuntos personalizados
                const motivosEstandarKeys = ['pedido', 'producto', 'devolucion', 'entrega', 'facturacion', 'sugerencia', 'otro'];
                matchMotivo = !motivosEstandarKeys.includes(m.asunto);
            } else {
                matchMotivo = (m.asunto === motivo);
            }
        }
        
        return matchText && matchFecha && matchMotivo;
    });
    renderizarTablaSoporte(filtrados);
}

function limpiarFiltrosSoporte() {
    document.getElementById('buscadorSoporte').value = '';
    document.getElementById('filtroSoporteFecha').value = '';
    document.getElementById('filtroSoporteMotivo').value = '';
    filtrarSoporte();
}

function abrirModalSoporte(mJson) {
    const m = JSON.parse(mJson);
    
    document.getElementById('soporteId').value = m.id;
    document.getElementById('soporteClienteNombreCorreo').textContent = `${m.nombres} ${m.apellidos} (${m.correo})`;
    
    const historial = document.getElementById('soporteHistorialMensajes');
    historial.innerHTML = '<div style="text-align: center; color: #94a3b8;">Cargando mensajes...</div>';
    
    const txtRespuesta = document.getElementById('soporteRespuesta');
    txtRespuesta.value = '';
    
    document.getElementById('modalSoporte').classList.add('open');

    // Cargar hilo de mensajes
    fetch(`${BASE_URL}/controllers/admin/SoporteAdminController.php?accion=obtener_hilo&id=${m.id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderizarHilo(data.data);
            } else {
                historial.innerHTML = `<div style="text-align:center; color:red;">${data.message}</div>`;
            }
        })
        .catch(() => {
            historial.innerHTML = '<div style="text-align:center; color:red;">Error de conexión.</div>';
        });
}

function renderizarHilo(mensajes) {
    const historial = document.getElementById('soporteHistorialMensajes');
    historial.innerHTML = '';
    
    mensajes.forEach(msg => {
        const esAdmin = msg.remitente === 'admin';
        const bg = esAdmin ? '#e30613' : '#e2e8f0'; // Red for admin, gray for client
        const color = esAdmin ? '#ffffff' : '#0f172a';
        const align = esAdmin ? 'flex-end' : 'flex-start';
        const borderRadius = esAdmin ? '20px 20px 0 20px' : '20px 20px 20px 0';
        const timeColor = esAdmin ? 'rgba(255,255,255,0.7)' : '#64748b';
        
        const div = document.createElement('div');
        div.style.alignSelf = align;
        div.style.background = bg;
        div.style.color = color;
        div.style.padding = '12px 18px';
        div.style.borderRadius = borderRadius;
        div.style.maxWidth = '80%';
        div.style.boxShadow = '0 3px 6px rgba(0,0,0,0.08)';
        
        div.innerHTML = `
            <div style="margin-bottom: 5px; line-height: 1.4;">${msg.mensaje.replace(/\n/g, '<br>')}</div>
            <div style="font-size: 0.75em; text-align: right; color: ${timeColor};">${new Date(msg.fecha).toLocaleString()}</div>
        `;
        historial.appendChild(div);
    });
    
    // Auto-scroll
    historial.scrollTop = historial.scrollHeight;
}

function cerrarModalSoporte() {
    document.getElementById('modalSoporte').classList.remove('open');
}

function enviarRespuestaSoporte() {
    const id = document.getElementById('soporteId').value;
    const respuesta = document.getElementById('soporteRespuesta').value.trim();
    const btn = document.getElementById('btnResponderSoporte');

    if (!respuesta) {
        toast("Escribe una respuesta.", "error");
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    const formData = new FormData();
    formData.append('accion', 'responder');
    formData.append('id', id);
    formData.append('respuesta', respuesta);

    fetch(BASE_URL + '/controllers/admin/SoporteAdminController.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane" style="margin: 0;"></i>';

        if (data.success) {
            document.getElementById('soporteRespuesta').value = '';
            // Recargar hilo
            fetch(`${BASE_URL}/controllers/admin/SoporteAdminController.php?accion=obtener_hilo&id=${id}`)
                .then(r => r.json())
                .then(hilo => { if(hilo.success) renderizarHilo(hilo.data); });
                
            cargarSoporte();
        } else {
            toast(data.message, 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-paper-plane" style="margin: 0;"></i>';
        toast("Ocurrió un error.", "error");
    });
}
