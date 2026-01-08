async function cargarCursosPropios() {
  const res = await fetch('/SoftwareQuality/API/cursos/listar_propios.php', {
    credentials: 'include'
  });
  const cursos = await res.json();

  const select = document.getElementById('cursoSelect');
  select.innerHTML = '<option value="">Selecciona un curso</option>';

  cursos.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.Id;
    opt.textContent = c.Titulo;
    select.appendChild(opt);
  });
}

function initCrearLeccion() {
  const form = document.getElementById('formLeccion');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const inputs = e.target.querySelectorAll('input, textarea');
    const tipo   = document.getElementById('tipoSelect').value;
    const curso  = document.getElementById('cursoSelect').value;

    const res = await fetch('/SoftwareQuality/API/lecciones/crear_leccion.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      credentials: 'include',
      body: JSON.stringify({
        titulo: inputs[0].value,
        contenido: inputs[1].value,
        tipo: Number(tipo),
        url: inputs[2].value,
        curso_id: curso
      })
    });

    const data = await res.json();

    if (data.success) {
      form.reset();
      document.getElementById('msg').textContent = 'Lección creada correctamente';
    } else {
      alert(data.msg || 'Error al crear lección');
    }
  };
}
