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
    <title>Administrar Usuarios - UniAlert</title>

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
        
        <div class="page-header">
            <h1>Administrar Usuarios</h1>
            <p>Muestra todos los usuarios agregados manualmente por el administrador en el sistema.</p>
        </div>

        <div class="tab-bar">
            <div class="tabs">
                <a href="#" class="tab-link active">Usuarios</a>
            </div>
            <div class="tab-actions">
                <button class="btn btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3.172L3.58 13.889l-1.57 3.923 3.923-1.57L13.889 5.672l-2.096-2.096z"></path></svg>
                    Editar Usuarios
                </button>
                <a href="AñadirUsuarios.php" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"></path></svg>
                    Añadir Usuario
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="col-handle"></th>
                        <th>Nombre</th>
                        <th>¿Eliminar?</th>
                        <th>Estatus</th>
                        <th>Dirección</th>
                        <th>Nombre de Usuario</th>
                        <th class="col-menu"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consulta: Trae a todos MENOS a los que tienen IdRol = 1 (Admin)
                    // Asumiendo que 1 es Admin. Si es otro número, cámbialo.
                    $sqlUsuarios = "
                        SELECT U.IdUsuario, U.Nombre, U.ApPaterno, U.Activo, U.Edificio, U.Departamento, C.NickName
                        FROM Usuarios U
                        JOIN Credenciales C ON U.IdUsuario = C.IdUsuario
                        WHERE U.IdRol != 1
                    ";
                    $resultUsuarios = $conn->query($sqlUsuarios);

                    if ($resultUsuarios->num_rows > 0) {
                        while($row = $resultUsuarios->fetch_assoc()) {
                            // Determinar estado y clase CSS
                            $estadoTexto = $row['Activo'] ? 'Activo' : 'Inactivo';
                            $estadoClase = $row['Activo'] ? 'status-active' : 'status-inactive';
                            
                            // Construir ubicación
                            $ubicacion = ($row['Edificio'] && $row['Departamento']) 
                                ? $row['Edificio'] . ', ' . $row['Departamento'] 
                                : 'Sin asignar';

                            echo "<tr>";
                            echo "<td class='col-handle'><button class='icon-button'>...</button></td>"; // Icono visual
                            
                            // Nombre Completo
                            echo "<td>" . $row['Nombre'] . " " . $row['ApPaterno'] . "</td>";
                            
                            // Botón Eliminar (Guardamos el ID en un atributo data-id para usarlo con JS)
                            echo "<td><button class='btn-delete js-delete-trigger' data-id='" . $row['IdUsuario'] . "'>Eliminar</button></td>";
                            
                            // Estatus
                            echo "<td><span class='status-dot " . $estadoClase . "'></span> " . $estadoTexto . "</td>";
                            
                            // Ubicación
                            echo "<td>" . $ubicacion . "</td>";
                            
                            // Usuario (NickName) en lugar de teléfono que no tenemos
                            echo "<td>" . $row['NickName'] . "</td>";
                            
                            // Botón Editar (Lleva a la página de edición con el ID)
                            echo "<td class='col-menu'>
                                    <a href='EditarUsuario.php?id=" . $row['IdUsuario'] . "' class='icon-button'>
                                        <svg width='16' height='16' fill='currentColor' viewBox='0 0 16 16'><path d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3.172L3.58 13.889l-1.57 3.923 3.923-1.57L13.889 5.672l-2.096-2.096z'/></svg>
                                    </a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>No hay usuarios registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="footer-pagination">
                <span>Filas por página:</span>
                <select class="rows-select">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="page-info">Página 1 de 1</span>
                <div class="page-nav">
                    <button class="page-nav-btn" aria-label="Primera página">&laquo;</button>
                    <button class="page-nav-btn" aria-label="Página anterior">&lsaquo;</button>
                    <button class="page-nav-btn" aria-label="Página siguiente">&rsaquo;</button>
                    <button class="page-nav-btn" aria-label="Última página">&raquo;</button>
                </div>
            </div>
        </div>
        <button onclick="window.location.href='Admin.php'" class="btn-back-global">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Volver
        </button>
    </main>

    <div class="modal-overlay" id="delete-modal">
        <div class="modal-box">
            <h2 class="modal-title">¿Estás completamente seguro de eliminar el registro?</h2>
            <p class="modal-text">Esta acción no puede deshacerse. Se borrará completamente el usuario de la base de datos.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" id="modal-cancel">Cancelar</button>
                <button class="btn btn-primary" id="modal-confirm">Continuar</button>
            </div>
        </div>
    </div>
    <script>
        // 1. Variables Globales
        let usuarioIdParaBorrar = null; // Aquí guardaremos el ID temporalmente

        // 2. Seleccionar elementos del DOM
        const modal = document.getElementById('delete-modal');
        const cancelButton = document.getElementById('modal-cancel');
        const confirmButton = document.getElementById('modal-confirm');
        
        // Seleccionamos TODOS los botones que tengan la clase 'js-delete-trigger'
        // (Estos botones se crean dinámicamente con PHP en la tabla)
        const deleteButtons = document.querySelectorAll('.js-delete-trigger');

        // 3. Funciones para Mostrar/Ocultar Modal
        function showModal() {
            modal.classList.add('modal-visible');
        }

        function hideModal() {
            modal.classList.remove('modal-visible');
            usuarioIdParaBorrar = null; // Limpiamos el ID por seguridad
        }

        // 4. Asignar eventos a los botones de "Eliminar" de la tabla
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                
                // Obtener el ID del atributo data-id (que pusimos con PHP)
                usuarioIdParaBorrar = this.getAttribute('data-id');
                
                // Mostrar la advertencia
                showModal();
            });
        });

        // 5. Botón "Cancelar" del Modal
        cancelButton.addEventListener('click', hideModal);

        // 6. Botón "Continuar" del Modal (La acción real)
        confirmButton.addEventListener('click', () => {
            if (usuarioIdParaBorrar) {
                // Redirigir al archivo PHP que elimina, pasando el ID por URL
                window.location.href = '../Backend/eliminar_usuario.php?id=' + usuarioIdParaBorrar;
            }
        });

        // 7. Cerrar si hacen clic fuera de la cajita blanca
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                hideModal();
            }
        });
    </script>
</body>
</html>