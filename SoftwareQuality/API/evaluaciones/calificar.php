<?php
require_once '../db.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$usuario = $_SESSION['usuario'] ?? null;
$evaluacionId = $data['evaluacion_id'] ?? null;
$respuestas = $data['respuestas'] ?? [];

if (!$usuario || !$evaluacionId || !is_array($respuestas)) {
  http_response_code(400);
  echo json_encode(['success'=>false,'msg'=>'Datos inválidos']);
  exit;
}

/* Verificar que no exista */
$stmt = $pdo->prepare("
  SELECT 1 FROM Evaluacion_Usuario
  WHERE UsuarioId = ? AND EvaluacionId = ?
");
$stmt->execute([$usuario, $evaluacionId]);

if ($stmt->fetch()) {
  echo json_encode(['success'=>false,'msg'=>'Evaluación ya enviada']);
  exit;
}

/* Total preguntas */
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

$map = ['A'=>1,'B'=>2,'C'=>3,'D'=>4];
$puntos = 0;

$stmtPregunta = $pdo->prepare("
  SELECT OpcionCorrecta
  FROM Pregunta
  WHERE Id = ?
");

foreach ($respuestas as $r) {
  $stmtPregunta->execute([$r['pregunta_id']]);
  $correcta = $stmtPregunta->fetchColumn();

  $valor = $map[$r['respuesta']] ?? 0;

  if ((int)$valor === (int)$correcta) {
    $puntos++;
  }
}

$calificacion = ($puntos * 10) / $eval['NumeroPreguntas'];

/* Guardar resultado */
$stmt = $pdo->prepare("
  INSERT INTO Evaluacion_Usuario (UsuarioId, EvaluacionId, Calificacion)
  VALUES (?, ?, ?)
");
$stmt->execute([$usuario, $evaluacionId, $calificacion]);

echo json_encode([
  'success' => true,
  'puntos' => $puntos,
  'total' => $eval['NumeroPreguntas'],
  'calificacion' => round($calificacion, 2)
]);
