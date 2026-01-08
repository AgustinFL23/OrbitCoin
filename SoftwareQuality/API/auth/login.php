<?php
require '../db.php';
header('Content-Type: application/json');

// Leer JSON del body
$input = json_decode(file_get_contents('php://input'), true);

$usuario  = $input['usuario']  ?? '';
$password = $input['password'] ?? '';

if ($usuario === '' || $password === '') {
  echo json_encode(['success' => false, 'rol' => null]);
  exit;
}

// Buscar usuario + rol
$sql = "
SELECT u.Contraseña, r.Nombre AS Rol
FROM Usuario u
JOIN Rol r ON r.Id = u.RolId
WHERE u.Usuario = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario]);

$user = $stmt->fetch();

if (!$user) {
  echo json_encode(['success' => false, 'rol' => null]);
  exit;
}

// Verificar contraseña (hash)
if (!password_verify($password, $user['Contraseña'])) {
  echo json_encode(['success' => false, 'rol' => null]);
  exit;
}

// Login correcto
session_start();

$_SESSION['usuario'] = $usuario;
$_SESSION['rol'] = $user['Rol'];

echo json_encode([
  'success' => true,
  'rol' => $user['Rol']
]);
