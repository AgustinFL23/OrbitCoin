
async function cargarEjercicio(ejercicioId) {
  const usuario = obtenerCookie('usuario');

  const res = await fetch('/SoftwareQuality/API/ejercicios/obtener_preguntas.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({
      usuario,
      ejercicio_id: ejercicioId
    })
  });

  const data = await res.json();

  if (!data.success) {
    document.getElementById('ejercicio').innerHTML =
      `<p>${data.msg}</p>`;
    return;
  }

  pintarPreguntas(data.preguntas, ejercicioId);
}
function pintarPreguntas(preguntas, ejercicioId) {
  const cont = document.getElementById('ejercicio');
  console.log(ejercicioId);
  cont.innerHTML = `
    <form id="formEjercicio">
      ${preguntas.map((p, i) => `
        <div class="pregunta">
          <p><b>${i + 1}. ${p.Texto}</b></p>

          ${['A','B','C','D'].map(op => `
            <label>
              <input type="radio" name="p_${p.Id}" value="${op}">
              ${p['Opcion' + op]}
            </label><br>
          `).join('')}
        </div>
      `).join('')}

      <button type="submit">Enviar respuestas</button>
    </form>
  `;

  document.getElementById('formEjercicio')
    .onsubmit = e => enviarEjercicio(e, preguntas, ejercicioId);
}

async function enviarEjercicio(e, preguntas, ejercicioId) {
  e.preventDefault();

  const usuario = obtenerCookie('usuario');
  const respuestas = [];

preguntas.forEach(p => {
  const sel = document.querySelector(`input[name="p_${p.Id}"]:checked`);

  respuestas.push({
    pregunta_id: p.Id,
    respuesta: sel ? sel.value : 0   // A,B,C,D ó 0
  });
});


  const res = await fetch('/SoftwareQuality/API/ejercicios/calificar.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify({
      usuario,
      ejercicio_id: ejercicioId,
      respuestas
    })
  });

  const data = await res.json();

  document.getElementById('ejercicio').innerHTML = `
    <h2>Resultado</h2>
    <p>${data.puntos} / ${data.total}</p>
    <button onclick="history.back()">Volver</button>
  `;
}
