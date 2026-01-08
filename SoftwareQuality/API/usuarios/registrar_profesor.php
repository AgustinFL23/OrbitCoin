<?php
require_once '../db.php';
session_start();
header('Content-Type: application/json');

if ($_SESSION['rol'] !== 'ADMIN') {
  http_response_code(403);
  echo json_encode(['success'=>false,'msg'=>'No autorizado']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$hash = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
  INSERT INTO Usuario
  (Usuario, Nombre, P_Apellido, S_Apellido, Contraseña, NivelId, RolId)
  VALUES (?, ?, ?, ?, ?, 4, (SELECT Id FROM Rol WHERE Nombre='PROFESOR'))
");

$stmt->execute([
  $data['usuario'],
  $data['nombre'],
  $data['p_apellido'],
  $data['s_apellido'] ?: null,
  $hash
]);

echo json_encode(['success'=>true]);
