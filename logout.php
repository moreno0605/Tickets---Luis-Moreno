<?php
session_start();
session_unset(); // Limpia todas las variables de sesion
session_destroy(); // Destruye la sesion por completo

// Redirige al login principal
header("Location: index.php");
exit();
?>