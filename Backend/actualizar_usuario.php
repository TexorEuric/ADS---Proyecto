<?php
session_start();
include 'conexion.php';

// Verificar si es Admin
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
    header("Location: ../Login/Login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recibir ID y Datos
    $idUsuario = $_POST['id_usuario']; // Este viene del input hidden
    
    $nombre = $_POST['nombre'];
    $apPaterno = $_POST['ap_paterno']; // Asegúrate de tener este input en tu HTML de editar
    $apMaterno = $_POST['ap_materno']; // Asegúrate de tener este input
    $correo = $_POST['correo'];
    
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    
    // Manejo de nulos para edificio/depto
    $edificio = !empty($_POST['edificio']) ? $_POST['edificio'] : NULL;
    $depto = !empty($_POST['departamento']) ? $_POST['departamento'] : NULL;

    // 2. Iniciar Transacción (Para actualizar ambas tablas o ninguna)
    $conn->begin_transaction();

    try {
        // --- ACTUALIZAR TABLA USUARIOS ---
        $sql1 = "UPDATE Usuarios SET 
                    Nombre = ?, 
                    ApPaterno = ?, 
                    ApMaterno = ?, 
                    Correo = ?, 
                    Edificio = ?, 
                    Departamento = ? 
                 WHERE IdUsuario = ?";
                 
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssssssi", $nombre, $apPaterno, $apMaterno, $correo, $edificio, $depto, $idUsuario);
        
        if (!$stmt1->execute()) {
            throw new Exception("Error al actualizar perfil: " . $stmt1->error);
        }

        // --- ACTUALIZAR TABLA CREDENCIALES ---
        // (Solo actualizamos contraseña y nickname)
        $sql2 = "UPDATE Credenciales SET 
                    NickName = ?, 
                    Contrasena = ? 
                 WHERE IdUsuario = ?";
                 
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssi", $nickname, $password, $idUsuario);

        if (!$stmt2->execute()) {
            throw new Exception("Error al actualizar credenciales: " . $stmt2->error);
        }

        // Confirmar cambios
        $conn->commit();
        
        echo "<script>
                alert('Usuario actualizado correctamente.'); 
                window.location.href='../Admin/AdminUsuarios.php';
              </script>";

    } catch (Exception $e) {
        $conn->rollback(); // Deshacer cambios si hubo error
        echo "Error: " . $e->getMessage();
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>