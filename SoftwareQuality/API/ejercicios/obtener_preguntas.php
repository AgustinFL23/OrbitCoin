<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  exit;
}

$usuarioId   = $_SESSION['usuario'];
$input       = json_decode(file_get_contents('php://input'), true);
$ejercicioId = $input['ejercicio_id'] ?? null;

if (!$ejercicioId) {
  echo json_encode(['success'=>false,'msg'=>'Ejercicio inválido']);
  exit;
}

/* Obtener numero de preguntas */
$stmt = $pdo->prepare("
  SELECT NumeroPreguntas, LimiteRepeticiones
  FROM Ejercicio
  WHERE Id = ?
");
$stmt->execute([$ejercicioId]);
$ejercicio = $stmt->fetch();

/* Intentos (igual que antes) */
$stmt = $pdo->prepare("
  SELECT Intento
  FROM Ejercicio_Usuario
  WHERE UsuarioId = ? AND EjercicioId = ?
");
$stmt->execute([$usuarioId, $ejercicioId]);
$intentos = $stmt->fetchColumn() ?? 0;

if ($intentos >= $ejercicio['LimiteRepeticiones']) {
  echo json_encode(['success'=>false,'msg'=>'Límite de intentos alcanzado']);
  exit;
}

/* 🔥 PREGUNTAS ALEATORIAS */
$stmt = $pdo->prepare("
  SELECT p.Id, p.Texto, p.OpcionA, p.OpcionB, p.OpcionC, p.OpcionD
  FROM Pregunta p
  JOIN Ejercicio_Pregunta ep ON ep.PreguntaId = p.Id
  WHERE ep.EjercicioId = ?
  ORDER BY RAND()
  LIMIT ?
");
$stmt->bindValue(1, $ejercicioId, PDO::PARAM_INT);
$stmt->bindValue(2, $ejercicio['NumeroPreguntas'], PDO::PARAM_INT);
$stmt->execute();

echo json_encode([
  'success' => true,
  'preguntas' => $stmt->fetchAll()
]);
