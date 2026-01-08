function obtenerCookie(nombre) {
  const match = document.cookie.match(
    new RegExp('(^| )' + nombre + '=([^;]+)')
  );
  return match ? decodeURIComponent(match[2]) : null;
}

async function cargarNavbar() {
  const usuario = obtenerCookie('usuario');
  const rol = sessionStorage.getItem('rol');

  if (!usuario || !rol) {
    location.href = '/login.html';
    return;
  }

  const res = await fetch(`/SoftwareQuality/API/cursos/inscritos.php?usuario=${usuario}`);
  const cursos = await res.json();

  const nav = document.getElementById('navbar');

  const menuCursosInscritos = `
    <li><a href="#">Mis cursos</a>
      <ul>
        ${cursos.map(c => `
          <li>
            <a href="#" onclick="cargarCurso(${c.Id}, '${c.Titulo}')">
              ${c.Titulo}
            </a>
          </li>
        `).join('')}
        <li>
          <a href="#" onclick="apuntarseCurso()">➕ Apuntarte a otro curso</a>
        </li>
      </ul>
    </li>
  `;

 let menuUsuarios = '';

if (rol === 'ADMIN') {
  menuUsuarios = `
    <li><a href="#">Usuarios</a>
      <ul>
        <li><a href="#" onclick="crearAlumno()">Crear alumno</a></li>
        <li><a href="#" onclick="registrarProfesor()">Registrar profesor</a></li>
      </ul>
    </li>
  `;
}

if (rol === 'PROFESOR') {
  menuUsuarios = `
    <li><a href="#">Usuarios</a>
      <ul>
        <li><a href="#" onclick="crearAlumno()">Crear alumno</a></li>
      </ul>
    </li>
  `;
}


  const menuCursosGestion = (rol === 'ADMIN' || rol === 'PROFESOR') ? `
    <li><a href="#">Cursos</a>
      <ul>
        <li><a href="#" onclick="crearCurso()">Crear curso</a></li>
        <li><a href="#" onclick="listarCursos()">Listar cursos</a></li>
        <li><a href="#" onclick="modificarCurso()">Modificar curso</a></li>
      </ul>
    </li>
  ` : '';

  const menuLecciones = (rol === 'ADMIN' || rol === 'PROFESOR') ? `
    <li><a href="#">Lecciones</a>
      <ul>
        <li><a href="#" onclick="crearLeccion()">Crear lección</a></li>
        <li><a href="#" onclick="listarLecciones()">Listar lecciones</a></li>
        <li><a href="#" onclick="modificarLeccion()">Modificar lección</a></li>
      </ul>
    </li>
  ` : '';

  const menuEvaluaciones = (rol === 'ADMIN' || rol === 'PROFESOR') ? `
    <li><a href="#">Evaluaciones</a>
      <ul>
        <li><a href="#" onclick="crearEjercicio()">Crear ejercicio</a></li>
        <li><a href="#" onclick="crearEvaluacion()">Crear evaluación</a></li>
      </ul>
    </li>
  ` : '';

  nav.innerHTML = `
    <ul class="nav">
      <li><a href="#" onclick="irInicio()">Inicio</a></li>

      ${menuCursosInscritos}
      ${menuUsuarios}
      ${menuCursosGestion}
      ${menuLecciones}
      ${menuEvaluaciones}

      <li><a href="#" onclick="logout()">Salir (${usuario})</a></li>
    </ul>
  `;
}

async function cargarCurso(cursoId, titulo) {
  history.pushState({ cursoId }, '', `/curso/${cursoId}`);

  const main = document.getElementById('contenido');
  const html = await fetch('/SoftwareQuality/Alumno/Curso').then(r => r.text());
  main.innerHTML = html;

  document.getElementById('curso-titulo').textContent = titulo;

  const res = await fetch(`/SoftwareQuality/API/lecciones/por_curso.php?cursoId=${cursoId}`);
  const lecciones = await res.json();

  const ul = document.getElementById('lista-lecciones');
  ul.innerHTML = lecciones.map(l =>
    `<li>
      <a href="#" onclick="cargarLeccion(${l.Id}, '${l.Titulo}')">
        ${l.Titulo}
      </a>
    </li>`
  ).join('');
}async function cargarLeccion(id, titulo) {
  history.pushState({ leccionId: id }, '', `/leccion/${id}`);

  const main = document.getElementById('contenido');
  const html = await fetch('/SoftwareQuality/Alumno/Curso/Leccion').then(r => r.text());
  main.innerHTML = html;

  document.getElementById('leccion-titulo').textContent = titulo;
  document.getElementById('leccion-contenido').textContent =
    'Contenido de la lección (API pendiente)';
}

