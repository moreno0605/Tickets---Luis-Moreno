<?php
session_start();
include 'conexion.php';

// Seguridad basica: Verificar que exista una sesion
if (!isset($_SESSION['rol'])) {
    die("Acceso denegado. Inicie sesion.");
}

if (isset($_GET['id']) && isset($_GET['nuevo_estatus'])) {
    $id = (int)$_GET['id'];
    $nuevo_estatus = $_GET['nuevo_estatus'];

    // LOGICA DE CIERRE: Solo administradores
    if ($nuevo_estatus == 'Cerrado') {
        
        // REGLA: Solo el administrador puede autorizar el cierre del reporte
        if ($_SESSION['rol'] != 'Administrador') {
            die("Acceso denegado. Solo un administrador puede autorizar el cierre del reporte.");
        }

        // Se captura el tiempo exacto con segundos
        $fecha_cierre = date('Y-m-d H:i:s');
        
        // El nombre se recupera automaticamente de la cuenta del administrador
        $autorizado_por = $_SESSION['nombre'];

        $sql = "UPDATE tickets SET estatus = ?, fecha_cierre = ?, aprobado_por = ? WHERE id_ticket = ?";
        $stmt = $pdo->prepare($sql);
        $ejecutado = $stmt->execute([$nuevo_estatus, $fecha_cierre, $autorizado_por, $id]);

    } else {
        // Para otros estados (Abierto o En Proceso), limpiamos la fecha de cierre y autorizacion
        $fecha_cierre = null;
        $autorizado_por = null;
        
        $sql = "UPDATE tickets SET estatus = ?, fecha_cierre = ?, aprobado_por = ? WHERE id_ticket = ?";
        $stmt = $pdo->prepare($sql);
        $ejecutado = $stmt->execute([$nuevo_estatus, $fecha_cierre, $autorizado_por, $id]);
    }

    if ($ejecutado) {
        // Redireccion con mensaje de exito
        header("Location: dashboard.php?msg=EstatusActualizado");
        exit();
    } else {
        echo "Error tecnico al intentar actualizar el estatus.";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>