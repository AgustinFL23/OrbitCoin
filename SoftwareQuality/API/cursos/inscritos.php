<?php
require_once '../db.php';
header('Content-Type: application/json');

$usuario = $_GET['usuario'] ?? null;
if (!$usuario) {
  http_response_code(400);
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT c.Id, c.Titulo
  FROM Curso c
  JOIN Curso_Usuario cu ON cu.CursoId = c.Id
  WHERE cu.UsuarioId = ?
");
$stmt->execute([$usuario]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
