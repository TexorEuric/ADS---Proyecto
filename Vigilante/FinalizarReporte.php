<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'vigilante') {
    header("Location: ../Login/Login.html");
    exit();
}

// 2. Datos del usuario
$idUsuario = $_SESSION['IdUsuario'];
$rolSesion = $_SESSION['Rol'];

$sql = "SELECT Nombre, ApPaterno, Edificio, Departamento FROM usuarios WHERE IdUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos = $resultado->fetch_assoc();

$nombreMostrar = $datos['Nombre'] . " " . $datos['ApPaterno'];
$detallesMostrar = (!empty($datos['Edificio']) && !empty($datos['Departamento'])) 
    ? "Edif. " . $datos['Edificio'] . " - Depto. " . $datos['Departamento'] 
    : $rolSesion;

// 3. Procesar formulario de finalización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IdIncidente'])) {
    $id = intval($_POST['IdIncidente']);
    $horaFin = $_POST['HoraFin'];
    $acciones = $_POST['AccionesGuardia'];
    
    // CORRECCIÓN IMPORTANTE:
    // Ahora guardamos también el ID del vigilante que tiene la sesión activa ($idUsuario)
    $sql = "UPDATE incidentes SET IdVigilante = ?, HoraFin = ?, AccionesGuardia = ?, Finalizado = 1 WHERE IdIncidente = ?";
    
    $stmt = $conn->prepare($sql);
    // Tipos: i (int vigilante), s (string hora), s (string acciones), i (int incidente)
    $stmt->bind_param("issi", $idUsuario, $horaFin, $acciones, $id);
    $stmt->execute();

    header("Location: FinalizarReporte.php");
    exit();
}

// 4. Revisar si se seleccionó un incidente
$incidenteSeleccionado = null;
if (isset($_GET['id'])) {
    $idIncidente = intval($_GET['id']);
    $sqlInc = "SELECT * FROM incidentes WHERE IdIncidente = ?";
    $stmtInc = $conn->prepare($sqlInc);
    $stmtInc->bind_param("i", $idIncidente);
    $stmtInc->execute();
    $resInc = $stmtInc->get_result();
    if ($resInc->num_rows > 0) {
        $incidenteSeleccionado = $resInc->fetch_assoc();
    } else {
        die("Incidente no encontrado.");
    }
}

// 5. Obtener todos los incidentes activos (Aprobado = 0)
// Nota: Aquí asumo que buscas los que el Admin NO ha aprobado (0) o los que NO están finalizados por el guardia.
// Mantengo tu consulta original.
$sqlActivos = "SELECT * FROM incidentes WHERE Aprobado = 0 ORDER BY Fecha DESC, HoraInicio DESC";
$resultActivos = $conn->query($sqlActivos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Reporte - UniAlert</title>
    
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
            <img src="../Multimedia/VigilanteAvatar.png" alt="Avatar" class="user-avatar">
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

        <a href="VigilanteIndex.php" class="btn-back-global" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Volver
        </a>

        <div class="page-header">
            <h1>Finalizar Incidente</h1>
            <p>Seleccione un incidente activo de la lista para cerrar el caso y agregar sus observaciones.</p>
        </div>

        <div class="report-list">
            <?php
            if ($resultActivos->num_rows > 0) {
                while($row = $resultActivos->fetch_assoc()) {
                    
                    // Lógica visual para icono y título
                    $esMedica = ($row['TipoEmergencia'] == 'medica');
                    $titulo = "Incidente #" . $row['IdIncidente'] . " - " . ucfirst($row['TipoEmergencia']);
                    $fechaTexto = $row['Fecha'] . " " . $row['HoraInicio'];
                    $colorIcono = $esMedica ? "#e02424" : "#1a56db";
                    
                    echo '<div class="report-item">
                            <div class="report-icon">';
                    
                    if ($esMedica) {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="'.$colorIcono.'" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/></svg>';
                    } else {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="'.$colorIcono.'" viewBox="0 0 16 16"><path d="M5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/></svg>';
                    }

                    echo '  </div>
                            <div class="report-content">
                                <h4>' . $titulo . '</h4>
                                <p>' . $fechaTexto . '</p>
                            </div>';

                    if($row['Finalizado'] == 0) {
                        echo '<a href="FinalizarReporte.php?id=' . $row['IdIncidente'] . '" class="review-button">Seleccionar</a>';
                    } else {
                        echo '<span class="status-badge" style="background:#def7ec; color:#03543f;">Finalizado</span>';
                    }
                    
                    echo '</div>'; // Fin report-item
                }
            } else {
                echo '<p style="text-align:center; color:#666; padding:20px;">No hay incidentes activos para finalizar.</p>';
            }
            ?>
        </div>

        <?php if($incidenteSeleccionado): ?>
            
            <div class="form-card" style="margin-top: 30px; border-top: 4px solid #2f3d78;">
                <h2 style="margin-bottom: 20px; color: #2f3d78;">Finalizando Incidente #<?php echo $incidenteSeleccionado['IdIncidente']; ?></h2>

                <form action="FinalizarReporte.php" method="POST">
                    <input type="hidden" name="IdIncidente" value="<?php echo $incidenteSeleccionado['IdIncidente']; ?>">

                    <div class="form-grid">
                        <div class="form-column">
                            <div class="form-group">
                                <label>Tipo de Emergencia</label>
                                <input type="text" class="form-input" value="<?php echo ucfirst($incidenteSeleccionado['TipoEmergencia']); ?>" readonly style="background-color: #e9ecef;">
                            </div>
                        </div>
                        
                        <div class="form-column">
                            <div class="form-group">
                                <label>Hora de Término</label>
                                <input type="time" name="HoraFin" class="form-input" 
                                       value="<?php echo $incidenteSeleccionado['HoraFin'] ? $incidenteSeleccionado['HoraFin'] : date('H:i'); ?>" 
                                       <?php echo ($incidenteSeleccionado['Finalizado'] == 1) ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Acciones del Guardia / Reporte de Cierre</label>
                        <textarea name="AccionesGuardia" class="form-input" rows="5" placeholder="Describa cómo se resolvió la situación..." 
                                  <?php echo ($incidenteSeleccionado['Finalizado'] == 1) ? 'readonly' : ''; ?> required><?php echo $incidenteSeleccionado['AccionesGuardia']; ?></textarea>
                    </div>

                    <div class="form-actions">
                        <?php if($incidenteSeleccionado['Finalizado'] == 0): ?>
                            <button type="submit" class="submit-button">Finalizar y Guardar</button>
                        <?php else: ?>
                            <p style="color: green; font-weight: bold; width:100%; text-align:center;">Este incidente ya fue cerrado.</p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <script>
                document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
            </script>
        <?php endif; ?>

    </main>

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
        const logoutBtn = document.getElementById('btn-logout-trigger');
        const logoutModal = document.getElementById('logout-modal');
        const logoutCancel = document.getElementById('logout-cancel');
        const logoutConfirm = document.getElementById('logout-confirm');

        if(logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                logoutModal.classList.add('modal-visible');
            });
            logoutCancel.addEventListener('click', function() {
                logoutModal.classList.remove('modal-visible');
            });
            logoutConfirm.addEventListener('click', function() {
                window.location.href = '../Backend/logout.php';
            });
            logoutModal.addEventListener('click', function(e) {
                if (e.target === logoutModal) logoutModal.classList.remove('modal-visible');
            });
        }
    </script>

</body>
</html>