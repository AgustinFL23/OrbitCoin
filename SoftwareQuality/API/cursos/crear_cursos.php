<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

// Verificar sesión y rol
if (!isset($_SESSION['usuario'], $_SESSION['rol']) || !in_array($_SESSION['rol'], ['ADMIN','PROFESOR'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'msg' => 'No autorizado']);
    exit;
}

// Leer JSON del body
$data = json_decode(file_get_contents('php://input'), true);

// Validar campos obligatorios
$titulo   = $data['titulo'] ?? '';
$contenido = $data['contenido'] ?? '';
$nivelId  = $data['nivel_id'] ?? '';

if (!$titulo || !$contenido || !$nivelId) {
    echo json_encode(['success'=>false, 'msg'=>'Campos incompletos']);
    exit;
}

// Obtener UsuarioId desde sesión
$usuarioId = $_SESSION['usuario'];

try {
    $stmt = $pdo->prepare("
        INSERT INTO Curso (Titulo, Contenido, NivelId, UsuarioId)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$titulo, $contenido, $nivelId, $usuarioId]);

    echo json_encode(['success'=>true, 'msg'=>'Curso creado']);
} catch (PDOException $e) {
    echo json_encode(['success'=>false, 'msg'=>$e->getMessage()]);
}
