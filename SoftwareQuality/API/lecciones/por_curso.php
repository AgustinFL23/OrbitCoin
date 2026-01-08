<?php
require_once '../db.php';
header('Content-Type: application/json');

$cursoId = $_GET['cursoId'] ?? null;
if (!$cursoId) {
  http_response_code(400);
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT l.Id, l.Titulo
  FROM Leccion l
  JOIN Leccion_Curso lc ON lc.LeccionId = l.Id
  WHERE lc.CursoId = ?
  ORDER BY lc.Lugar ASC
");
$stmt->execute([$cursoId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
