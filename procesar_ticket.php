<?php
session_start();
include 'conexion.php';

// Aseguramos la zona horaria de Mexico para que coincida con tu reloj
date_default_timezone_set('America/Mexico_City');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. CAPTURA AUTOMATICA E IMPLICITA
    // Recuperamos el nombre directamente de la sesion abierta
    $reportado_por = $_SESSION['nombre']; 
    
    // Capturamos la fecha, hora, minuto y segundo exacto de la creacion
    $fecha_apertura = date('Y-m-d H:i:s'); 

    // 2. CAPTURA DE DATOS DEL FORMULARIO
    $id_activo         = $_POST['id_activo'];
    $categoria         = $_POST['categoria'];
    $descripcion_falla = trim($_POST['descripcion_falla']);
    
    // 3. GENERACION AUTOMATICA DEL FOLIO (CORREGIDO PARA SUPABASE)
    $anio_actual = date("Y");
    
    // Cambiamos YEAR() por EXTRACT(YEAR FROM ...) que es lo que entiende Postgres
    $sql_count = "SELECT COUNT(*) FROM tickets WHERE EXTRACT(YEAR FROM fecha_apertura) = $anio_actual";
    $stmtFolio = $pdo->query($sql_count);
    $cantidad_tickets = $stmtFolio->fetchColumn() + 1;
    
    $folio_ticket = "TKT-" . $anio_actual . "-" . str_pad($cantidad_tickets, 4, "0", STR_PAD_LEFT);

    // 4. CONFIGURACION Y PROCESAMIENTO DE ARCHIVOS
    $ruta_archivo = null;
    $limite_peso  = 10 * 1024 * 1024; // 10 MB

    if (isset($_FILES['hoja_firmada']) && $_FILES['hoja_firmada']['error'] == 0) {
        
        if ($_FILES['hoja_firmada']['size'] <= $limite_peso) {
            $directorio_subida = 'uploads/';
            if (!file_exists($directorio_subida)) {
                mkdir($directorio_subida, 0777, true);
            }

            $nombre_archivo = basename($_FILES['hoja_firmada']['name']);
            $extension      = pathinfo($nombre_archivo, PATHINFO_EXTENSION);
            $nombre_final   = $folio_ticket . "_evidencia." . $extension;
            $ruta_destino   = $directorio_subida . $nombre_final;

            if (move_uploaded_file($_FILES['hoja_firmada']['tmp_name'], $ruta_destino)) {
                $ruta_archivo = $ruta_destino;
            }
        }
    }

    try {
        // 5. INSERCION EN BASE DE DATOS
        // Las columnas deben coincidir con tu tabla de Supabase
        $sql = "INSERT INTO tickets (id_activo, folio_ticket, categoria, reportado_por, fecha_apertura, descripcion_falla, estatus, hoja_firmada) 
                VALUES (?, ?, ?, ?, ?, ?, 'Abierto', ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $id_activo, 
            $folio_ticket, 
            $categoria, 
            $reportado_por, 
            $fecha_apertura, 
            $descripcion_falla, 
            $ruta_archivo
        ]);

        // Redireccion al Dashboard con el folio generado
        header("Location: dashboard.php?msg=exito&folio=" . $folio_ticket);
        exit();

    } catch (PDOException $e) {
        // En caso de error, puedes imprimir $e->getMessage() para debuggear si falla de nuevo
        header("Location: dashboard.php?error=db");
        exit();
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>