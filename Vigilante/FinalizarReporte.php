<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD: Verificar sesión Y que sea ADMIN
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'vigilante') {
    header("Location: ../Login/Login.html");
    exit();
}

// 2. Obtener datos del usuario
$idUsuario = $_SESSION['IdUsuario'];
$rolSesion = $_SESSION['Rol']; 

$sql = "SELECT Nombre, ApPaterno, Edificio, Departamento FROM Usuarios WHERE IdUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos = $resultado->fetch_assoc();

// 3. Formatear Nombre
$nombreMostrar = $datos['Nombre'] . " " . $datos['ApPaterno'];

// 4. Lógica Inteligente para el Subtítulo
$detallesMostrar = "";

if (!empty($datos['Edificio']) && !empty($datos['Departamento'])) {
    $detallesMostrar = "Edif. " . $datos['Edificio'] . " - Depto. " . $datos['Departamento'];
} else {
    $detallesMostrar = $rolSesion; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Reporte - UniAlert</title>

    <link rel="stylesheet" href="../CSS/style.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="body-admin">

    <header class="navbar">
        <div class="navbar-left">
            <img src="../Multimedia/Unialert Logo.png" alt="Logo de UniAlert" class="navbar-logo">
            <span class="navbar-logo-text">UNIALERT</span>
        </div>
        <div class="navbar-right">
            <img src="../Multimedia/User Avatar.png" alt="Avatar" class="user-avatar">
            
            <div class="user-info">
                <span class="user-name"><?php echo $nombreMostrar; ?></span>
                
                <span class="user-details"><?php echo $detallesMostrar; ?></span>
            </div>
        </div>
    </header>

    <main class="admin-container">

        <div class="form-card" style="max-width: 600px; margin: 0 auto;">
            
            <h2 style="margin-bottom: 8px; font-size: 20px;">Formulario de finalización de reporte</h2>
            <p style="font-size: 14px; color: #666; margin-bottom: 24px; line-height: 1.5;">
                Finaliza el reporte de alerta vecinal con el tipo de alerta, observaciones y si fue necesario solicitar apoyo médico o de seguridad.
            </p>

            <form action="#" method="POST">
                
                <div class="form-group">
                    <label for="tipo-alerta">Seleccione el tipo de alerta</label>
                    <select id="tipo-alerta" class="form-input" style="width: auto; min-width: 200px;">
                        <option value="" disabled selected>Alerta</option>
                        <option value="medica">Alerta Médica</option>
                        <option value="seguridad">Alerta de Seguridad</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="tipo-apoyo">¿Si fue necesario solicitar apoyo qué tipo de servicio se solicitó?</label>
                    <select id="tipo-apoyo" class="form-input" style="width: auto; min-width: 200px;">
                        <option value="" disabled selected>Apoyo</option>
                        <option value="publico">Servicio Público (Ambulancia/Policía)</option>
                        <option value="privado">Servicio Privado</option>
                        <option value="ninguno">No fue necesario</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="observaciones">Menciona las observaciones de este reporte</label>
                    <textarea id="observaciones" class="form-input" rows="4" placeholder="Escribir un reporte"></textarea>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="submit-button" style="padding: 10px 24px;">Envíar</button>
                </div>

            </form>
        </div>

    </main>

</body>
</html>