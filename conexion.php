<?php
date_default_timezone_set('America/Mexico_City');

$host     = 'aws-1-us-east-1.pooler.supabase.com'; // Connection String
$port     = '6543'; // Puerto de pooling (IPv4 compatible)
$dbname   = 'postgres'; 
$user     = 'postgres.gmcvkkywdxauuxlqovat'; // Usuario con ID de proyecto
$password = 'Planeta@0605.'; // Contraseña del proyecto

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // Conexión nexitosa
} catch (PDOException $e) {
    die("Error critico de conexion: " . $e->getMessage());
}
?>