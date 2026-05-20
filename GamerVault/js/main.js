// ================================================
// GAMERVAULT - main.js
// ================================================


function leerURL(parametro) {
    var url = new URLSearchParams(window.location.search);
    return url.get(parametro);
}


// ================================================
// BUSCADOR — index.php y resultados.php
// ================================================

var inputBusqueda = document.getElementById('busqueda');

if (inputBusqueda) {
    inputBusqueda.addEventListener('keydown', function(evento) {
        if (evento.key === 'Enter') {
            var termino = inputBusqueda.value;
            if (termino == '') return;
            window.location.href = 'resultados.php?q=' + encodeURIComponent(termino);
        }
    });
}

var botonBuscar = document.querySelector('#busqueda-form button[type="submit"]');

if (botonBuscar) {
    botonBuscar.addEventListener('click', function(evento) {
        evento.preventDefault();
        var termino = inputBusqueda.value;
        if (termino == '') return;
        window.location.href = 'resultados.php?q=' + encodeURIComponent(termino);
    });
}


// ================================================
// RESULTADOS — resultados.php
// ================================================

var tablaPrecios = document.getElementById('tabla-precios');
var juegoActual = null

if (tablaPrecios) {

    var terminoBuscado = leerURL('q');

    var spanTermino = document.getElementById('termino-busqueda');
    if (spanTermino) spanTermino.textContent = terminoBuscado;

    var mensajeCarga = document.getElementById('estado-carga');
    if (mensajeCarga) mensajeCarga.hidden = false;

    // Las 4 tiendas que queremos mostrar
    var tiendas = {
    '1':  'Steam',
    '7':  'GOG',
    '11': 'Humble Store',
    '14': 'Green Man Gaming',
    '21': 'GamersGate',
    '25': 'Epic Games Store'
};

    var tiendasPermitidas = ['1', '7', '11', '14', '21', '25'];

    // ------------------------------------------------
    // PASO 1: Buscar el juego por nombre
    // Pedimos 10 resultados para encontrar el mejor
    // ------------------------------------------------
    fetch('https://www.cheapshark.com/api/1.0/games?title=' + encodeURIComponent(terminoBuscado) + '&limit=10')
        .then(function(r) { return r.json(); })
        .then(function(juegos) {

            if (juegos.length == 0) {
                if (mensajeCarga) mensajeCarga.hidden = true;
                document.getElementById('sin-resultados').hidden = false;
                return;
            }

            // ------------------------------------------------
            // PASO 2: Encontrar el juego que mejor coincida
            // con lo que escribió el usuario
            // ------------------------------------------------
            var terminoMinusculas = terminoBuscado.toLowerCase();
            var juegoEncontrado = null;

            for (var i = 0; i < juegos.length; i++) {
                var tituloAPI = juegos[i].external.toLowerCase();

                // Coincidencia exacta → lo usamos directamente
                if (tituloAPI === terminoMinusculas) {
                    juegoEncontrado = juegos[i];
                    break;
                }

                // El título empieza con lo que buscó → buen candidato
                if (tituloAPI.startsWith(terminoMinusculas) && !juegoEncontrado) {
                    juegoEncontrado = juegos[i];
                }
            }

            // Si no hubo ninguna coincidencia, usamos el primero
            if (!juegoEncontrado) {
                juegoEncontrado = juegos[0];
            }

            // Mostramos el nombre real del juego
            if (spanTermino) spanTermino.textContent = juegoEncontrado.external;
            juegoActual = juegoEncontrado;

            fetch('php/historial.php', {
                 method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'game_nombre=' + encodeURIComponent(juegoEncontrado.external)
            });

            // ------------------------------------------------
            // PASO 3: Pedir los precios ACTUALES
            // /games?id= devuelve UN precio por tienda, el de hoy
            // No históricos, no duplicados — precio actual y punto
            // ------------------------------------------------
            return fetch('https://www.cheapshark.com/api/1.0/games?id=' + juegoEncontrado.gameID)
                .then(function(r) { return r.json(); })
                .then(function(detalle) {

                    if (mensajeCarga) mensajeCarga.hidden = true;
                    tablaPrecios.innerHTML = '';

                    // La respuesta tiene esta forma:
                    // { info: { title, thumb }, deals: [ {storeID, price, retailPrice, dealID} ] }
                    var deals = detalle.deals;

                    if (!deals || deals.length == 0) {
                        document.getElementById('sin-resultados').hidden = false;
                        return;
                    }

                    // ------------------------------------------------
                    // PASO 4: Filtrar solo Steam, GOG, Epic, Microsoft
                    // ------------------------------------------------
                    deals = deals.filter(function(deal) {
                        return tiendasPermitidas.includes(deal.storeID);
                    });

                    if (deals.length == 0) {
                        document.getElementById('sin-resultados').hidden = false;
                        return;
                    }

                    // Ordenamos de menor a mayor precio
                    deals.sort(function(a, b) {
                        return parseFloat(a.price) - parseFloat(b.price);
                    });

                    // ------------------------------------------------
                    // PASO 5: Mostrar una fila por tienda
                    // ------------------------------------------------
                    for (var i = 0; i < deals.length; i++) {
                        var deal = deals[i];
                        var fila = document.createElement('tr');

                        if (i == 0) fila.classList.add('precio-ganador');

                        var nombreTienda   = tiendas[deal.storeID] || 'Otra tienda';
                        var precioActual   = parseFloat(deal.price).toFixed(2);
                        var precioOriginal = parseFloat(deal.retailPrice).toFixed(2);

                        // Calculamos el % de descuento
                        var descuento = 0;
                        if (precioOriginal > 0 && precioActual < precioOriginal) {
                            descuento = Math.round((1 - precioActual / precioOriginal) * 100);
                        }

                        var linkCompra = 'https://www.cheapshark.com/redirect?dealID=' + deal.dealID;

                        fila.innerHTML =
                            '<td>' + (i == 0 ? '🥇 ' : '') + nombreTienda + '</td>' +
                            '<td>' +
                                (descuento > 0
                                    ? '<span class="precio-original">$' + precioOriginal + '</span> '
                                    : ''
                                ) +
                                '<span class="precio-actual">$' + precioActual + '</span>' +
                            '</td>' +
                            '<td class="descuento">' +
                                (descuento > 0 ? '-' + descuento + '%' : 'Precio normal') +
                            '</td>' +
                            '<td><a href="' + linkCompra + '" target="_blank" rel="noopener">Ir a la tienda</a></td>';

                        tablaPrecios.appendChild(fila);
                    }

                    // Link a la ficha completa del juego
                    var linkDetalle = document.getElementById('link-detalle');
                    if (linkDetalle) {
                        linkDetalle.href = 'juego.php?id=' + juegoEncontrado.gameID;
                        linkDetalle.hidden = false;
                    }
                });
        })
        .catch(function(error) {
            if (mensajeCarga) mensajeCarga.hidden = true;
            console.log('Error con la API:', error);
        });
}


// ================================================
// TABS — auth.html
// ================================================

var tabLogin        = document.getElementById('tab-login');
var tabRegistro     = document.getElementById('tab-registro');
var seccionLogin    = document.getElementById('seccion-login');
var seccionRegistro = document.getElementById('seccion-registro');

if (tabLogin && tabRegistro) {

    tabLogin.addEventListener('click', function() {
        seccionLogin.hidden    = false;
        seccionRegistro.hidden = true;
        tabLogin.classList.add('activo');
        tabRegistro.classList.remove('activo');
    });

    tabRegistro.addEventListener('click', function() {
        seccionLogin.hidden    = true;
        seccionRegistro.hidden = false;
        tabRegistro.classList.add('activo');
        tabLogin.classList.remove('activo');
    });

    var irRegistro = document.getElementById('ir-registro');
    var irLogin    = document.getElementById('ir-login');

    if (irRegistro) {
        irRegistro.addEventListener('click', function() { tabRegistro.click(); });
    }
    if (irLogin) {
        irLogin.addEventListener('click', function() { tabLogin.click(); });
    }

    if (window.location.hash === '#seccion-registro') {
        tabRegistro.click();
    }
}


// ================================================
// TABS — perfil.php
// ================================================

var tabWishlist  = document.getElementById('tab-wishlist');
var tabAlertas   = document.getElementById('tab-alertas');
var tabHistorial = document.getElementById('tab-historial');

var seccionWishlist  = document.getElementById('seccion-wishlist');
var seccionAlertas   = document.getElementById('seccion-alertas');
var seccionHistorial = document.getElementById('seccion-historial');

if (tabWishlist && tabAlertas && tabHistorial) {

    function cambiarTab(seccionActiva, tabActivo) {
        seccionWishlist.hidden  = true;
        seccionAlertas.hidden   = true;
        seccionHistorial.hidden = true;
        tabWishlist.classList.remove('activo');
        tabAlertas.classList.remove('activo');
        tabHistorial.classList.remove('activo');
        seccionActiva.hidden = false;
        tabActivo.classList.add('activo');
    }

    tabWishlist.addEventListener('click', function() { cambiarTab(seccionWishlist, tabWishlist); });
    tabAlertas.addEventListener('click', function() { cambiarTab(seccionAlertas, tabAlertas); });
    tabHistorial.addEventListener('click', function() { cambiarTab(seccionHistorial, tabHistorial); });
}


// ================================================
// REGISTRO — validación de contraseñas
// ================================================

var formRegistro = document.querySelector('#seccion-registro form');

if (formRegistro) {
    formRegistro.addEventListener('submit', function(evento) {
        var password          = document.getElementById('registro-password').value;
        var passwordConfirmar = document.getElementById('registro-password-confirmar').value;
        var errorContrasena   = document.getElementById('registro-error');

        if (password != passwordConfirmar) {
            evento.preventDefault();
            errorContrasena.hidden = false;
        } else {
            errorContrasena.hidden = true;
        }
    });
}


// ================================================
// WISHLIST — botón agregar
// ================================================

var botonWishlist = document.getElementById('btn-wishlist');

if (botonWishlist) {
    botonWishlist.addEventListener('click', function() {

        if (!juegoActual) {
            alert('Primero buscá un juego');
            return;
        }

        fetch('php/wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'game_id=' + encodeURIComponent(juegoActual.gameID) +
                  '&game_nombre=' + encodeURIComponent(juegoActual.external)
        })
        .then(function(r) { return r.json(); })
        .then(function(resultado) {
            if (resultado.ok) {
                botonWishlist.textContent = '♥ Guardado en wishlist';
                botonWishlist.disabled = true;
            } else {
                alert(resultado.mensaje);
            }
        })
        .catch(function(error) {
            console.log('Error wishlist:', error);
        });
    });
}


// CARGAR WISHLIST EN PERFIL
var listaWishlist = document.getElementById('lista-wishlist');

if (listaWishlist) {
    fetch('php/obtener_wishlist.php')
        .then(function(r) { return r.json(); })
        .then(function(juegos) {
            if (juegos.length == 0) {
                document.getElementById('wishlist-vacia').hidden = false;
                listaWishlist.hidden = true;
                return;
            }
            listaWishlist.innerHTML = '';
            for (var i = 0; i < juegos.length; i++) {
                var j = juegos[i];
                var li = document.createElement('li');
                li.className = 'juego-card';
                li.innerHTML =
                    '<img src="https://www.cheapshark.com/img/games/capsules/' + j.game_id + '.jpg" alt="' + j.game_nombre + '" />' +
                    '<div class="juego-info">' +
                        '<h3><a href="juego.php?id=' + j.game_id + '">' + j.game_nombre + '</a></h3>' +
                        '<p class="fecha-agregado">Agregado el ' + new Date(j.fecha_agregado).toLocaleDateString('es-AR') + '</p>' +
                    '</div>' +
                    '<div class="juego-acciones">' +
                        '<a href="juego.php?id=' + j.game_id + '">Ver precios</a>' +
                    '</div>';
                listaWishlist.appendChild(li);
            }
        });
}

// ================================================
// CARGAR WISHLIST EN PERFIL
// ================================================
var listaWishlist = document.getElementById('lista-wishlist');

if (listaWishlist) {
    fetch('php/obtener_wishlist.php')
        .then(function(r) { return r.json(); })
        .then(function(juegos) {

            if (juegos.length == 0) {
                document.getElementById('wishlist-vacia').hidden = false;
                listaWishlist.hidden = true;
                return;
            }

            listaWishlist.innerHTML = '';

            for (var i = 0; i < juegos.length; i++) {
                var j = juegos[i];
                var li = document.createElement('li');
                li.className = 'juego-card';
                li.innerHTML =
                    '<img src="https://www.cheapshark.com/img/games/capsules/' + j.game_id + '.jpg" alt="' + j.game_nombre + '" />' +
                    '<div class="juego-info">' +
                        '<h3><a href="juego.php?id=' + j.game_id + '">' + j.game_nombre + '</a></h3>' +
                        '<p class="fecha-agregado">Agregado el ' + new Date(j.fecha_agregado).toLocaleDateString('es-AR') + '</p>' +
                    '</div>' +
                    '<div class="juego-acciones">' +
                        '<a href="juego.php?id=' + j.game_id + '">Ver precios</a>' +
                        '<button type="button" class="btn-quitar-wishlist" data-id="' + j.game_id + '">Quitar de wishlist</button>' +
                    '</div>';
                listaWishlist.appendChild(li);
            }

            // Botones de quitar wishlist
            var botonesQuitar = document.querySelectorAll('.btn-quitar-wishlist');
            botonesQuitar.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var gameId = this.getAttribute('data-id');
                    fetch('php/quitar_wishlist.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'game_id=' + gameId
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(resultado) {
                        if (resultado.ok) {
                            location.reload();
                        } else {
                            alert(resultado.mensaje);
                        }
                    });
                });
            });
        });
}


// ================================================
// CARGAR HISTORIAL EN PERFIL
// ================================================
var listaHistorial = document.getElementById('lista-historial');

if (listaHistorial) {
    fetch('php/obtener_historial.php')
        .then(function(r) { return r.json(); })
        .then(function(busquedas) {

            if (busquedas.length == 0) {
                document.getElementById('historial-vacio').hidden = false;
                listaHistorial.hidden = true;
                return;
            }

            listaHistorial.innerHTML = '';

            for (var i = 0; i < busquedas.length; i++) {
                var b = busquedas[i];
                var fecha = new Date(b.fecha_busqueda).toLocaleDateString('es-AR');
                var li = document.createElement('li');
                li.className = 'historial-item';
                li.innerHTML =
                    '<a href="resultados.php?q=' + encodeURIComponent(b.game_nombre) + '">' + b.game_nombre + '</a>' +
                    '<time>' + fecha + '</time>';
                listaHistorial.appendChild(li);
            }
        });
}