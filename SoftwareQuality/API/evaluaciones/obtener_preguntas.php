<?php
require_once '../db.php';
session_start();
header('Content-Type: application/json');

$usuario = $_SESSION['usuario'] ?? null;
$evaluacionId = $_GET['evaluacion_id'] ?? null;

if (!$usuario || !$evaluacionId) {
  http_response_code(400);
  echo json_encode(['success'=>false,'msg'=>'Datos incompletos']);
  exit;
}

/* ¿Ya evaluado? */
$stmt = $pdo->prepare("
  SELECT 1 FROM Evaluacion_Usuario
  WHERE UsuarioId = ? AND EvaluacionId = ?
");
$stmt->execute([$usuario, $evaluacionId]);

if ($stmt->fetch()) {
  echo json_encode(['success'=>false,'msg'=>'Evaluación ya realizada']);
  exit;
}

/* Datos de evaluación */
$stmt = $pdo->prepare("
  SELECT NumeroPreguntas
  FROM Evaluacion
  WHERE Id = ?
");
$stmt->execute([$evaluacionId]);
$eval = $stmt->fetch();

if (!$eval) {
  echo json_encode(['success'=>false,'msg'=>'Evaluación no existe']);
  exit;
}

/* Preguntas aleatorias */
$stmt = $pdo->prepare("
  SELECT p.Id, p.Texto, p.OpcionA, p.OpcionB, p.OpcionC, p.OpcionD
  FROM Pregunta p
  JOIN Evaluacion_Pregunta ep ON ep.PreguntaId = p.Id
  WHERE ep.EvaluacionId = ?
  ORDER BY RAND()
  LIMIT ?
");
$stmt->bindValue(1, $evaluacionId, PDO::PARAM_INT);
$stmt->bindValue(2, $eval['NumeroPreguntas'], PDO::PARAM_INT);
$stmt->execute();

echo json_encode([
  'success' => true,
  'preguntas' => $stmt->fetchAll()
]);
