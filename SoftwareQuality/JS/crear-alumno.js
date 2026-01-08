function initCrearAlumno() {
  const form = document.getElementById('formAlumno');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const i = e.target.querySelectorAll('input');
    const nivelSelect = document.getElementById('nivelSelect');

    const res = await fetch('/SoftwareQuality/API/usuarios/crear_alumno.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        usuario: i[0].value,
        nombre: i[1].value,
        p_apellido: i[2].value,
        s_apellido: i[3].value,
        password: i[4].value,
        nivel_id: nivelSelect.value
      })
    });

    const data = await res.json();

    if (data.success) {
      form.reset();            // 👈 LIMPIA FORMULARIO
      nivelSelect.selectedIndex = 0;
      alert('Alumno creado');
    } else {
      alert(data.msg || 'Error al crear alumno');
    }
  };
}
