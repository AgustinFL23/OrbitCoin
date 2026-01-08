async function cargarEvaluacion(id) {
  const res = await fetch(`/SoftwareQuality/API/evaluaciones/obtener_preguntas.php?evaluacion_id=${id}`, {
    credentials:'include'
  });
  const data = await res.json();

  if (!data.success) {
    alert(data.msg);
    return;
  }

  const form = document.getElementById('formEvaluacion');
  form.innerHTML = '';

  data.preguntas.forEach(p => {
    form.innerHTML += `
      <p>${p.Texto}</p>
      ${['A','B','C','D'].map(l => `
        <label>
          <input type="radio" name="p_${p.Id}" value="${l}">
          ${p['Opcion'+l]}
        </label><br>
      `).join('')}
      <hr>
    `;
  });

  document.getElementById('btnEnviar').onclick =
    e => enviarEvaluacion(e, data.preguntas, id);
}

async function enviarEvaluacion(e, preguntas, evaluacionId) {
  e.preventDefault();

  const respuestas = preguntas.map(p => {
    const sel = document.querySelector(`input[name="p_${p.Id}"]:checked`);
    return {
      pregunta_id: p.Id,
      respuesta: sel ? sel.value : ''
    };
  });

  const res = await fetch('/SoftwareQuality/API/evaluaciones/calificar.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    credentials:'include',
    body: JSON.stringify({ evaluacion_id: evaluacionId, respuestas })
  });

  const r = await res.json();
  document.getElementById('resultado').textContent =
    `Resultado: ${r.puntos}/${r.total} | Calificación: ${r.calificacion}`;
}
