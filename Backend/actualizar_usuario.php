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
    $idUsuario = $_POST['id_usuario']; // Viene del input hidden
    
    $nombre = $_POST['nombre'];
    $apPaterno = $_POST['ap_paterno'];
    $apMaterno = $_POST['ap_materno'];
    $correo = $_POST['correo'];
    
    $nickname = $_POST['nickname'];
    
    // Recibimos la contraseña plana y la encriptamos
    $passwordInput = $_POST['password'];
    // --- CAMBIO CLAVE: Cifrado al actualizar ---
    $passwordHash = password_hash($passwordInput, PASSWORD_DEFAULT);
    
    // Manejo de nulos para edificio/depto
    $edificio = !empty($_POST['edificio']) ? $_POST['edificio'] : NULL;
    $depto = !empty($_POST['departamento']) ? $_POST['departamento'] : NULL;

    // 2. Iniciar Transacción
    $conn->begin_transaction();

    try {
        // --- A. ACTUALIZAR TABLA USUARIOS ---
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

        // --- B. ACTUALIZAR TABLA CREDENCIALES (Con contraseña cifrada) ---
        $sql2 = "UPDATE Credenciales SET 
                    NickName = ?, 
                    Contrasena = ? 
                 WHERE IdUsuario = ?";
                 
        $stmt2 = $conn->prepare($sql2);
        // Usamos $passwordHash aquí
        $stmt2->bind_param("ssi", $nickname, $passwordHash, $idUsuario);

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
        echo "<script>
                alert('Error al actualizar: " . addslashes($e->getMessage()) . "'); 
                window.history.back();
              </script>";
    }

    if(isset($stmt1)) $stmt1->close();
    if(isset($stmt2)) $stmt2->close();
    $conn->close();
}
?>