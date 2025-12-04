<?php
session_start();
include 'conexion.php';

// Seguridad: Solo admin puede borrar
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
    die("Acceso denegado");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // No te puedes borrar a ti mismo
    if ($id == $_SESSION['IdUsuario']) {
        die("No puedes borrarte a ti mismo.");
    }

    // Borrar usuario (CASCADE borrará credenciales también)
    $sql = "DELETE FROM Usuarios WHERE IdUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../Admin/AdminUsuarios.php?msg=eliminado");
    } else {
        echo "Error al eliminar: " . $conn->error;
    }
}
?>