<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'ALUMNO') {
  http_response_code(403);
  echo json_encode(['success'=>false]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cursoId = $data['curso_id'] ?? null;

if (!$cursoId) {
  echo json_encode(['success'=>false]);
  exit;
}

/* Evitar duplicados */
$stmt = $pdo->prepare("
  SELECT 1 FROM Curso_Usuario
  WHERE UsuarioId = ? AND CursoId = ?
");
$stmt->execute([$_SESSION['usuario'], $cursoId]);

if ($stmt->fetch()) {
  echo json_encode(['success'=>false,'msg'=>'Ya inscrito']);
  exit;
}

/* Insertar inscripción */
$stmt = $pdo->prepare("
  INSERT INTO Curso_Usuario (UsuarioId, CursoId, FechaInicio)
  VALUES (?, ?, CURRENT_DATE)
");
$stmt->execute([$_SESSION['usuario'], $cursoId]);

echo json_encode(['success'=>true]);
