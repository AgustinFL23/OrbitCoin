<?php
require_once '../db.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT Id, Nombre, Descripcion FROM Nivel");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
