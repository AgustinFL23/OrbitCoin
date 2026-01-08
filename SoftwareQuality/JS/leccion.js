function getYoutubeId(url) {
  const pos = url.indexOf('v=');
  return pos !== -1 ? url.substring(pos + 2) : url;
}

async function cargarLeccion(id) {
  const res = await fetch(`/SoftwareQuality/API/lecciones/obtener.php?id=${id}`);
  const data = await res.json();

  if (!data.success) {
    alert('Lección no encontrada');
    return;
  }

  const l = data.leccion;
  const cont = document.getElementById('contenido');

  let media = '';
  if (l.Tipo == 1) {
  const videoId = getYoutubeId(l.UrlContenido);
  const embedUrl = `https://www.youtube.com/embed/${videoId}`;

  media = `
    <iframe
      src="${embedUrl}"
      allowfullscreen>
    </iframe>
  `;
}
 else {
    media = `<img src="${l.UrlContenido}" alt="Contenido de la lección">`;
  }

  cont.innerHTML = `
    <div class="leccion">
      <h1>${l.Titulo}</h1>

      <div class="leccion-media">
        ${media}
      </div>

      <div class="leccion-texto">
        ${l.Contenido}
      </div>

      <div class="leccion-botones">
        ${data.tieneEjercicio ? `<button onclick="irEjercicio(${data.IDEjercicio})">Ejercicio</button>` : ''}
        ${data.tieneEvaluacion ? `<button onclick="irEvaluacion(${data.IDEval})">Evaluación</button>` : ''}
        ${data.siguienteLeccion ? `<button onclick="cargarLeccion(${data.siguienteLeccion})">Siguiente</button>` : ''}
      </div>
    </div>
  `;
}
