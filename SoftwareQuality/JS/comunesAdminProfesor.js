async function cargarLecciones() {
  const select = document.getElementById('leccionSelect');
  if (!select) return;

  const res = await fetch('/SoftwareQuality/API/lecciones/listar_por_usuario.php', {
    credentials: 'include'
  });

  const lecciones = await res.json();
  select.innerHTML = '';

  lecciones.forEach(l => {
    const opt = document.createElement('option');
    opt.value = l.Id;
    opt.textContent = l.Titulo;
    select.appendChild(opt);
  });
}

function crearAlumno() {
  history.pushState({}, '', '/SoftwareQuality/Usuario/Crear_alumno');

  cargarVista(
    '/SoftwareQuality/Usuario/Crear_alumno/index.html',
    () => {
      cargarNiveles();
      initCrearAlumno(); 
    }
  );
}
function registrarProfesor() {
  history.pushState({}, '', '/SoftwareQuality/RegistrarProfesor');

  cargarVista(
    '/SoftwareQuality/Usuario/registrar_profesor.html',
    () => {
      initCrearProfesor(); 
    }
  );
}

async function cargarNiveles() {
  const res = await fetch('/SoftwareQuality/API/niveles/listar.php');
  const niveles = await res.json();

  const select = document.getElementById('nivelSelect');

  niveles.forEach(n => {
    const opt = document.createElement('option');
    opt.value = n.Id;
    opt.textContent = n.Nombre;
    opt.title = n.Descripcion; // tooltip
    select.appendChild(opt);
  });
}
function crearCurso() {
  history.pushState({}, '', '/SoftwareQuality/Curso/Crear');

  cargarVista(
    '/SoftwareQuality/Usuario/crear_curso.html',
    () => {
      cargarNiveles();
      initCrearCurso();
    }
  );
}
function crearLeccion() {
  history.pushState({}, '', '/SoftwareQuality/Administrador/Leccion/Crear');

  cargarVista(
    '/SoftwareQuality/Usuario/crear_leccion.html',
    () => {
      cargarCursosPropios();
      initCrearLeccion();
    }
  );
}
function agregarPregunta() {
  const cont = document.getElementById('preguntas');

  cont.insertAdjacentHTML('beforeend', `
    <div class="card pregunta">
      <input placeholder="Texto pregunta">
      <input placeholder="Opción A">
      <input placeholder="Opción B">
      <input placeholder="Opción C">
      <input placeholder="Opción D">
      <select>
        <option value="1">A</option>
        <option value="2">B</option>
        <option value="3">C</option>
        <option value="4">D</option>
      </select>
    </div>
  `);
}

function leerPreguntas() {
  return [...document.querySelectorAll('.pregunta')].map(p => {
    const i = p.querySelectorAll('input');
    const c = p.querySelector('select').value;

    return {
      texto: i[0].value,
      a: i[1].value,
      b: i[2].value,
      c: i[3].value,
      d: i[4].value,
      correcta: parseInt(c)
    };
  });
}
function crearEvaluacion(leccionId) {
  history.pushState({}, '', `/SoftwareQuality/Evaluacion/Crear?leccion=${leccionId}`);

  cargarVista('/SoftwareQuality/Usuario/crear_evaluacion.html', () => {
    cargarLecciones();
    initCrearEvaluacion();
    document.getElementById('leccionSelect').value = leccionId;
  });
}

function crearEjercicio(leccionId) {
  history.pushState({}, '', `/SoftwareQuality/Ejercicio/Crear?leccion=${leccionId}`);

  cargarVista('/SoftwareQuality/Usuario/crear_ejercicio.html', () => {
    cargarLecciones();
    initCrearEjercicio();
    document.getElementById('leccionSelect').value = leccionId;
  });
}

