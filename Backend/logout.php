<?php
session_start();
session_unset();   // Limpia las variables de sesión
session_destroy(); // Destruye la sesión por completo

// Redirigir al Login
header("Location: ../Login/Login.html");
exit();
?>