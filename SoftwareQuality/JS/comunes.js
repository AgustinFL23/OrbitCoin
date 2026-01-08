function apuntarseCurso() {
  history.pushState({}, '', '/SoftwareQuality/Cursos/Inscribirse');
  cargarVista('/SoftwareQuality/Alumno/Cursos/cursos_disponibles.html',
    () => cargarCursosNivel());
}
async function cargarVista(ruta, onLoad = null) {
  const contenedor = document.getElementById('contenido');
  const html = await fetch(ruta).then(r => r.text());
  contenedor.innerHTML = html;

  if (onLoad) onLoad();
}
function irEjercicio(ejercicioId) {
  history.pushState({}, '', `/SoftwareQuality/Ejercicio/${ejercicioId}`);
  cargarVista('/SoftwareQuality/Alumno/Curso/Ejercicio', () => {
    cargarEjercicio(ejercicioId);
  });
}
