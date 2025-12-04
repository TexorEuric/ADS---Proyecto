<?php
// Indicamos que vamos a responder en formato JSON
header('Content-Type: application/json');
session_start();
include 'conexion.php';

// Validar Sesión
if (!isset($_SESSION['IdUsuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión expirada. Por favor inicia sesión nuevamente.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $idUsuario = $_SESSION['IdUsuario'];
    $tipoEmergencia = $_POST['tipo_emergencia']; 
    
    // --- VALIDACIÓN DE CAMPOS VACÍOS ---
    // Si la descripción está vacía, detenemos todo.
    if (empty(trim($_POST['descripcion']))) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, describe brevemente la emergencia.']);
        exit();
    }

    // Datos automáticos del usuario
    $sqlUser = "SELECT Edificio, Departamento FROM Usuarios WHERE IdUsuario = ?";
    $stmtU = $conn->prepare($sqlUser);
    $stmtU->bind_param("i", $idUsuario);
    $stmtU->execute();
    $userData = $stmtU->get_result()->fetch_assoc();
    $edificio = $userData['Edificio'];
    $depto = $userData['Departamento'];
    $stmtU->close();

    // Recibir datos (usamos operador ternario para limpiar código)
    $publicoPrivado = $_POST['servicio_tipo'] ?? 'Indefinido';
    $sangre = (isset($_POST['hay_sangrado']) && $_POST['hay_sangrado'] != 'no') ? 1 : 0;
    $armas = (isset($_POST['armas']) && $_POST['armas'] != 'no') ? 1 : 0;
    
    // Construir Descripción Detallada
    $descripcionFinal = "";
    $descUsuario = $_POST['descripcion'] ?? "";
    $descripcionFinal .= "Detalle: " . $descUsuario . ". \n";

    if ($tipoEmergencia == 'medica') {
        $pacNombre = $_POST['paciente_nombre'] ?? '-';
        if (empty($pacNombre)) { 
             echo json_encode(['status' => 'error', 'message' => 'El nombre del paciente es obligatorio.']);
             exit();
        }
        $pacConsciente = $_POST['paciente_consciente'] ?? '?';
        $pacRespirando = $_POST['paciente_respirando'] ?? '?';
        $pacEdad = $_POST['paciente_edad'] ?? '?';
        $pacSexo = $_POST['paciente_sexo'] ?? '?';
        $lugarSangrado = $_POST['lugar_sangrado'] ?? '';

        $descripcionFinal .= "[MÉDICA] Paciente: $pacNombre. Edad: $pacEdad. Sexo: $pacSexo. Consciente: $pacConsciente. Respirando: $pacRespirando.";
        if ($sangre) $descripcionFinal .= " Sangrado en: $lugarSangrado.";

    } else {
        $sucedeAhora = $_POST['sucede_ahora'] ?? '?';
        $heridos = $_POST['heridos'] ?? 'no';
        $cantPersonas = $_POST['cantidad_personas'] ?? '?';
        $sospechosos = $_POST['desc_sospechosos'] ?? '';
        $fuga = $_POST['intento_fuga'] ?? '';
        $vehiculo = $_POST['desc_vehiculo'] ?? '';
        $anonimo = $_POST['anonimo'] ?? 'no';

        $descripcionFinal .= "[SEGURIDAD] Sucede ahora: $sucedeAhora. Heridos: $heridos. Personas: $cantPersonas. Sospechosos: $sospechosos. Fuga: $fuga. Vehículo: $vehiculo. Anónimo: $anonimo.";
    }

    // Insertar
    $sql = "INSERT INTO Incidentes (IdUsuario, EdificioAfectado, DepartamentoAfectado, Sangre, TipoEmergencia, PublicoOPrivado, Armas, Descripcion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississis", $idUsuario, $edificio, $depto, $sangre, $tipoEmergencia, $publicoPrivado, $armas, $descripcionFinal);

    if ($stmt->execute()) {
        // RESPUESTA ÉXITOSA
        echo json_encode(['status' => 'success', 'message' => '¡Alerta enviada! El vigilante ha sido notificado.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error SQL: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>