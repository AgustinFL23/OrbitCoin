function initCrearEjercicio() {
  const form = document.getElementById('formEjercicio');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const res = await fetch('/SoftwareQuality/API/ejercicios/crear.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      credentials:'include',
      body: JSON.stringify({
        leccion_id: leccionSelect.value,
        numero_preguntas: numPreguntas.value,
        limite_repeticiones: limite.value,
        preguntas: leerPreguntas()
      })
    });

    if ((await res.json()).success) {
      form.reset();
      preguntas.innerHTML = '';
      alert('Ejercicio creado');
    }
  };
}
