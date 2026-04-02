<?php 
include 'conexion.php';
include 'header.php'; 

// 1. Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cliente = $_POST['id_cliente'];
    $desc_prod = $_POST['descripcion_producto'];
    $tecnologia = $_POST['tecnologia'];
    $oportunidad = $_POST['oportunidad'];
    $pedido = $_POST['pedido'];
    $fecha_oc = $_POST['fecha_oc'];
    $config = $_POST['configuracion'];
    $smartnet = $_POST['smartnet'];
    $no_contrato = $_POST['no_contrato'];
    $fin_contrato = $_POST['fin_contrato'];
    $n_parte = $_POST['numeros_parte'];
    $n_serie = $_POST['numeros_serie'];

    $sql = "INSERT INTO activos_contratos (id_cliente, descripcion_producto, tecnologia, oportunidad, pedido, fecha_oc, configuracion, smartnet, no_contrato, fin_contrato, numeros_parte, numeros_serie) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$id_cliente, $desc_prod, $tecnologia, $oportunidad, $pedido, $fecha_oc, $config, $smartnet, $no_contrato, $fin_contrato, $n_parte, $n_serie])) {
        echo "<div class='alert alert-success'>¡Activo registrado con éxito!.</div>";
    } else {
        echo "<div class='alert alert-danger'>Hubo un error al guardar.</div>";
    }
}

// 2. Traer la lista de clientes para el dropdown
$clientes = $pdo->query("SELECT id_cliente, nombre_empresa FROM clientes ORDER BY nombre_empresa ASC")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card card-custom mb-5">
            <div class="card-header-custom p-3">
                <i class="bi bi-cpu-fill"></i> Registrar Nuevo Activo / Contrato
            </div>
            <div class="card-body">
                <form action="agregar_activo.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Cliente</label>
                            <select name="id_cliente" class="form-select" required>
                                <option value="">-- Selecciona --</option>
                                <?php foreach($clientes as $c): ?>
                                    <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nombre_empresa']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tecnología</label>
                            <input type="text" name="tecnologia" class="form-control" placeholder="Networking, Seguridad, etc." required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Descripción del Producto Vendido</label>
                            <textarea name="descripcion_producto" class="form-control" rows="2" placeholder="Ej: Switch Catalyst 9200L 24 puertos"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Serie</label>
                            <input type="text" name="numeros_serie" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número de Parte</label>
                            <input type="text" name="numeros_parte" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Smartnet / Support ID</label>
                            <input type="text" name="smartnet" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">Oportunidad</label>
                            <input type="text" name="oportunidad" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Pedido</label>
                            <input type="text" name="pedido" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fecha OC</label>
                            <input type="date" name="fecha_oc" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Fin de Contrato</label>
                            <input type="date" name="fin_contrato" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">No. de Contrato</label>
                            <input type="text" name="no_contrato" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Configuración Especial</label>
                            <input type="text" name="configuracion" class="form-control" placeholder="VLANs, IPs, etc.">
                        </div>

                        <div class="col-12 text-end mt-4">
                            <a href="dashboard.php" class="btn btn-light">Cancelar</a>
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-save"></i> Guardar Activo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>