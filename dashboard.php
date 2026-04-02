<?php 
// 1. INICIO DE SESION Y SEGURIDAD
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Si no hay sesion iniciada, mandamos al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

include 'conexion.php';
include 'header.php'; 

// 2. OBTENER EL CONTEO REAL DE TICKETS ABIERTOS EN LA NUBE
$stmt_count = $pdo->query("SELECT COUNT(*) FROM tickets WHERE estatus = 'Abierto'");
$total_abiertos = $stmt_count->fetchColumn();

// 3. CONSULTA PARA LA TABLA (Postgres compatible)
$sql = "SELECT t.*, c.nombre_empresa, a.smartnet, a.tecnologia 
        FROM tickets t 
        JOIN activos_contratos a ON t.id_activo = a.id_activo
        JOIN clientes c ON a.id_cliente = c.id_cliente 
        ORDER BY t.fecha_apertura DESC";
$stmt = $pdo->query($sql);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 style="color: var(--color-primary);">Panel de Control</h2>
        <p class="text-muted">Gestion de incidencias y garantias</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="nuevo_ticket.php" class="btn btn-secondary-custom btn-lg shadow-sm">
            <i class="bi bi-plus-lg"></i> Crear Nuevo Ticket
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-custom text-center p-3 shadow-sm border-0">
            <h3 class="text-primary fw-bold"><?php echo $total_abiertos; ?></h3>
            <small class="text-uppercase fw-semibold">Tickets Abiertos</small>
        </div>
    </div>
</div>

<div class="card card-custom shadow-sm border-0">
    <div class="card-header-custom p-3 bg-white border-bottom">
        <i class="bi bi-list-task"></i> Tickets Recientes
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Tecnologia</th>
                        <th>Smartnet</th>
                        <th>Falla Reportada</th>
                        <th>Estatus</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch()): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?php echo $row['folio_ticket']; ?></td>
                        <td><?php echo htmlspecialchars($row['nombre_empresa']); ?></td>
                        <td><?php echo htmlspecialchars($row['tecnologia']); ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['smartnet']); ?></span></td>
                        <td>
                            <small>
                                <?php echo (strlen($row['descripcion_falla']) > 45) ? htmlspecialchars(substr($row['descripcion_falla'], 0, 45)) . '...' : htmlspecialchars($row['descripcion_falla']); ?>
                            </small>
                        </td>
                        <td>
                            <?php 
                            $badgeColor = match($row['estatus']) {
                                'Abierto' => 'bg-danger',
                                'En Proceso' => 'bg-warning text-dark',
                                'Cerrado' => 'bg-success',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $badgeColor; ?>"><?php echo $row['estatus']; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="ver_ticket.php?id=<?php echo $row['id_ticket']; ?>" class="btn btn-sm btn-outline-primary" title="Ver Detalle">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador' && $row['estatus'] != 'Cerrado'): ?>
                                    <a href="actualizar_estatus.php?id=<?php echo $row['id_ticket']; ?>&nuevo_estatus=Cerrado" 
                                       class="btn btn-sm btn-outline-success ms-1" 
                                       onclick="return confirm('Confirmar autorizacion de cierre para este reporte?')" 
                                       title="Autorizar Cierre">
                                         <i class="bi bi-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div> <?php include 'footer.php'; ?>