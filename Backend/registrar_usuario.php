<?php
// Indicamos respuesta JSON
header('Content-Type: application/json');
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- 1. RECIBIR Y LIMPIAR VARIABLES ---
    $nombre = trim($_POST['nombre'] ?? '');
    $apPaterno = trim($_POST['ap_paterno'] ?? '');
    $apMaterno = trim($_POST['ap_materno'] ?? ''); 
    $correo = trim($_POST['correo'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    $idRol = isset($_POST['id_rol']) ? $_POST['id_rol'] : 3; 
    $edificio = !empty($_POST['edificio']) ? $_POST['edificio'] : NULL;
    $depto = !empty($_POST['departamento']) ? $_POST['departamento'] : NULL;

    // --- 2. VALIDACIÓN DE CAMPOS VACÍOS (Server Side) ---
    if (empty($nombre) || empty($apPaterno) || empty($correo) || empty($nickname) || empty($pass)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, llena todos los campos obligatorios.']);
        exit();
    }

    // --- 3. INICIAR TRANSACCIÓN ---
    $conn->begin_transaction();

    try {
        // A. Insertar en USUARIOS
        $stmt1 = $conn->prepare("INSERT INTO Usuarios (IdRol, Nombre, ApPaterno, ApMaterno, Correo, Edificio, Departamento) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("issssss", $idRol, $nombre, $apPaterno, $apMaterno, $correo, $edificio, $depto);
        
        if (!$stmt1->execute()) {
            throw new Exception("Error al insertar usuario: " . $stmt1->error);
        }

        $idUsuarioNuevo = $conn->insert_id;

        // B. Insertar en CREDENCIALES
        $stmt2 = $conn->prepare("INSERT INTO Credenciales (IdUsuario, NickName, Contrasena) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $idUsuarioNuevo, $nickname, $pass);

        if (!$stmt2->execute()) {
            // Error común: Nickname duplicado
            if ($conn->errno == 1062) { 
                throw new Exception("El nombre de usuario '$nickname' ya existe.");
            }
            throw new Exception("Error al crear credenciales: " . $stmt2->error);
        }

        // --- 4. ÉXITO ---
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado exitosamente.']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    if(isset($stmt1)) $stmt1->close();
    if(isset($stmt2)) $stmt2->close();
    $conn->close();
}
?>