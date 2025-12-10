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
    <title>Añadir Usuario - UniAlert</title>

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

        <h1 class="form-page-title form-page-title-user">Añadir Nuevo Usuario</h1>

        <div class="form-card">
            <form action="../Backend/registrar_usuario.php" method="POST">
                <div class="form-grid">
                    
                    <div class="form-column">
                        <div class="form-group">
                            <label for="nombre">Nombre(s)</label>
                            <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Ej. Juan" required>
                        </div>

                        <div class="form-group">
                            <label for="ap-paterno">Apellido Paterno</label>
                            <input type="text" id="ap-paterno" name="ap_paterno" class="form-input" placeholder="Ej. Pérez" required>
                        </div>

                        <div class="form-group">
                            <label for="ap-materno">Apellido Materno</label>
                            <input type="text" id="ap-materno" name="ap_materno" class="form-input" placeholder="Ej. López">
                        </div>
                        
                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" class="form-input" placeholder="usuario@ejemplo.com" required>
                        </div>
                    </div>
                    
                    <div class="form-column">
                        
                        <div class="form-group">
                            <label for="nickname">Nombre de Usuario (NickName)</label>
                            <input type="text" id="nickname" name="nickname" class="form-input" placeholder="Ej. jperez2025" required>
                        </div>

                        <div class="form-group">
                            <label for="edificio">Edificio</label>
                            <select id="edificio" name="edificio" class="form-input">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="A1">Edificio A1</option>
                                <option value="A2">Edificio A2</option>
                                <option value="B1">Edificio B1</option>
                                <option value="B2">Edificio B2</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <input type="text" id="departamento" name="departamento" class="form-input" placeholder="Ej. 302">
                        </div>

                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="rol">Tipo de Usuario</label>
                            <select id="rol" name="id_rol" class="form-input" required>
                                <option value="3" selected>Vecino</option>
                                <option value="2">Vigilante</option>
                                <option value="1">Administrador</option>
                            </select>
                        </div>
                    </div>
                    
                </div>
                
                <div class="form-actions">
                    <a href="AdminUsuarios.php" class="btn btn-secondary" style="margin-right: 10px; text-decoration: none;">Cancelar</a>
                    <button type="submit" class="submit-button">Guardar Usuario</button>
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

            // 1. Validación rápida en JS (Cliente)
            const nombre = document.getElementById('nombre').value.trim();
            const usuario = document.getElementById('nickname').value.trim();
            const pass = document.getElementById('password').value.trim();

            if (!nombre || !usuario || !pass) {
                mostrarModal('error', 'Por favor, no dejes campos vacíos.');
                return;
            }

            // 2. Enviar datos por AJAX
            const formData = new FormData(form);

            fetch('../Backend/registrar_usuario.php', {
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
            modalTitle.textContent = isSuccess ? '¡Registrado!' : 'Error';
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
                // Si se registró bien, regresamos a la lista de usuarios
                window.location.href = 'AdminUsuarios.php';
            }
        });
    </script>
</body>
</html>