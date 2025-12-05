<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD: Verificar sesión Y que sea ADMIN
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'vecino') {
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
    <title>Selección de Alerta - UniAlert</title>

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
            <img src="../Multimedia/VecinoAvatar.png" alt="Avatar" class="user-avatar">
            
            <div class="user-info">
                <span class="user-name"><?php echo $nombreMostrar; ?></span>
                <span class="user-details"><?php echo $detallesMostrar; ?></span>
            </div>
            <a href="#" id="btn-logout-trigger" class="btn-logout" title="Cerrar Sesión" style="margin-left:20px; color:#ff6b6b; text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
            </a>
        </div>
    </header>

    <main class="admin-container">
        
        <div class="page-header">
            <h1>Seleccione el tipo de alerta que desea realizar</h1>
            <p>Solo en caso de Emergencias</p>
        </div>

        <div class="action-list">
            
            <div class="action-card">
                <img src="../Multimedia/Sevicios Medicos.jpg" alt="Icono Médicos" class="large-card-icon">
                <div class="card-content">
                    <h3>Alerta de Servicios Medicos</h3>
                    <p>Esta alerta avisará al Vigilante de la unidad habitacional para que llame a algun servicio de emergencia medica.</p>
                    <a href="UFormularioSM.php" class="card-button">Alertar</a>
                </div>
            </div>

            <div class="action-card">
                <img src="../Multimedia/Seguridad.jpg" alt="Icono Policía" class="large-card-icon">
                <div class="card-content">
                    <h3>Alerta Servicios de Seguridad</h3>
                    <p>Esta alerta avisará al vigilante de la unidad habitacional para que llame a algun servicio de seguridad.</p>
                    <a href="UFormularioSS.php" class="card-button">Alertar</a>
                </div>
            </div>

        </div>

    </main>

    <footer class="warning-footer">
        <strong class="warning-title">Advertencia</strong>
        <p>Esta es una herramienta para salvar vidas. El uso indebido, las falsas alarmas o las bromas desvian recursos de emergencias reales y pueden poner en riesgo a otros. <br>Todo abuso de este servicio será rastreado y sancionado con todo el rigor de la ley. Use esta función únicamente en una situación de emergencia real.</p>
    </footer>
    <div class="modal-overlay" id="logout-modal">
        <div class="modal-box">
            <h2 class="modal-title">¿Cerrar sesión?</h2>
            <p class="modal-text">Estás a punto de salir del sistema. ¿Estás seguro?</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" id="logout-cancel">Cancelar</button>
                <button class="btn btn-primary" id="logout-confirm" style="background-color: #d93025; border-color: #d93025;">Sí, salir</button>
            </div>
        </div>
    </div>
    <script>
        // Elementos del Logout
        const logoutBtn = document.getElementById('btn-logout-trigger');
        const logoutModal = document.getElementById('logout-modal');
        const logoutCancel = document.getElementById('logout-cancel');
        const logoutConfirm = document.getElementById('logout-confirm');

        // 1. Mostrar el modal al hacer clic en el icono de salir
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Evita que el enlace haga algo
            logoutModal.classList.add('modal-visible');
        });

        // 2. Ocultar si cancela
        logoutCancel.addEventListener('click', function() {
            logoutModal.classList.remove('modal-visible');
        });

        // 3. Cerrar sesión si confirma (Redirige al PHP)
        logoutConfirm.addEventListener('click', function() {
            window.location.href = '../Backend/logout.php';
        });

        // 4. Cerrar si da clic fuera del cuadro
        logoutModal.addEventListener('click', function(e) {
            if (e.target === logoutModal) {
                logoutModal.classList.remove('modal-visible');
            }
        });
    </script>
</body>
</html>