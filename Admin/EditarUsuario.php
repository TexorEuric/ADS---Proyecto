<?php
session_start();
include '../Backend/conexion.php';

// --- 1. SEGURIDAD: Verificar sesión Y que sea ADMIN ---
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
    header("Location: ../Login/Login.html");
    exit();
}

// --- 2. DATOS DEL ADMIN (Para la Navbar) ---
// Obtenemos los datos de QUIEN está logueado para mostrar en la esquina
$idAdmin = $_SESSION['IdUsuario'];
$rolSesion = $_SESSION['Rol'];

$sqlAdmin = "SELECT Nombre, ApPaterno, Edificio, Departamento FROM Usuarios WHERE IdUsuario = ?";
$stmtA = $conn->prepare($sqlAdmin);
$stmtA->bind_param("i", $idAdmin);
$stmtA->execute();
$resA = $stmtA->get_result();
$datosAdmin = $resA->fetch_assoc();

$nombreMostrar = $datosAdmin['Nombre'] . " " . $datosAdmin['ApPaterno'];
$detallesMostrar = (!empty($datosAdmin['Edificio']) && !empty($datosAdmin['Departamento'])) 
    ? "Edif. " . $datosAdmin['Edificio'] . " - Depto. " . $datosAdmin['Departamento'] 
    : $rolSesion;


// --- 3. DATOS DEL USUARIO A EDITAR (Para el Formulario) ---
if (!isset($_GET['id'])) {
    header("Location: AdminUsuarios.php"); // Si no hay ID en la URL, regresar a la lista
    exit();
}

$idEditar = $_GET['id'];

// Traemos datos personales (Usuarios) y de login (Credenciales)
$sqlUser = "SELECT U.*, C.NickName, C.Contrasena 
            FROM Usuarios U 
            JOIN Credenciales C ON U.IdUsuario = C.IdUsuario 
            WHERE U.IdUsuario = ?";
            
$stmtU = $conn->prepare($sqlUser);
$stmtU->bind_param("i", $idEditar);
$stmtU->execute();
$resU = $stmtU->get_result();
$usuario = $resU->fetch_assoc();

// Si el usuario no existe, mostrar error
if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario - UniAlert</title>

    <link rel="stylesheet" href="../CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="body-admin">

    <header class="navbar">
        <div class="navbar-left">
            <img src="../Multimedia/Unialert Logo.png" alt="Logo de UniAlert" class="navbar-logo">
            <span class="navbar-logo-text">UNIALERT</span>
        </div>
        <div class="navbar-right">
            <img src="../Multimedia/AdminAvatar.png" alt="Avatar" class="user-avatar">
            <div class="user-info">
                <span class="user-name"><?php echo $nombreMostrar; ?></span>
                <span class="user-details"><?php echo $detallesMostrar; ?></span>
            </div>
        </div>
    </header>

    <main class="admin-container">
        
        <button onclick="history.back()" class="btn-back-global">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Volver
        </button>

        <h1 class="form-page-title form-page-title-user">Editar Usuario</h1>

        <div class="form-card">
            <form action="../Backend/actualizar_usuario.php" method="POST">
                
                <input type="hidden" name="id_usuario" value="<?php echo $usuario['IdUsuario']; ?>">
                
                <div class="form-grid">
                    
                    <div class="form-column">
                        <div class="form-group">
                            <label>Nombre(s)</label>
                            <input type="text" name="nombre" class="form-input" value="<?php echo $usuario['Nombre']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Apellido Paterno</label>
                            <input type="text" name="ap_paterno" class="form-input" value="<?php echo $usuario['ApPaterno']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Apellido Materno</label>
                            <input type="text" name="ap_materno" class="form-input" value="<?php echo $usuario['ApMaterno']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="correo" class="form-input" value="<?php echo $usuario['Correo']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        
                        <div class="form-group">
                            <label>Nombre de Usuario (Login)</label>
                            <input type="text" name="nickname" class="form-input" value="<?php echo $usuario['NickName']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Edificio</label>
                            <select name="edificio" class="form-input">
                                <option value="" disabled>Seleccionar</option>
                                <option value="A1" <?php if($usuario['Edificio']=='A1') echo 'selected'; ?>>Edificio A1</option>
                                <option value="A2" <?php if($usuario['Edificio']=='A2') echo 'selected'; ?>>Edificio A2</option>
                                <option value="B1" <?php if($usuario['Edificio']=='B1') echo 'selected'; ?>>Edificio B1</option>
                                <option value="B2" <?php if($usuario['Edificio']=='B2') echo 'selected'; ?>>Edificio B2</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Departamento</label>
                            <input type="text" name="departamento" class="form-input" value="<?php echo $usuario['Departamento']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="text" name="password" class="form-input" value="<?php echo $usuario['Contrasena']; ?>" required>
                        </div>
                    </div>
                    
                </div>
                
                <div class="form-actions">
                    <a href="AdminUsuarios.php" class="btn btn-secondary" style="text-decoration:none; margin-right:10px;">Cancelar</a>
                    <button type="submit" class="submit-button">Guardar Cambios</button>
                </div>
            </form>
        </div>

    </main>
</body>
</html>