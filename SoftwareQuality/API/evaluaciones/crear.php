<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  echo json_encode(['success'=>false]);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$leccionId = $data['leccion_id'] ?? null;
$numeroPreguntas = $data['numero_preguntas'] ?? 0;
$preguntas = $data['preguntas'] ?? [];

if (!$leccionId || empty($preguntas)) {
  echo json_encode(['success'=>false,'msg'=>'Datos incompletos']);
  exit;
}

$pdo->beginTransaction();

try {
  // Crear evaluación
  $stmt = $pdo->prepare("
    INSERT INTO Evaluacion (LeccionId, NumeroPreguntas)
    VALUES (?, ?)
  ");
  $stmt->execute([$leccionId, $numeroPreguntas]);
  $evaluacionId = $pdo->lastInsertId();

  // Insertar preguntas y relación
  $stmtPregunta = $pdo->prepare("
    INSERT INTO Pregunta
    (Texto, OpcionA, OpcionB, OpcionC, OpcionD, OpcionCorrecta)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $stmtRel = $pdo->prepare("
    INSERT INTO Evaluacion_Pregunta (EvaluacionId, PreguntaId)
    VALUES (?, ?)
  ");

  foreach ($preguntas as $p) {
    $stmtPregunta->execute([
      $p['texto'],
      $p['a'],
      $p['b'],
      $p['c'],
      $p['d'],
      $p['correcta'] // ya viene como entero 1-4
    ]);

    $preguntaId = $pdo->lastInsertId();
    $stmtRel->execute([$evaluacionId, $preguntaId]);
  }

  $pdo->commit();
  echo json_encode(['success'=>true]);

} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success'=>false,'msg'=>$e->getMessage()]);
}
