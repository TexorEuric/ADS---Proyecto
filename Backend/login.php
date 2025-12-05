<?php
// Indicamos que la respuesta será en formato JSON (para que JS la entienda)
header('Content-Type: application/json');

session_start();
include 'conexion.php';

$response = array(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $inputUser = $_POST['email']; // Puede ser NickName o Correo
    $inputPass = $_POST['password'];

    // Buscamos el Hash de la contraseña y el Rol en las tablas unidas
    $sql = "SELECT 
                U.IdUsuario, U.Nombre, U.ApPaterno, U.IdRol, 
                C.Contrasena, 
                R.NombreRol 
            FROM Usuarios U
            JOIN Credenciales C ON U.IdUsuario = C.IdUsuario
            JOIN Roles R ON U.IdRol = R.IdRol
            WHERE C.NickName = ? OR U.Correo = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $inputUser, $inputUser);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // --- CAMBIO CLAVE: Usamos password_verify para el cifrado ---
        if (password_verify($inputPass, $row['Contrasena'])) {
            
            // Guardar sesión
            $_SESSION['IdUsuario'] = $row['IdUsuario'];
            $_SESSION['Nombre'] = $row['Nombre'] . " " . $row['ApPaterno'];
            $_SESSION['Rol'] = $row['NombreRol'];

            // Determinar a dónde ir (Asegúrate de que los archivos existan con .php)
            $rol = strtolower($row['NombreRol']); 
            $redirectUrl = "";

            if ($rol == 'admin') {
                $redirectUrl = "../Admin/Admin.php";
            } elseif ($rol == 'vigilante') {
                $redirectUrl = "../Vigilante/VigilanteIndex.php";
            } elseif ($rol == 'vecino') {
                $redirectUrl = "../Usuario/UsuarioIndex.php";
            }

            // RESPUESTA DE ÉXITO
            echo json_encode([
                "status" => "success", 
                "redirect" => $redirectUrl
            ]);

        } else {
            // RESPUESTA DE ERROR (Contraseña mal)
            echo json_encode([
                "status" => "error", 
                "message" => "La contraseña es incorrecta."
            ]);
        }
    } else {
        // RESPUESTA DE ERROR (Usuario no existe)
        echo json_encode([
            "status" => "error", 
            "message" => "El usuario o correo no existe."
        ]);
    }
    exit();
}
?>