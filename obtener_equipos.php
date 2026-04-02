<?php
include 'conexion.php';

if (isset($_GET['cliente_id'])) {
    $cliente_id = (int)$_GET['cliente_id'];
    
    // El SELECT debe coincidir EXACTO con tus nombres de tabla
    $stmt = $pdo->prepare("SELECT id_activo, tecnologia, numeros_serie, smartnet 
                           FROM activos_contratos 
                           WHERE id_cliente = ? AND estatus_activo = 'Activo'");
    $stmt->execute([$cliente_id]);
    $equipos = $stmt->fetchAll();

    if (count($equipos) > 0) {
        echo '<option value="" selected disabled>-- Selecciona el equipo --</option>';
        foreach ($equipos as $equipo) {
            $texto = "Serie: " . $equipo['numeros_serie'] . " - " . $equipo['tecnologia'];
            echo "<option value='{$equipo['id_activo']}'>{$texto}</option>";
        }
    } else {
        echo '<option value="" disabled>No hay equipos activos para este cliente</option>';
    }
}
?>