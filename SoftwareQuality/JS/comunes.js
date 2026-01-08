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
function irEvaluacion(evaluacionId) {
  history.pushState({}, '', `/SoftwareQuality/Evaluacion/${evaluacionId}`);
  cargarVista('/SoftwareQuality/Alumno/Curso/Evaluacion', () => {
    cargarEvaluacion(evaluacionId);
  });
}
function irInicio() {
  const rol = sessionStorage.getItem('rol');
  switch (rol) {
    case 'ADMIN':
      history.pushState({}, '', `/SoftwareQuality/Administrador`);
      cargarVista('/SoftwareQuality/Administrador', () => {  });
      break;
    case 'PROFESOR':
      history.pushState({}, '', `/SoftwareQuality/Profesor`);
      cargarVista('/SoftwareQuality/Profesor', () => {  });
      break;
    case 'ALUMNO':
      history.pushState({}, '', `/SoftwareQuality/Alumno`);
      cargarVista('/SoftwareQuality/Alumno', () => {  });
      break;
    default:
      mostrarMensaje('Rol no válido');
  }
  
}
async function logout() {
  await fetch('/SoftwareQuality/API/auth/logout.php', {
    method: 'POST',
    credentials: 'include'
  });

  /* Limpieza local */
  document.cookie = 'usuario=; Max-Age=0; path=/';
  sessionStorage.clear();

  /* Redirección */
  location.href = '/SoftwareQuality';
}
