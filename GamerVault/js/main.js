// ================================================
// GAMERVAULT - main.js
// Acá va todo el JavaScript del proyecto
// ================================================


// ------------------------------------------------
// FUNCIÓN PARA LEER LA URL
// Sirve para saber qué juego buscar o mostrar.
// Por ejemplo si la URL es: resultados.html?q=elden+ring
// leerURL('q') nos devuelve "elden ring"
// ------------------------------------------------
function leerURL(parametro) {
    var url = new URLSearchParams(window.location.search);
    return url.get(parametro);
}


// ================================================
// BUSCADOR — funciona en index.html y resultados.html
// ================================================

// Buscamos el input del buscador en la página
var inputBusqueda = document.getElementById('busqueda');

// Si existe el input (o sea, si estamos en una página que tiene buscador)
if (inputBusqueda) {

    // Cuando el usuario presiona una tecla
    inputBusqueda.addEventListener('keydown', function(evento) {

        // Si la tecla fue Enter
        if (evento.key === 'Enter') {

            // Guardamos lo que escribió el usuario
            var termino = inputBusqueda.value;

            // Si no escribió nada, no hacemos nada
            if (termino == '') {
                return;
            }

            // Mandamos al usuario a la página de resultados
            // encodeURIComponent convierte los espacios y caracteres especiales para la URL
            window.location.href = 'resultados.html?q=' + encodeURIComponent(termino);
        }
    });
}

// También funciona con el botón Buscar
var botonBuscar = document.querySelector('form button[type="submit"]');

if (botonBuscar) {
    botonBuscar.addEventListener('click', function(evento) {

        // Evitamos que el formulario recargue la página
        evento.preventDefault();

        var termino = inputBusqueda.value;

        if (termino == '') {
            return;
        }

        window.location.href = 'resultados.html?q=' + encodeURIComponent(termino);
    });
}


// ================================================
// RESULTADOS — funciona en resultados.html
// Llama a la API y muestra los precios
// ================================================

// Buscamos la tabla donde van los resultados
var tablaPrecios = document.getElementById('tabla-precios');

// Si existe la tabla (o sea, si estamos en resultados.html)
if (tablaPrecios) {

    // Leemos el término buscado desde la URL
    var terminoBuscado = leerURL('q');

    // Mostramos el término en el título de la página
    var spanTermino = document.getElementById('termino-busqueda');
    if (spanTermino) {
        spanTermino.textContent = terminoBuscado;
    }

    // Mostramos el mensaje de carga mientras esperamos la API
    var mensajeCarga = document.getElementById('estado-carga');
    if (mensajeCarga) {
        mensajeCarga.hidden = false;
    }

    // Llamamos a la API de CheapShark con el término buscado
    // La API nos devuelve una lista de juegos con sus precios
    fetch('https://www.cheapshark.com/api/1.0/games?title=' + terminoBuscado + '&limit=5')

        // Convertimos la respuesta a formato JSON
        .then(function(respuesta) {
            return respuesta.json();
        })

        // Cuando tenemos los datos, los mostramos
        .then(function(juegos) {

            // Ocultamos el mensaje de carga
            if (mensajeCarga) {
                mensajeCarga.hidden = true;
            }

            // Si la API no devolvió ningún juego
            if (juegos.length == 0) {
                var sinResultados = document.getElementById('sin-resultados');
                if (sinResultados) {
                    sinResultados.hidden = false;
                }
                return;
            }

            // Vaciamos la tabla antes de llenarla
            // (borramos las filas de ejemplo que pusimos en el HTML)
            tablaPrecios.innerHTML = '';

            // Recorremos cada juego que devolvió la API
            for (var i = 0; i < juegos.length; i++) {

                var juego = juegos[i];

                // Creamos una nueva fila para este juego
                var fila = document.createElement('tr');

                // Si es el primer juego (el más barato), le ponemos la clase ganador
                if (i == 0) {
                    fila.classList.add('precio-ganador');
                }

                // Llenamos la fila con los datos del juego
                // cheapest es el precio más bajo que encontró la API
                fila.innerHTML =
                    '<td>' + juego.external + '</td>' +
                    '<td class="precio-actual">$' + juego.cheapest + '</td>' +
                    '<td>—</td>' +
                    '<td><a href="juego.html?id=' + juego.gameID + '">Ver precios</a></td>';

                // Agregamos la fila a la tabla
                tablaPrecios.appendChild(fila);
            }
        })

        // Si hubo un error (por ejemplo sin internet)
        .catch(function(error) {
            if (mensajeCarga) {
                mensajeCarga.hidden = true;
            }
            console.log('Error al llamar a la API:', error);
        });
}


// ================================================
// TABS — funciona en auth.html y perfil.html
// Muestra y oculta secciones al hacer click en los tabs
// ================================================

// --- TABS DE AUTH (Login / Registro) ---

var tabLogin    = document.getElementById('tab-login');
var tabRegistro = document.getElementById('tab-registro');
var seccionLogin    = document.getElementById('seccion-login');
var seccionRegistro = document.getElementById('seccion-registro');

// Si existen los tabs de auth (o sea, si estamos en auth.html)
if (tabLogin && tabRegistro) {

    // Cuando hacen click en "Iniciar sesión"
    tabLogin.addEventListener('click', function() {

        // Mostramos el login y ocultamos el registro
        seccionLogin.hidden    = false;
        seccionRegistro.hidden = true;

        // Marcamos el tab activo
        tabLogin.classList.add('activo');
        tabRegistro.classList.remove('activo');
    });

    // Cuando hacen click en "Crear cuenta"
    tabRegistro.addEventListener('click', function() {

        // Mostramos el registro y ocultamos el login
        seccionLogin.hidden    = true;
        seccionRegistro.hidden = false;

        // Marcamos el tab activo
        tabRegistro.classList.add('activo');
        tabLogin.classList.remove('activo');
    });

    // Los links de "Registrate acá" y "Iniciá sesión acá"
    // hacen lo mismo que los tabs
    var irRegistro = document.getElementById('ir-registro');
    var irLogin    = document.getElementById('ir-login');

    if (irRegistro) {
        irRegistro.addEventListener('click', function() {
            tabRegistro.click(); // Simulamos click en el tab de registro
        });
    }

    if (irLogin) {
        irLogin.addEventListener('click', function() {
            tabLogin.click(); // Simulamos click en el tab de login
        });
    }
}


// --- TABS DE PERFIL (Wishlist / Alertas / Historial) ---

var tabWishlist  = document.getElementById('tab-wishlist');
var tabAlertas   = document.getElementById('tab-alertas');
var tabHistorial = document.getElementById('tab-historial');

var seccionWishlist  = document.getElementById('seccion-wishlist');
var seccionAlertas   = document.getElementById('seccion-alertas');
var seccionHistorial = document.getElementById('seccion-historial');

// Si existen los tabs de perfil (o sea, si estamos en perfil.html)
if (tabWishlist && tabAlertas && tabHistorial) {

    // Función para cambiar de tab (así no repetimos código)
    function cambiarTab(seccionActiva, tabActivo) {

        // Ocultamos todas las secciones
        seccionWishlist.hidden  = true;
        seccionAlertas.hidden   = true;
        seccionHistorial.hidden = true;

        // Sacamos la clase activo de todos los tabs
        tabWishlist.classList.remove('activo');
        tabAlertas.classList.remove('activo');
        tabHistorial.classList.remove('activo');

        // Mostramos solo la sección que queremos
        seccionActiva.hidden = false;

        // Marcamos el tab activo
        tabActivo.classList.add('activo');
    }

    tabWishlist.addEventListener('click', function() {
        cambiarTab(seccionWishlist, tabWishlist);
    });

    tabAlertas.addEventListener('click', function() {
        cambiarTab(seccionAlertas, tabAlertas);
    });

    tabHistorial.addEventListener('click', function() {
        cambiarTab(seccionHistorial, tabHistorial);
    });
}


// ================================================
// REGISTRO — validación en auth.html
// Verificamos que las contraseñas coincidan
// antes de enviar el formulario al PHP
// ================================================

var formRegistro = document.querySelector('#seccion-registro form');

if (formRegistro) {

    formRegistro.addEventListener('submit', function(evento) {

        var password          = document.getElementById('registro-password').value;
        var passwordConfirmar = document.getElementById('registro-password-confirmar').value;
        var errorContrasena   = document.getElementById('registro-error');

        // Si las contraseñas no coinciden
        if (password != passwordConfirmar) {

            // Cancelamos el envío del formulario
            evento.preventDefault();

            // Mostramos el mensaje de error
            errorContrasena.hidden = false;

        } else {

            // Si coinciden, ocultamos el error (por si estaba visible)
            errorContrasena.hidden = true;

            // El formulario sigue su camino al PHP normalmente
        }
    });
}


// ================================================
// WISHLIST — botón de agregar/quitar
// Llama al PHP para guardar en la base de datos
// ================================================

var botonWishlist = document.getElementById('btn-wishlist');

if (botonWishlist) {

    botonWishlist.addEventListener('click', function() {

        // Leemos el ID del juego desde la URL
        var idJuego = leerURL('id');

        // Llamamos al PHP que guarda el juego en la wishlist
        fetch('php/wishlist.php?game_id=' + idJuego)
            .then(function(respuesta) {
                return respuesta.json();
            })
            .then(function(resultado) {

                // Si el PHP nos dice que se guardó bien
                if (resultado.ok) {
                    botonWishlist.textContent = '♥ Guardado en wishlist';
                } else {
                    // Si hubo un error (por ejemplo el usuario no está logueado)
                    alert(resultado.mensaje);
                }
            })
            .catch(function(error) {
                console.log('Error al guardar en wishlist:', error);
            });
    });
}