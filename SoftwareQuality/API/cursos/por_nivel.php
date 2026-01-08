<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'ALUMNO') {
  http_response_code(403);
  echo json_encode([]);
  exit;
}

/* Obtener nivel del alumno */
$stmt = $pdo->prepare("
  SELECT NivelId
  FROM Usuario
  WHERE Usuario = ?
");
$stmt->execute([$_SESSION['usuario']]);
$nivel = $stmt->fetchColumn();

if (!$nivel) {
  echo json_encode([]);
  exit;
}

/* Cursos del nivel NO inscritos */
$stmt = $pdo->prepare("
  SELECT c.Id, c.Titulo, c.Contenido
  FROM Curso c
  WHERE c.NivelId = ?
  AND c.Id NOT IN (
    SELECT CursoId FROM Curso_Usuario WHERE UsuarioId = ?
  )
");
$stmt->execute([$nivel, $_SESSION['usuario']]);

echo json_encode($stmt->fetchAll());
