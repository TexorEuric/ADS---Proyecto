<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD: Verificar sesión Y que sea ADMIN
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
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
    <title>Panel de Administración - UniAlert</title>

    <link rel="stylesheet" href="../CSS/style.css">
    
    <style>
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ff6b6b; /* Un rojo suave */
            padding: 8px;
            border-radius: 8px;
            margin-left: 20px; /* Separación de los datos del usuario */
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-logout:hover {
            background-color: rgba(255, 107, 107, 0.1);
            border-color: #ff6b6b;
        }

        /* Pequeña línea divisoria vertical antes del botón */
        .navbar-right {
            display: flex;
            align-items: center;
        }
    </style>

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
            <h1>Selecciona la Opción de la Acción que deseas realizar:</h1>
            <p>Hay opciones que solo deben ser usadas en caso de emergencia.</p>
        </div>

        <div class="action-list">
            <div class="action-card">
                <img src="../Multimedia/Sevicios Medicos.jpg" alt="Icono Servicios Médicos" class="card-icon">
                <div class="card-content">
                    <h3>Alerta de Servicios Medicos</h3>
                    <p>Esta alerta avisará al Vigilante de la unidad habitacional para que llame a algun servicio de emergencia medica.</p>
                    <a href="FSM.php" class="card-button">Acceder a Formulario</a>
                </div>
            </div>

            <div class="action-card">
                <img src="../Multimedia/Seguridad.jpg" alt="Icono Servicios de Seguridad" class="card-icon">
                <div class="card-content">
                    <h3>Alerta Servicios de Seguridad</h3>
                    <p>Esta alerta avisará al vigilante de la unidad habitacional para que llame a algun servicio de seguridad.</p>
                    <a href="FSS.php" class="card-button">Acceder a Formulario</a>
                </div>
            </div>

            <div class="action-card">
                <img src="../Multimedia/Alta.jpg" alt="Icono Nuevo Usuario" class="card-icon">
                <div class="card-content">
                    <h3>Añadir Nuevo Usuario</h3>
                    <p>Con esta acción se abrirá el formulario para registrar a un nuevo vecino.</p>
                    <a href="AñadirUsuarios.php" class="card-button">Añadir</a>
                </div>
            </div>
            
            <div class="action-card">
                <img src="../Multimedia/AdminUsuarios.png" alt="Icono Administración de Usuarios" class="card-icon">
                <div class="card-content">
                    <h3>Administración de Usuarios</h3>
                    <p>Esta acción se podra tener un control de los usuarios activos e inactivos, asi como su eliminación y edición.</p>
                    <a href="AdminUsuarios.php" class="card-button">Acceder</a>
                </div>
            </div>

            <div class="action-card">
                <img src="../Multimedia/RevisionReporte.png" alt="Icono Revisión de Reportes" class="card-icon">
                <div class="card-content">
                    <h3>Revisión y Aprobación de Reportes</h3>
                    <p>En este apartado se podrán revisar los reportes pendientes que hayan sido rellenados por el vigilante y devueltos o aprobados.</p>
                    <a href="ReportesSA.php" class="card-button">Acceder</a>
                </div>
            </div>
            
            <div class="action-card">
                <img src="../Multimedia/Historial.png" alt="Icono Historial de Reportes" class="card-icon">
                <div class="card-content">
                    <h3>Historial de Reportes</h3>
                    <p>Esta acción se podra tener un control de los usuarios activos e inactivos, asi como su eliminación y edición.</p>
                    <a href="HistorialReportes.php" class="card-button">Acceder</a>
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