<?php
include 'conexion.php';

echo "<h2>Estado de la base de datos en Supabase</h2>";

try {
    // En PostgreSQL (Supabase), consultamos information_schema para ver las tablas publicas
    $sql = "SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            ORDER BY table_name";
            
    $stmt = $pdo->query($sql);
    $tablas = $stmt->fetchAll();

    if (count($tablas) > 0) {
        echo "<p style='color: green;'>Conexion exitosa. Se encontraron las siguientes tablas:</p>";
        echo "<ul>";
        foreach ($tablas as $t) {
            echo "<li><strong>" . $t['table_name'] . "</strong></li>";
        }
        echo "</ul>";
        
        // Verificacion rapida de las tablas principales de tu proyecto
        $requeridas = ['tickets', 'activos_contratos', 'clientes', 'usuarios'];
        $existentes = array_column($tablas, 'table_name');
        
        echo "<h3>Validacion de estructura:</h3>";
        foreach ($requeridas as $req) {
            if (in_array($req, $existentes)) {
                echo "<span style='color: blue;'>[OK]</span> Tabla '$req' lista.<br>";
            } else {
                echo "<span style='color: red;'>[ERROR]</span> Falta la tabla '$req'.<br>";
            }
        }
    } else {
        echo "<p style='color: orange;'>Conexion establecida, pero la base de datos esta vacia.</p>";
        echo "<p>Copia el contenido de tu archivo <b>sistema_tickets_soporte.sql</b> y pegalo en el SQL Editor de Supabase.</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error critico: " . $e->getMessage() . "</p>";
    echo "<p>Recuerda que para Supabase el host es <b>db.gmcvkkywdxauuxlqovat.supabase.co</b> y el driver debe ser <b>pgsql</b>.</p>";
}
?>