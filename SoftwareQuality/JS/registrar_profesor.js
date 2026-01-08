function initCrearProfesor() {
  const form = document.getElementById('formProfesor');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const i = e.target.querySelectorAll('input');

    const res = await fetch('/SoftwareQuality/API/usuarios/registrar_profesor.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',
      body: JSON.stringify({
        usuario: i[0].value,
        nombre: i[1].value,
        p_apellido: i[2].value,
        s_apellido: i[3].value,
        password: i[4].value
      })
    });

    const data = await res.json();

    if (data.success) {
      form.reset();               // 👈 LIMPIA FORMULARIO
      const msg = document.getElementById('msg');
      if (msg) msg.textContent = 'Profesor registrado';
      alert('Profesor creado correctamente');
    } else {
      alert(data.msg || 'Error al crear profesor');
    }
  };
}
