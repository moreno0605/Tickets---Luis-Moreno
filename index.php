<?php 
session_start();
$no_navbar = true; // Variable para ocultar el menú en el login
include 'conexion.php';
include 'header.php'; 
?>

<div class="row justify-content-center align-items-center vh-100">
    <div class="col-md-4">
        <div class="text-center mb-4">
            <img src="img/logo.png" alt="Logo Empresa" width="150">
            <h4 class="mt-3" style="color: var(--color-primary);">Acceso a Soporte</h4>
        </div>
        
        <div class="card card-custom p-4">
            <form action="auth.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary-custom">Iniciar Sesión</button>
                </div>
            </form>
        </div>
        <div class="text-center mt-3">
            <small class="text-muted">© 2026 Total Support - Gestion de Soporte</small>
        </div>
    </div>
</div>

</body>
</html>