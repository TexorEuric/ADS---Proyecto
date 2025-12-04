<?php
session_start();
include '../Backend/conexion.php';

// 1. SEGURIDAD
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'vigilante') {
    header("Location: ../Login/Login.html");
    exit();
}

// 2. DATOS DEL ADMIN (Para Navbar)
$idAdmin = $_SESSION['IdUsuario'];
$sqlAdmin = "SELECT Nombre, ApPaterno, Edificio, Departamento FROM Usuarios WHERE IdUsuario = ?";
$stmtA = $conn->prepare($sqlAdmin);
$stmtA->bind_param("i", $idAdmin);
$stmtA->execute();
$resA = $stmtA->get_result();
$datosAdmin = $resA->fetch_assoc();
$nombreNavbar = $datosAdmin['Nombre'] . " " . $datosAdmin['ApPaterno'];
$detallesNavbar = (!empty($datosAdmin['Edificio'])) ? "Edif. " . $datosAdmin['Edificio'] : "Administrador";

// 3. OBTENER EL REPORTE ESPECÍFICO
if (!isset($_GET['id'])) {
    header("Location: ReportesSA.php");
    exit();
}
$idIncidente = $_GET['id'];

// Hacemos JOIN para traer los nombres del Vecino y del Vigilante en lugar de solo sus IDs
$sql = "SELECT I.*, 
               U.Nombre as NomVecino, U.ApPaterno as ApeVecino, U.Edificio as EdifVecino, U.Departamento as DeptoVecino,
               V.Nombre as NomVigilante, V.ApPaterno as ApeVigilante
        FROM Incidentes I
        JOIN Usuarios U ON I.IdUsuario = U.IdUsuario
        LEFT JOIN Usuarios V ON I.IdVigilante = V.IdUsuario
        WHERE I.IdIncidente = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idIncidente);
$stmt->execute();
$resultado = $stmt->get_result();
$reporte = $resultado->fetch_assoc();

if (!$reporte) {
    echo "Reporte no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Reporte - UniAlert</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos específicos para esta vista de detalles */
        .detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 30px;
        }
        .detail-card {
            background: white;
            border: 1px solid #e9e9e9;
            border-radius: 12px;
            padding: 24px;
        }
        .detail-section {
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-section:last-child { border-bottom: none; }
        .detail-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #888;
            font-weight: 600;
            margin-bottom: 4px;
            display: block;
        }
        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
            line-height: 1.5;
        }
        .info-row {
            display: flex;
            gap: 40px;
            margin-bottom: 16px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: #fff3cd;
            color: #856404;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 20px;
        }
        .btn-reject {
            background-color: #fff;
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-approve {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        @media (max-width: 768px) { .detail-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="body-admin">

    <header class="navbar">
        <div class="navbar-left">
            <img src="../Multimedia/Unialert Logo.png" alt="Logo" class="navbar-logo">
            <span class="navbar-logo-text">UNIALERT</span>
        </div>
        <div class="navbar-right">
            <img src="../Multimedia/User Avatar.png" alt="Avatar" class="user-avatar">
            <div class="user-info">
                <span class="user-name"><?php echo $nombreNavbar; ?></span>
                <span class="user-details"><?php echo $detallesNavbar; ?></span>
            </div>
        </div>
    </header>

    <main class="admin-container">
        
        <button onclick="history.back()" class="btn-back-global">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
            Volver
        </button>

        <div class="page-header" style="text-align: left; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>Detalle del Reporte #<?php echo $reporte['IdIncidente']; ?></h1>
                <p>Revisión final para aprobación.</p>
            </div>
            <span class="status-badge">Pendiente de Aprobación</span>
        </div>

        <div class="detail-grid">
            
            <div class="detail-card">
                <h3 style="margin-bottom: 20px;">Información del Incidente</h3>
                
                <div class="detail-section">
                    <span class="detail-label">Descripción General</span>
                    <p class="detail-value" style="white-space: pre-line;"><?php echo $reporte['Descripcion']; ?></p>
                </div>

                <div class="info-row">
                    <div>
                        <span class="detail-label">Tipo de Emergencia</span>
                        <span class="detail-value" style="text-transform: capitalize;"><?php echo $reporte['TipoEmergencia']; ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Fecha y Hora Inicio</span>
                        <span class="detail-value"><?php echo $reporte['Fecha'] . " - " . $reporte['HoraInicio']; ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Servicio Solicitado</span>
                        <span class="detail-value"><?php echo $reporte['PublicoOPrivado']; ?></span>
                    </div>
                </div>

                <?php if($reporte['TipoEmergencia'] == 'seguridad'): ?>
                <div class="info-row">
                    <div>
                        <span class="detail-label">Armas</span>
                        <span class="detail-value"><?php echo ($reporte['Armas'] ? 'Sí' : 'No'); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if($reporte['TipoEmergencia'] == 'medica'): ?>
                <div class="info-row">
                    <div>
                        <span class="detail-label">Sangre</span>
                        <span class="detail-value"><?php echo ($reporte['Sangre'] ? 'Sí' : 'No'); ?></span>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <div class="detail-card">
                <h3 style="margin-bottom: 20px;">Involucrados</h3>
                
                <div class="detail-section">
                    <span class="detail-label">Reportado por (Vecino)</span>
                    <p class="detail-value"><?php echo $reporte['NomVecino'] . " " . $reporte['ApeVecino']; ?></p>
                    <p style="font-size: 13px; color: #666;">
                        <?php echo "Edif. " . $reporte['EdifVecino'] . " - Depto. " . $reporte['DeptoVecino']; ?>
                    </p>
                </div>

                <div class="detail-section">
                    <span class="detail-label">Atendido por (Vigilante)</span>
                    <?php if($reporte['NomVigilante']): ?>
                        <p class="detail-value"><?php echo $reporte['NomVigilante'] . " " . $reporte['ApeVigilante']; ?></p>
                    <?php else: ?>
                        <p class="detail-value" style="color: #999;">Aún no asignado</p>
                    <?php endif; ?>
                </div>

                <div class="detail-section">
                    <span class="detail-label">Reporte de Cierre (Vigilante)</span>
                    <p class="detail-value"><?php echo $reporte['AccionesGuardia'] ? $reporte['AccionesGuardia'] : 'Sin observaciones.'; ?></p>
                </div>
            </div>

        </div>
        <?php if ($reporte['Aprobado'] == 0): ?>
        <div class="action-buttons">
            <button id="btn-reject-trigger" class="btn-reject">Rechazar Reporte</button>

            <button id="btn-approve-trigger" class="btn-approve">Finalizar y Aprobar</button>
        </div>
        <?php endif; ?>

    </main>
<div class="modal-overlay" id="status-modal">
        <div class="modal-box" style="text-align: center;">
            <div id="modal-icon-container" style="margin-bottom: 15px;">
                </div>
            <h2 class="modal-title" id="modal-title">Título</h2>
            <p class="modal-text" id="modal-message">Mensaje de respuesta.</p>
            <div class="modal-actions" style="justify-content: center;">
                <button class="btn btn-primary" id="modal-btn-ok">Aceptar</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirm-modal">
        <div class="modal-box">
            <h2 class="modal-title">¿Rechazar reporte?</h2>
            <p class="modal-text">Esta acción marcará el reporte como rechazado en el historial.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" id="confirm-cancel">Cancelar</button>
                <button class="btn btn-delete" id="confirm-yes">Sí, Rechazar</button>
            </div>
        </div>
    </div>
<script>
        const idIncidente = <?php echo $idIncidente; ?>; // ID traído desde PHP

        // Elementos del DOM
        const statusModal = document.getElementById('status-modal');
        const confirmModal = document.getElementById('confirm-modal');
        
        const modalTitle = document.getElementById('modal-title');
        const modalMsg = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon-container');
        
        // --- 1. APROBAR (Directo) ---
        document.getElementById('btn-approve-trigger').addEventListener('click', function() {
            enviarAccion('aprobar');
        });

        // --- 2. RECHAZAR (Con Confirmación) ---
        document.getElementById('btn-reject-trigger').addEventListener('click', function() {
            confirmModal.classList.add('modal-visible');
        });

        document.getElementById('confirm-cancel').addEventListener('click', function() {
            confirmModal.classList.remove('modal-visible');
        });

        document.getElementById('confirm-yes').addEventListener('click', function() {
            confirmModal.classList.remove('modal-visible');
            enviarAccion('rechazar');
        });

        // --- 3. CERRAR MODAL DE ÉXITO ---
        document.getElementById('modal-btn-ok').addEventListener('click', function() {
            window.location.href = 'ReportesSA.php'; // Volver a la lista
        });

        // --- FUNCIÓN AJAX PRINCIPAL ---
        function enviarAccion(accion) {
            const formData = new FormData();
            formData.append('id', idIncidente);
            formData.append('accion', accion);

            fetch('../Backend/procesar_reporte.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarResultado(data.status, data.message);
            })
            .catch(error => {
                console.error(error);
                mostrarResultado('error', 'Error de conexión');
            });
        }

        function mostrarResultado(status, message) {
            modalTitle.textContent = status === 'success' ? '¡Listo!' : 'Error';
            modalMsg.textContent = message;
            
            if (status === 'success') {
                modalIcon.innerHTML = `<svg width="50" height="50" fill="#28a745" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>`;
            } else {
                modalIcon.innerHTML = `<svg width="50" height="50" fill="#dc3545" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/></svg>`;
            }
            statusModal.classList.add('modal-visible');
        }
    </script>
</body>
</html>