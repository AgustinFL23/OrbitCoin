<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'], $_SESSION['rol']) || !in_array($_SESSION['rol'], ['ADMIN','PROFESOR'])) {
  http_response_code(403);
  echo json_encode(['success'=>false,'msg'=>'No autorizado']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$titulo    = $data['titulo'] ?? '';
$contenido = $data['contenido'] ?? '';
$tipo      = $data['tipo'] ?? null; // 0 = imagen, 1 = video
$url       = $data['url'] ?? '';
$cursoId   = $data['curso_id'] ?? null;

if (
  $titulo === '' ||
  $contenido === '' ||
  $tipo === null ||
  $url === '' ||
  !$cursoId
) {
  echo json_encode(['success'=>false,'msg'=>'Datos incompletos']);
  exit;
}

try {
  $pdo->beginTransaction();

  /* Verificar curso del usuario */
  $stmt = $pdo->prepare("
    SELECT Id FROM Curso
    WHERE Id = ? AND UsuarioId = ?
  ");
  $stmt->execute([$cursoId, $_SESSION['usuario']]);

  if (!$stmt->fetch()) {
    throw new Exception('Curso no válido');
  }

  /* Insertar lección */
  $stmt = $pdo->prepare("
    INSERT INTO Leccion (Titulo, Contenido, Tipo, UrlContenido)
    VALUES (?, ?, ?, ?)
  ");
  $stmt->execute([$titulo, $contenido, $tipo, $url]);

  $leccionId = $pdo->lastInsertId();

  /* Calcular lugar */
  $stmt = $pdo->prepare("
    SELECT COALESCE(MAX(Lugar), 0) + 1 AS Lugar
    FROM Leccion_Curso
    WHERE CursoId = ?
  ");
  $stmt->execute([$cursoId]);
  $lugar = $stmt->fetch()['Lugar'];

  /* Insertar relación */
  $stmt = $pdo->prepare("
    INSERT INTO Leccion_Curso (CursoId, LeccionId, Lugar)
    VALUES (?, ?, ?)
  ");
  $stmt->execute([$cursoId, $leccionId, $lugar]);

  $pdo->commit();

  echo json_encode(['success'=>true]);

} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success'=>false,'msg'=>$e->getMessage()]);
}
