<?php
// Iniciamos sesion solo si no se ha iniciado antes
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="css/custom.css" rel="stylesheet">
    <style>
        .navbar-custom { background-color: #0e2d5c; }
        .nav-link { color: rgba(255,255,255,0.8) !important; }
        .nav-link:hover { color: #fff !important; }
        /* Aseguramos que el dropdown este por encima de todo */
        .dropdown-menu { z-index: 9999 !important; }
    </style>
</head>
<body>

<?php if(!isset($no_navbar)): ?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
        <img src="img/logo.png" alt="Logo" height="40" class="me-2">
        <span>Gestion de Soporte</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>

        <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador'): ?>
            <li class="nav-item"><a class="nav-link" href="agregar_cliente.php"><i class="bi bi-people"></i> Clientes</a></li>
            <li class="nav-item"><a class="nav-link" href="agregar_activo.php"><i class="bi bi-box-seam"></i> Inventario</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="nuevo_ticket.php"><i class="bi bi-plus-circle"></i> Nuevo Ticket</a></li>
        
        <li class="nav-item ms-lg-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nombre'] ?? 'Usuario') ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenu">
                    <li><h6 class="dropdown-header">Rol: <?= htmlspecialchars($_SESSION['rol'] ?? 'Soporte') ?></h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<div class="container mt-4">