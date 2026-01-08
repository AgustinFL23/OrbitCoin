document.getElementById('loginForm').addEventListener('submit', function (e) {
  e.preventDefault(); // evita recarga

  const usuario = this.usuario.value.trim();
  const password = this.contrasenia.value;

  if (!usuario || !password) {
    mostrarMensaje('Completa todos los campos');
    return;
  }

  fetch('API/auth/login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      usuario: usuario,
      password: password
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      manejarLoginExitoso(data.rol);
      guardarUsuario(usuario)
    } else {
      mostrarMensaje('Usuario o contraseña incorrectos');
    }
  })
  .catch(() => {
    mostrarMensaje('Error de conexión');
  });
});

function manejarLoginExitoso(rol) {
  // Guardar rol (ejemplo simple)
  sessionStorage.setItem('rol', rol);

  // Redirección básica por rol
  switch (rol) {
    case 'ADMIN':
      window.location.href = 'Administrador/';
      break;
    case 'PROFESOR':
      window.location.href = 'Profesor/';
      break;
    case 'ALUMNO':
      window.location.href = 'Alumno/';
      break;
    default:
      mostrarMensaje('Rol no válido');
  }
}
function guardarUsuario(usuario) {
  const dias = 1; // duración de la cookie
  const fecha = new Date();
  fecha.setTime(fecha.getTime() + (dias * 24 * 60 * 60 * 1000));
  document.cookie = `usuario=${usuario}; expires=${fecha.toUTCString()}; path=/`;
}
function mostrarMensaje(texto) {
  document.getElementById('mensaje').textContent = texto;
}
