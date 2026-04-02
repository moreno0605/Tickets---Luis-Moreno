<?php
// Verificamos si la sesion ya esta activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpiamos los datos de entrada
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Por favor llena todos los campos'); window.location='index.php';</script>";
        exit();
    }

    try {
        // Busqueda en la tabla usuarios de Supabase
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Validacion con password_verify para mayor seguridad
        if ($user && password_verify($password, $user['password'])) {
            // Guardamos datos clave en la sesion
            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['rol']        = $user['rol'];
            
            // Redireccion al dashboard principal
            header("Location: dashboard.php");
            exit();
        } else {
            // Mensaje de error de autenticación
            echo "<script>alert('Datos incorrectos. Intenta de nuevo'); window.location='index.php';</script>";
            exit();
        }
    } catch (PDOException $e) {
        die("Error en el sistema de autenticacion: " . $e->getMessage());
    }
} else {
    // Si intentan entrar sin POST, mandamos al login
    header("Location: index.php");
    exit();
}
?>