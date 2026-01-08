<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$leccionId = $data['leccion_id'] ?? null;
$numeroPreguntas = $data['numero_preguntas'] ?? 0;
$limite = $data['limite_repeticiones'] ?? 1;
$preguntas = $data['preguntas'] ?? [];

$pdo->beginTransaction();

try {
  $stmt = $pdo->prepare("
    INSERT INTO Ejercicio (LeccionId, NumeroPreguntas, LimiteRepeticiones)
    VALUES (?, ?, ?)
  ");
  $stmt->execute([$leccionId, $numeroPreguntas, $limite]);
  $ejercicioId = $pdo->lastInsertId();

  $stmtPregunta = $pdo->prepare("
    INSERT INTO Pregunta
    (Texto, OpcionA, OpcionB, OpcionC, OpcionD, OpcionCorrecta)
    VALUES (?, ?, ?, ?, ?, ?)
  ");

  $stmtRel = $pdo->prepare("
    INSERT INTO Ejercicio_Pregunta (EjercicioId, PreguntaId)
    VALUES (?, ?)
  ");

  foreach ($preguntas as $p) {
    $stmtPregunta->execute([
      $p['texto'],
      $p['a'],
      $p['b'],
      $p['c'],
      $p['d'],
      $p['correcta']
    ]);

    $stmtRel->execute([$ejercicioId, $pdo->lastInsertId()]);
  }

  $pdo->commit();
  echo json_encode(['success'=>true]);

} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(['success'=>false]);
}
