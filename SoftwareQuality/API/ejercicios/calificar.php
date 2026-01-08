<?php
require_once '../db.php';
header('Content-Type: application/json');

/* =========================
   Leer y validar entrada
   ========================= */

$data = json_decode(file_get_contents('php://input'), true);

$usuario     = $data['usuario'] ?? null;
$ejercicioId = $data['ejercicio_id'] ?? null;
$respuestas  = $data['respuestas'] ?? [];

if (!$usuario || !$ejercicioId || !is_array($respuestas)) {
  http_response_code(400);
  echo json_encode(['success'=>false,'msg'=>'Datos incompletos']);
  exit;
}

/* =========================
   Obtener ejercicio
   ========================= */

$stmt = $pdo->prepare("
  SELECT NumeroPreguntas
  FROM Ejercicio
  WHERE Id = ?
");
$stmt->execute([$ejercicioId]);
$ejercicio = $stmt->fetch();

if (!$ejercicio) {
  echo json_encode(['success'=>false,'msg'=>'Ejercicio no existe']);
  exit;
}

/* =========================
   Calificar
   ========================= */

$puntos = 0;

/* Mapeo de letras a entero */
$map = ['A'=>1,'B'=>2,'C'=>3,'D'=>4];

$stmtPregunta = $pdo->prepare("
  SELECT OpcionCorrecta
  FROM Pregunta
  WHERE Id = ?
");

foreach ($respuestas as $r) {
  $preguntaId = $r['pregunta_id'] ?? null;
  $respuesta  = $r['respuesta'] ?? 0;

  if (!$preguntaId) continue;

  $stmtPregunta->execute([$preguntaId]);
  $correcta = $stmtPregunta->fetchColumn();

  if (!$correcta) continue;

  $valor = $map[$respuesta] ?? 0;

  if ((int)$valor === (int)$correcta) {
    $puntos++;
  }
}

/* =========================
   Registrar intento
   ========================= */

$stmt = $pdo->prepare("
  INSERT INTO Ejercicio_Usuario (UsuarioId, EjercicioId, Intento)
  VALUES (?, ?, 1)
  ON DUPLICATE KEY UPDATE Intento = Intento + 1
");
$stmt->execute([$usuario, $ejercicioId]);

/* =========================
   Respuesta
   ========================= */

echo json_encode([
  'success' => true,
  'puntos'  => $puntos,
  'total'   => $ejercicio['NumeroPreguntas']
]);
