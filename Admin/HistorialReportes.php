<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
    header("Location: ../Login/Login.html");
    exit();
}

// 2. DATOS DEL ADMIN
$idUsuario = $_SESSION['IdUsuario'];
$rolSesion = $_SESSION['Rol']; 

$sql = "SELECT Nombre, ApPaterno, Edificio, Departamento FROM Usuarios WHERE IdUsuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos = $resultado->fetch_assoc();

$nombreMostrar = $datos['Nombre'] . " " . $datos['ApPaterno'];
$detallesMostrar = (!empty($datos['Edificio']) && !empty($datos['Departamento'])) 
    ? "Edif. " . $datos['Edificio'] . " - Depto. " . $datos['Departamento'] 
    : $rolSesion;

// --- 3. LÓGICA DE FILTRADO ---
// Mostrar solo Aprobados (1) o Rechazados (2). Ignorar pendientes (0).
$whereClause = "WHERE Aprobado != 0"; 

// Filtro por Tipo
$filtroTipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if (!empty($filtroTipo)) {
    $whereClause .= " AND TipoEmergencia = '$filtroTipo'";
}

// Filtro por Mes
$filtroMes = isset($_GET['mes']) ? $_GET['mes'] : '';
if (!empty($filtroMes)) {
    $whereClause .= " AND Fecha LIKE '$filtroMes%'";
}

// Consulta Final a la BD
$sqlReportes = "SELECT * FROM Incidentes $whereClause ORDER BY Fecha DESC, HoraInicio DESC";
$resultReportes = $conn->query($sqlReportes);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Reportes - UniAlert</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/style.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    
    <style>
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 10px;
        }
        .badge-approved { background-color: #def7ec; color: #03543f; border: 1px solid #bcf0da; }
        .badge-rejected { background-color: #fde8e8; color: #9b1c1c; border: 1px solid #fbd5d5; }
    </style>
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
            <a href="../Backend/logout.php" class="btn-logout" title="Cerrar Sesión" style="margin-left:20px; color:#ff6b6b; text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </a>
        </div>
    </header>

    <main class="admin-container">
        
        <button onclick="window.location.href='Admin.php'" class="btn-back-global">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Volver
        </button>

        <div class="page-header">
            <h1>Historial de Reportes</h1>
            <p>Muestra el contenido de reportes en su totalidad (Aprobados y Rechazados).</p>
        </div>

        <form method="GET" class="filter-bar-simple" style="gap: 15px;">
            <div class="filter-bar-title">Filtrar historial</div>
            
            <div class="filter-bar-actions" style="width: auto;">
                <div class="date-picker-wrapper" style="width: 180px;">
                    <select name="tipo" class="form-input" style="border:none; background:transparent; padding:0; height:100%; cursor:pointer;" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <option value="medica" <?php if($filtroTipo == 'medica') echo 'selected'; ?>>Médica</option>
                        <option value="seguridad" <?php if($filtroTipo == 'seguridad') echo 'selected'; ?>>Seguridad</option>
                    </select>
                </div>

                <div class="date-picker-wrapper">
                    <div class="date-picker-wrapper">
                        <input type="text" id="filtroFecha" name="mes" class="form-input calendar-input" 
                            placeholder="Seleccionar Mes" 
                            value="<?php echo $filtroMes; ?>" 
                            readonly 
                            style="cursor: pointer; background-color: transparent; border: none;">
                    </div>
                </div>

                <?php if(!empty($filtroTipo) || !empty($filtroMes)): ?>
                    <a href="HistorialReportes.php" style="color: #666; text-decoration: underline; font-size: 14px;">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="report-list">
            <?php
            if ($resultReportes->num_rows > 0) {
                while($row = $resultReportes->fetch_assoc()) {
                    
                    // Definir Icono y Título
                    $esMedica = ($row['TipoEmergencia'] == 'medica');
                    $titulo = $esMedica ? "Alerta Médica" : "Alerta de Seguridad";
                    
                    // Formato de Fecha
                    $fechaOriginal = strtotime($row['Fecha'] . ' ' . $row['HoraInicio']);
                    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                    $dia = date('d', $fechaOriginal);
                    $mesIndex = date('n', $fechaOriginal) - 1;
                    $anio = date('Y', $fechaOriginal);
                    $hora = date('H:i', $fechaOriginal);
                    $fechaTexto = "$dia de " . $meses[$mesIndex] . " del $anio - $hora";

                    // Determinar Estado (Badge)
                    $estadoBadge = "";
                    if ($row['Aprobado'] == 1) {
                        $estadoBadge = '<span class="badge badge-approved">Aprobado</span>';
                    } elseif ($row['Aprobado'] == 2) {
                        $estadoBadge = '<span class="badge badge-rejected">Rechazado</span>';
                    }

                    echo '
                    <div class="report-item">
                        <div class="report-icon">';
                            
                    if ($esMedica) {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e02424" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/></svg>';
                    } else {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#1a56db" viewBox="0 0 16 16"><path d="M5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/></svg>';
                    }

                    echo '</div>
                        <div class="report-content">
                            <h4>' . $titulo . $estadoBadge . '</h4>
                            <p>' . $fechaTexto . '</p>
                        </div>
                        <a href="DetalleAprobacion.php?id=' . $row['IdIncidente'] . '" class="review-button">Ver Detalle</a>
                    </div>';
                }
            } else {
                echo '<p style="text-align:center; color:#666; padding:20px;">No hay reportes en el historial.</p>';
            }
            ?>
        </div>

    </main>
    <script>
    // Configuración del Calendario
    flatpickr("#filtroFecha", {
        locale: "es", // Idioma español
        plugins: [
            new monthSelectPlugin({
                shorthand: true, // Muestra "Ene", "Feb"
                dateFormat: "Y-m", // Formato para enviar al PHP (Año-Mes)
                altFormat: "F Y", // Formato visual para el usuario (Octubre 2025)
                theme: "material_blue" // Tema visual
            })
        ],
        onChange: function(selectedDates, dateStr, instance) {
            // Cuando el usuario elige una fecha, enviamos el formulario automáticamente
            // Buscamos el formulario padre y lo enviamos
            instance.element.closest('form').submit();
        }
    });
</script>
</body>
</html>