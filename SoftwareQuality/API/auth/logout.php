<?php
session_start();
header('Content-Type: application/json');

/* Destruir sesión */
$_SESSION = [];
session_destroy();

/* Borrar cookie de sesión (PHPSESSID) */
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
  );
}

echo json_encode(['success' => true]);
