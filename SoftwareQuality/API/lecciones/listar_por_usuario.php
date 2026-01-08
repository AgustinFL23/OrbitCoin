<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  http_response_code(403);
  echo json_encode([]);
  exit;
}

$usuario = $_SESSION['usuario'];

/*
  Lecciones → Curso → Usuario creador
*/
$stmt = $pdo->prepare("
  SELECT DISTINCT l.Id, l.Titulo
  FROM Leccion l
  JOIN Leccion_Curso lc ON lc.LeccionId = l.Id
  JOIN Curso c ON c.Id = lc.CursoId
  WHERE c.UsuarioId = ?
  ORDER BY l.Titulo
");

$stmt->execute([$usuario]);

echo json_encode($stmt->fetchAll());
