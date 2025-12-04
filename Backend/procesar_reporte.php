<?php
// RESPUESTA JSON
header('Content-Type: application/json');
session_start();
include 'conexion.php';

// Seguridad
if (!isset($_SESSION['IdUsuario']) || strtolower($_SESSION['Rol']) != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Acceso denegado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $accion = $_POST['accion']; // 'aprobar' o 'rechazar'
    
    // Validar ID
    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
        exit();
    }

    $nuevoEstado = 0;
    $mensajeExito = "";

    if ($accion === 'aprobar') {
        $nuevoEstado = 1;
        $mensajeExito = "El reporte ha sido APROBADO correctamente.";
    } elseif ($accion === 'rechazar') {
        $nuevoEstado = 2;
        $mensajeExito = "El reporte ha sido RECHAZADO.";
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        exit();
    }

    // Ejecutar actualización
    $sql = "UPDATE Incidentes SET Aprobado = ? WHERE IdIncidente = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $nuevoEstado, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => $mensajeExito]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $conn->error]);
    }
}
?>