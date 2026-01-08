<?php
require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->prepare("
  SELECT Id, Titulo
  FROM Curso
  WHERE UsuarioId = ?
  ORDER BY Titulo
");
$stmt->execute([$_SESSION['usuario']]);

echo json_encode($stmt->fetchAll());
