<?php
require '../db.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
  echo json_encode(['success'=>false]);
  exit;
}

/* Obtener lección */
$stmt = $pdo->prepare("
  SELECT Id, Titulo, Contenido, Tipo, UrlContenido
  FROM Leccion
  WHERE Id = ?
");
$stmt->execute([$id]);
$leccion = $stmt->fetch();

if (!$leccion) {
  echo json_encode(['success'=>false]);
  exit;
}

/* Ver si tiene ejercicio */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Ejercicio WHERE LeccionId = ?");
$stmt->execute([$id]);
$tieneEjercicio = $stmt->fetchColumn() > 0;
if ($tieneEjercicio>0) {
  $stmt = $pdo->prepare(
    "SELECT Id FROM Ejercicio WHERE LeccionId = ? LIMIT 1"
);
$stmt->execute([$id]);
$IDEjercicio = $stmt->fetchColumn();

}
else {
  $IDEjercicio = 0;
}
/* Ver si tiene evaluación */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM Evaluacion WHERE LeccionId = ?");
$stmt->execute([$id]);
$tieneEvaluacion = $stmt->fetchColumn() > 0;
if ($tieneEvaluacion>0) {
  $stmt = $pdo->prepare(
    "SELECT Id FROM Evaluacion WHERE LeccionId = ? LIMIT 1"
);
$stmt->execute([$id]);
$IDEval = $stmt->fetchColumn();

}
else {
  $IDEval = 0;
}
/* Buscar siguiente lección */
$stmt = $pdo->prepare("
  SELECT lc2.LeccionId
  FROM Leccion_Curso lc
  JOIN Leccion_Curso lc2
    ON lc2.CursoId = lc.CursoId
   AND lc2.Lugar = lc.Lugar + 1
  WHERE lc.LeccionId = ?
");
$stmt->execute([$id]);
$siguiente = $stmt->fetchColumn();

echo json_encode([
  'success' => true,
  'leccion' => $leccion,
  'tieneEjercicio' => $tieneEjercicio,
  'tieneEvaluacion' => $tieneEvaluacion,
  'siguienteLeccion' => $siguiente,
  'IDEval'=>$IDEval,
  'IDEjercicio'=>$IDEjercicio,
]);
