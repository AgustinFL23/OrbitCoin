async function cargarCursosNivel() {
  const cont = document.getElementById('listaCursos');
  if (!cont) return;

  const res = await fetch('/SoftwareQuality/API/cursos/por_nivel.php', {
    credentials: 'include'
  });

  const cursos = await res.json();
  cont.innerHTML = '';

  if (!cursos.length) {
    cont.textContent = 'No hay cursos disponibles para tu nivel';
    return;
  }

  cursos.forEach(c => {
    const div = document.createElement('div');
    div.className = 'curso';
    div.innerHTML = `
      <h3>${c.Titulo}</h3>
      <p>${c.Contenido}</p>
    `;

    div.onclick = () => inscribirseCurso(c.Id);

    cont.appendChild(div);
  });
}

async function inscribirseCurso(cursoId) {
  if (!confirm('¿Inscribirte en este curso?')) return;

  const res = await fetch('/SoftwareQuality/API/cursos/inscribirse.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    credentials: 'include',
    body: JSON.stringify({ curso_id: cursoId })
  });

  const data = await res.json();

  if (data.success) {
    alert('Inscripción exitosa');
    cargarCursosNivel(); // refrescar lista
  } else {
    alert(data.msg || 'Error al inscribirse');
  }
}
