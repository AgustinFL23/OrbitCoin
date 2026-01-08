function initCrearEvaluacion() {
  const form = document.getElementById('formEvaluacion');
  if (!form) return;

  form.onsubmit = async e => {
    e.preventDefault();

    const res = await fetch('/SoftwareQuality/API/evaluaciones/crear.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      credentials:'include',
      body: JSON.stringify({
        leccion_id: leccionSelect.value,
        numero_preguntas: numPreguntas.value,
        preguntas: leerPreguntas()
      })
    });

    if ((await res.json()).success) {
      //form.reset();
      document.getElementById('preguntas').innerHTML = '';
      alert('Evaluación creada');
    }
  };
}
