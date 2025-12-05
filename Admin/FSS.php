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
    <title>Formulario de Servicios de Seguridad - UniAlert</title>

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
            <img src="../Multimedia/AdminAvatar.png" alt="Avatar" class="user-avatar">
            
            <div class="user-info">
                <span class="user-name"><?php echo $nombreMostrar; ?></span>
                
                <span class="user-details"><?php echo $detallesMostrar; ?></span>
            </div>
        </div>
    </header>

    <main class="admin-container">

        <h1 class="form-page-title form-page-title-security">Formulario para solicitar Servicios de Seguridad</h1>

        <div class="form-card">
            <form action="../Backend/guardar_incidente.php" method="POST">
                <input type="hidden" name="tipo_emergencia" value="seguridad">

                <div class="form-grid">
                    
                    <div class="form-column">
                        <div class="form-group">
                            <label for="emergencia-desc-seg">¿Cual es la emergencia?</label>
                            <textarea id="emergencia-desc-seg" name="descripcion" class="form-input" rows="4" placeholder="Describe brevemente que sucedió"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="sucediendo-ahora">¿Esta sucediendo ahora mismo?</label>
                            <select id="sucediendo-ahora" name="sucede_ahora" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="si">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="armas-involucradas">¿Hay Armas involucradas?</label>
                            <select id="armas-involucradas" name="armas" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="no">No</option>
                                <option value="si_arma_blanca">Sí, arma blanca</option>
                                <option value="si_arma_fuego">Sí, arma de fuego</option>
                                <option value="si_otra">Sí, otro tipo</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="hay-heridos">¿Hay heridos?</label>
                            <select id="hay-heridos" name="heridos" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="no">No</option>
                                <option value="si">Sí</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="cuantas-personas">¿Cuantas personas son?</label>
                            <select id="cuantas-personas" name="cantidad_personas" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="1">1 persona</option>
                                <option value="2">2 personas</option>
                                <option value="3-5">3-5 personas</option>
                                <option value="mas-de-5">Más de 5 personas</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="caracteristicas-personas">Describa a la(s) Persona(s) Involucradas</label>
                            <input type="text" id="caracteristicas-personas" name="desc_sospechosos" class="form-input" placeholder="Características físicas">
                        </div>
                    </div>
                    
                    <div class="form-column">
                        <div class="form-group">
                            <label for="huyeron-pie-vehiculo">¿Huyeron a pie o en vehiculo? (Opcional)</label>
                            <select id="huyeron-pie-vehiculo" name="intento_fuga" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="pie">A pie</option>
                                <option value="vehiculo">En vehículo</option>
                                <option value="no_huyeron">No huyeron</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="marca-modelo-vehiculo">¿Marca y Modelo? (Opcional)</label>
                            <textarea id="marca-modelo-vehiculo" name="desc_vehiculo" class="form-input" rows="4" placeholder="Describir el vehículo..."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="permanecer-anonimo">¿Permanecer Anonimo?</label>
                            <select id="permanecer-anonimo" name="anonimo" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="si">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="servicio-publico-privado-seg">¿Servicio Publico o Privado?</label>
                            <select id="servicio-publico-privado-seg" name="servicio_tipo" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="publico">Público (Policía, Guardia Nacional, etc.)</option>
                                <option value="privado">Privado (Seguridad privada)</option>
                                <option value="cualquiera">El más cercano / Cualquiera</option>
                            </select>
                        </div>
                    </div>
                    
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-button">Finalizar y Alertar al Vigilante</button>
                </div>
            </form>
            <button onclick="history.back()" class="btn-back-global">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Volver
            </button>
        </div>

    </main>

    <footer class="warning-footer">
        <strong class="warning-title">Advertencia</strong>
        <p>Esta es una herramienta para salvar vidas. El uso indebido, las falsas alarmas o las bromas desvian recursos de emergencias reales y pueden poner en riesgo a otros. <br>Todo abuso de este servicio será rastreado y sancionado con todo el rigor de la ley. Use esta función únicamente en una situación de emergencia real.</p>
    </footer>
    <div class="modal-overlay" id="status-modal">
        <div class="modal-box" style="text-align: center;">
            <div id="modal-icon-container" style="margin-bottom: 15px;"></div>
            <h2 class="modal-title" id="modal-title"></h2>
            <p class="modal-text" id="modal-message"></p>
            <div class="modal-actions" style="justify-content: center;">
                <button class="btn btn-primary" id="modal-btn-ok" style="background-color: #2b2b2b; color: #fff; padding: 10px 24px; border-radius: 8px; border:none; cursor: pointer;">Aceptar</button>
            </div>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        const modal = document.getElementById('status-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMsg = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon-container');
        const btnOk = document.getElementById('modal-btn-ok');

        let isSuccess = false;

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // DETIENE EL ENVÍO NORMAL

            const formData = new FormData(form);

            // Validar campos vacíos en el Frontend también (Doble seguridad)
            const descripcion = formData.get('descripcion');
            if (!descripcion || descripcion.trim() === "") {
                mostrarModal('error', 'Por favor, llena el campo de descripción de la emergencia.');
                return;
            }

            // Enviar datos por AJAX
            fetch('../Backend/guardar_incidente.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                mostrarModal(data.status, data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarModal('error', 'Ocurrió un error de conexión.');
            });
        });

        function mostrarModal(status, message) {
            isSuccess = (status === 'success');
            modalTitle.textContent = isSuccess ? '¡Enviado!' : 'Atención';
            modalMsg.textContent = message;

            if (isSuccess) {
                // Icono Check Verde
                modalIcon.innerHTML = `<svg width="60" height="60" fill="#28a745" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>`;
            } else {
                // Icono Exclamación Rojo
                modalIcon.innerHTML = `<svg width="60" height="60" fill="#dc3545" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>`;
            }
            
            modal.classList.add('modal-visible');
        }

        // Al dar clic en Aceptar
        btnOk.addEventListener('click', function() {
            modal.classList.remove('modal-visible');
            if (isSuccess) {
                // Si todo salió bien, regresa al usuario a la página anterior (Menú)
                window.history.back();
            }
        });
    </script>
</body>
</html>