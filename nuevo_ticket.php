<?php 
include 'conexion.php';
include 'header.php'; 

// Obtenemos la lista de clientes para el primer desplegable
$stmtClientes = $pdo->query("SELECT id_cliente, nombre_empresa FROM clientes ORDER BY nombre_empresa ASC");
$clientes = $stmtClientes->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="color: var(--color-primary);"><i class="bi bi-ticket-detailed"></i> Levantar Nuevo Ticket</h3>
            <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Volver al Dashboard</a>
        </div>

        <div class="card card-custom shadow-sm">
            <div class="card-header-custom p-3">
                Informacion de la Incidencia
            </div>
            <div class="card-body">
                <form action="procesar_ticket.php" method="POST" enctype="multipart/form-data" id="formTicket">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_cliente" class="form-label fw-bold">1. Selecciona el Cliente</label>
                            <select class="form-select" id="id_cliente" name="id_cliente" required onchange="cargarEquipos(this.value)">
                                <option value="" selected disabled>-- Elige un cliente --</option>
                                <?php foreach($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id_cliente']; ?>">
                                        <?php echo htmlspecialchars($cliente['nombre_empresa']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="id_activo" class="form-label fw-bold">2. Equipo / Numero de Serie</label>
                            <select class="form-select" id="id_activo" name="id_activo" required disabled>
                                <option value="" selected>Primero selecciona un cliente</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="categoria" class="form-label fw-bold">3. Categoria del Servicio</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="" selected disabled>-- Selecciona una categoria --</option>
                            <option value="Colaboracion">Colaboracion</option>
                            <option value="Data Center">Data Center</option>
                            <option value="Data Center Infraestructura">Data Center Infraestructura</option>
                            <option value="Networking">Networking</option>
                            <option value="Seguridad Fisica">Seguridad Fisica</option>
                            <option value="Seguridad Logica">Seguridad Logica</option>
                            <option value="Servicios">Servicios</option>
                            <option value="Cableado UPI">Cableado UPI</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion_falla" class="form-label fw-bold">4. Descripcion del Problema</label>
                        <textarea class="form-control" id="descripcion_falla" name="descripcion_falla" rows="4" placeholder="Detalla el problema o codigos de error..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="hoja_firmada" class="form-label fw-bold">5. Evidencia / Hoja Firmada (Opcional)</label>
                        <input class="form-control" type="file" id="hoja_firmada" name="hoja_firmada" accept=".pdf, .jpg, .jpeg, .png">
                        <div id="error-archivo" class="text-danger fw-bold mt-1" style="display:none; font-size: 0.85rem;">
                            <i class="bi bi-exclamation-circle"></i> El archivo debe ser menor a 10 MB.
                        </div>
                        <div class="form-text text-muted">Formatos permitidos: PDF o Imagenes (Max 10 MB).</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="reset" class="btn btn-light me-md-2" onclick="document.getElementById('error-archivo').style.display='none'">Limpiar</button>
                        <button type="submit" class="btn btn-primary-custom" id="btnGenerar"><i class="bi bi-save"></i> Generar Ticket</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Validacion de peso de archivo
document.getElementById('hoja_firmada').addEventListener('change', function() {
    const file = this.files[0];
    const errorDiv = document.getElementById('error-archivo');
    const maxSize = 10 * 1024 * 1024; 

    if (file) {
        if (file.size > maxSize) {
            errorDiv.style.display = 'block';
            this.classList.add('is-invalid');
            this.value = ""; 
        } else {
            errorDiv.style.display = 'none';
            this.classList.remove('is-invalid');
        }
    }
});

// Funcion AJAX para equipos
function cargarEquipos(idCliente) {
    const selectActivo = document.getElementById('id_activo');
    
    if(!idCliente) {
        selectActivo.innerHTML = '<option value="">Primero selecciona un cliente</option>';
        selectActivo.disabled = true;
        return;
    }

    selectActivo.disabled = true;
    selectActivo.innerHTML = '<option value="">Buscando equipos...</option>';

    fetch(`obtener_equipos.php?cliente_id=${idCliente}`)
        .then(response => {
            if (!response.ok) throw new Error('Error en la red');
            return response.text();
        })
        .then(html => {
            selectActivo.innerHTML = html;
            selectActivo.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            selectActivo.innerHTML = '<option value="">Error al cargar equipos</option>';
        });
}
</script>

<?php include 'footer.php'; ?>