<?php 
include 'conexion.php';
include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $empresa = $_POST['nombre_empresa'];
    $am = $_POST['am_responsable'];
    $contacto = $_POST['contacto_nombre'];
    $email = $_POST['contacto_email'];

    $sql = "INSERT INTO clientes (nombre_empresa, am_responsable, contacto_nombre, contacto_email) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$empresa, $am, $contacto, $email])) {
        echo "<div class='alert alert-success'>Cliente registrado con éxito.</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-custom">
            <div class="card-header-custom p-3">Registrar Nuevo Cliente</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Empresa</label>
                        <input type="text" name="nombre_empresa" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">AM Responsable</label>
                        <input type="text" name="am_responsable" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Contacto</label>
                        <input type="text" name="contacto_nombre" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email de Contacto</label>
                        <input type="email" name="contacto_email" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary-custom w-100">Guardar Cliente</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>