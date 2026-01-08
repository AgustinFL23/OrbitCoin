function initCrearCurso() {
  const form = document.getElementById('formCurso');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const i = e.target.querySelectorAll('input, textarea');
    const nivelSelect = document.getElementById('nivelSelect');

    const res = await fetch('/SoftwareQuality/API/cursos/crear_cursos.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        titulo: i[0].value,
        contenido: i[1].value,
        nivel_id: nivelSelect.value
      })
    });

    const data = await res.json();

    if (data.success) {
      form.reset();
      nivelSelect.selectedIndex = 0;
      document.getElementById('msg').textContent = 'Curso creado correctamente';
    } else {
      alert(data.msg || 'Error al crear curso');
    }
  };
}
