<?php 
include 'conexion.php';
include 'header.php'; 

// Verificamos que el ID exista para evitar errores
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>Error: No se especifico un ID de ticket valido.</div>";
    include 'footer.php';
    exit;
}

$id = $_GET['id'];

// Consulta completa para jalar todos los datos incluyendo categoria y reportado_por
$stmt = $pdo->prepare("SELECT t.*, a.*, c.nombre_empresa, c.am_responsable 
                       FROM tickets t 
                       JOIN activos_contratos a ON t.id_activo = a.id_activo
                       JOIN clientes c ON a.id_cliente = c.id_cliente 
                       WHERE t.id_ticket = ?");
$stmt->execute([$id]);
$tk = $stmt->fetch();

if (!$tk) {
    echo "<div class='alert alert-warning'>Ticket no encontrado en la base de datos.</div>";
    include 'footer.php';
    exit;
}
?>

<div class="row mb-3 align-items-center">
    <div class="col-md-6">
        <h3 style="color: var(--color-primary);">
            <i class="bi bi-ticket-perforated"></i> Detalle del Ticket: <?php echo $tk['folio_ticket']; ?>
        </h3>
    </div>
    <div class="col-md-6 text-end">
        <a href="generar_pdf.php?id=<?php echo $tk['id_ticket']; ?>" class="btn btn-danger shadow-sm">
            <i class="bi bi-file-earmark-pdf"></i> Descargar PDF
        </a>
        <a href="dashboard.php" class="btn btn-outline-secondary shadow-sm ms-2">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<?php if($tk['estatus'] == 'Cerrado'): ?>
    <div class="alert alert-success d-flex align-items-center shadow-sm mb-4">
        <i class="bi bi-shield-check fs-3 me-3"></i>
        <div>
            <h6 class="mb-0 fw-bold text-uppercase">Autorizacion de cierre realizada</h6>
            <small>
                <strong>Autorizado por:</strong> <?php echo htmlspecialchars($tk['aprobado_por']); ?> 
                | <strong>Fecha y Hora:</strong> <?php echo date('d/m/Y H:i:s', strtotime($tk['fecha_cierre'])); ?>
            </small>
        </div>
    </div>
<?php endif; ?>

<div class="card card-custom shadow-sm mb-4">
    <div class="card-header-custom p-3">
        <i class="bi bi-info-square"></i> Informacion Completa de la Incidencia
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3 border-end">
                <h6 class="text-secondary small fw-bold text-uppercase">Cliente</h6>
                <p class="mb-1"><strong>Empresa:</strong> <?php echo $tk['nombre_empresa']; ?></p>
                <p class="mb-0"><strong>AM Responsable:</strong> <?php echo $tk['am_responsable']; ?></p>
            </div>
            
            <div class="col-md-3 border-end">
                <h6 class="text-secondary small fw-bold text-uppercase">Reporte Original</h6>
                <p class="mb-1"><strong>Reportado por:</strong> <?php echo $tk['reportado_por'] ?: 'N/A'; ?></p>
                <p class="mb-0"><strong>Apertura:</strong> <?php echo date('d/m/Y H:i:s', strtotime($tk['fecha_apertura'])); ?></p>
            </div>

            <div class="col-md-3 border-end">
                <h6 class="text-secondary small fw-bold text-uppercase">Clasificacion</h6>
                <p class="mb-1"><strong>Categoria:</strong> <span class="badge bg-info text-dark"><?php echo $tk['categoria'] ?: 'General'; ?></span></p>
                <p class="mb-0"><strong>Tecnologia:</strong> <?php echo $tk['tecnologia']; ?></p>
            </div>

            <div class="col-md-3">
                <h6 class="text-secondary small fw-bold text-uppercase">Estado del reporte</h6>
                <?php 
                    $badgeClass = match($tk['estatus']) {
                        'Abierto' => 'bg-danger',
                        'En Proceso' => 'bg-warning text-dark',
                        'Cerrado' => 'bg-success',
                        default => 'bg-primary'
                    };
                ?>
                <span class="badge <?php echo $badgeClass; ?> mb-2"><?php echo $tk['estatus']; ?></span><br>
                <small><strong>Fin de Contrato:</strong> <?php echo $tk['fin_contrato'] ?: 'Sin fecha'; ?></small>
            </div>
        </div>

        <hr>

        <div class="row mt-4">
            <div class="col-md-6 border-end">
                <h5 class="text-secondary"><i class="bi bi-cpu"></i> Hardware / Activo</h5>
                <p class="mb-1"><strong>Numero de Serie:</strong> <code class="text-dark fw-bold"><?php echo $tk['numeros_serie']; ?></code></p>
                <p class="mb-1"><strong>Numero de Parte:</strong> <?php echo $tk['numeros_parte'] ?: 'N/A'; ?></p>
                <p><strong>Smartnet:</strong> <span class="badge bg-light text-dark border"><?php echo $tk['smartnet'] ?: 'N/A'; ?></span></p>
            </div>
            <div class="col-md-6">
                <h5 class="text-secondary"><i class="bi bi-exclamation-triangle"></i> Falla Reportada</h5>
                <div class="p-3 bg-light border rounded mb-3">
                    <?php echo nl2br(htmlspecialchars($tk['descripcion_falla'])); ?>
                </div>
                
                <?php if($tk['hoja_firmada']): ?>
                    <div class="mt-2">
                        <a href="<?php echo $tk['hoja_firmada']; ?>" class="btn btn-sm btn-outline-danger w-100" target="_blank">
                            <i class="bi bi-file-pdf"></i> Ver Evidencia / Hoja Firmada
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border text-center py-1">
                        <small class="text-muted">No se adjunto evidencia digital.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>