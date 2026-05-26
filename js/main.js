// ================================================
// GAMERVAULT - main.js
// ================================================


function leerURL(parametro) {
    var url = new URLSearchParams(window.location.search);
    return url.get(parametro);
}


// ================================================
// TOAST NOTIFICACIONES
// ================================================
function showToast(mensaje, tipo = 'success') {
    var container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
    
    var toast = document.createElement('div');
    toast.className = 'toast toast-' + tipo;
    toast.innerHTML = (tipo === 'success' ? '✅ ' : '❌ ') + mensaje;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ================================================
// BUSCADOR — index.php y resultados.php
// ================================================

var inputBusqueda = document.getElementById('busqueda');
var sugerencias = document.getElementById('busqueda-sugerencias');
var timeoutIdBusqueda;

if (inputBusqueda) {
    // Autocompletado en tiempo real
    if (sugerencias) {
        inputBusqueda.addEventListener('input', function(e) {
            clearTimeout(timeoutIdBusqueda);
            var q = e.target.value.trim();
            if (q.length < 3) {
                sugerencias.hidden = true;
                return;
            }
            timeoutIdBusqueda = setTimeout(() => {
                fetch('https://www.cheapshark.com/api/1.0/games?title=' + encodeURIComponent(q) + '&limit=5')
                    .then(r => r.json())
                    .then(juegos => {
                        if (juegos.length === 0) {
                            sugerencias.hidden = true;
                            return;
                        }
                        sugerencias.innerHTML = '';
                        juegos.forEach(j => {
                            var li = document.createElement('li');
                            li.innerHTML = `
                                <a href="juego.php?id=${j.gameID}">
                                    <img src="${j.thumb}" alt="${j.external}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">
                                    <span>${j.external} - <strong>$${j.cheapest}</strong></span>
                                </a>
                            `;
                            sugerencias.appendChild(li);
                        });
                        sugerencias.hidden = false;
                    })
                    .catch(() => sugerencias.hidden = true);
            }, 300);
        });

        // Cerrar sugerencias al hacer clic afuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#busqueda-form')) {
                sugerencias.hidden = true;
            }
        });
    }

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

            fetch('php/historial/historial.php', {
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

                    // Encontrar el precio de venta original (MSRP) más alto entre todas las tiendas del juego.
                    var maxRetailPrice = 0;
                    for (var d = 0; d < deals.length; d++) {
                        var rPrice = parseFloat(deals[d].retailPrice);
                        if (rPrice > maxRetailPrice) {
                            maxRetailPrice = rPrice;
                        }
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
                        
                        // Si una tienda reporta un precio original menor al máximo precio de lista del juego (MSRP),
                        // asumimos el precio máximo detectado como el precio de venta original oficial.
                        var originalVal = parseFloat(deal.retailPrice);
                        if (originalVal < maxRetailPrice) {
                            originalVal = maxRetailPrice;
                        }
                        var precioOriginal = originalVal.toFixed(2);

                        // Calculamos el % de descuento
                        var descuento = 0;
                        if (precioOriginal > 0 && parseFloat(deal.price) < originalVal) {
                            descuento = Math.round((1 - parseFloat(deal.price) / originalVal) * 100);
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


// DETALLE DEL JUEGO — juego.php
// ================================================
var fichaJuego = document.getElementById('ficha-juego');
if (fichaJuego) {
    var gameId = leerURL('id');
    var mensajeCarga = document.getElementById('estado-carga');
    var mensajeError = document.getElementById('error-juego');

    if (!gameId) {
        if (mensajeError) {
            mensajeError.textContent = 'No se proporcionó un ID de juego válido.';
            mensajeError.hidden = false;
        }
        fichaJuego.hidden = true;
    } else {
        if (mensajeCarga) mensajeCarga.hidden = false;
        fichaJuego.hidden = true;

        var tiendas = {
            '1':  'Steam',
            '7':  'GOG',
            '11': 'Humble Store',
            '14': 'Green Man Gaming',
            '21': 'GamersGate',
            '25': 'Epic Games Store'
        };
        var tiendasPermitidas = ['1', '7', '11', '14', '21', '25'];

        fetch('https://www.cheapshark.com/api/1.0/games?id=' + gameId)
            .then(function(r) { return r.json(); })
            .then(function(detalle) {
                if (mensajeCarga) mensajeCarga.hidden = true;

                if (!detalle || !detalle.info) {
                    if (mensajeError) mensajeError.hidden = false;
                    return;
                }

                // Guardar como juegoActual para que los botones de Wishlist y Alertas tengan acceso
                juegoActual = {
                    gameID: gameId,
                    external: detalle.info.title
                };

                fichaJuego.hidden = false;

                // 1. Datos básicos
                document.getElementById('breadcrumb-nombre').textContent = detalle.info.title;
                document.getElementById('juego-nombre').textContent = detalle.info.title;
                
                var portada = document.getElementById('juego-portada');
                if (portada) {
                    portada.src = detalle.info.thumb || 'assets/img/placeholder.jpg';
                    portada.alt = 'Portada de ' + detalle.info.title;
                }

                // Datos adicionales estéticos
                document.getElementById('juego-desarrollador').textContent = 'Multiplataforma';
                document.getElementById('juego-generos').textContent = 'Videojuego';
                document.getElementById('juego-fecha').textContent = 'N/D';
                document.getElementById('juego-descripcion').textContent = 
                    'Seguí el precio de ' + detalle.info.title + ' en tiempo real. Te enviaremos una alerta de correo en cuanto detectemos una oferta en Steam, GOG, Epic Games u otras tiendas asociadas que coincida con tu precio objetivo.';

                // 2. Procesar ofertas
                var deals = detalle.deals || [];
                deals = deals.filter(function(deal) {
                    return tiendasPermitidas.includes(deal.storeID);
                });

                var tablaPreciosDetalle = document.getElementById('tabla-precios-detalle');
                if (tablaPreciosDetalle) {
                    tablaPreciosDetalle.innerHTML = '';
                    
                    if (deals.length == 0) {
                        tablaPreciosDetalle.innerHTML = '<tr><td colspan="4" style="text-align: center;">No hay ofertas disponibles en este momento.</td></tr>';
                    } else {
                        // Ordenar por precio
                        deals.sort(function(a, b) {
                            return parseFloat(a.price) - parseFloat(b.price);
                        });

                        // Calcular MSRP (Original) máximo para corregir el bug de descuentos
                        var maxRetail = 0;
                        deals.forEach(function(d) {
                            var rPrice = parseFloat(d.retailPrice);
                            if (rPrice > maxRetail) maxRetail = rPrice;
                        });

                        // Renderizar filas
                        deals.forEach(function(deal, index) {
                            var fila = document.createElement('tr');
                            if (index == 0) fila.className = 'precio-ganador';

                            var nombreTienda   = tiendas[deal.storeID] || 'Otra tienda';
                            var precioActual   = parseFloat(deal.price).toFixed(2);
                            var precioOriginal = parseFloat(deal.retailPrice);

                            // Aplicar MSRP máximo si el deal tiene un retailPrice igual o menor al salePrice (Steam bug)
                            if (precioOriginal < maxRetail) {
                                precioOriginal = maxRetail;
                            }

                            var descuento = 0;
                            if (precioOriginal > 0 && parseFloat(deal.price) < precioOriginal) {
                                descuento = Math.round((1 - parseFloat(deal.price) / precioOriginal) * 100);
                            }

                            var linkCompra = 'https://www.cheapshark.com/redirect?dealID=' + deal.dealID;

                            fila.innerHTML =
                                '<td>' + (index == 0 ? '🥇 ' : '') + nombreTienda + '</td>' +
                                '<td>' +
                                    (descuento > 0
                                        ? '<span class="precio-original">$' + precioOriginal.toFixed(2) + '</span> '
                                        : ''
                                    ) +
                                    '<span class="precio-actual">$' + precioActual + '</span>' +
                                '</td>' +
                                '<td class="descuento">' +
                                    (descuento > 0 ? '-' + descuento + '%' : 'Precio normal') +
                                '</td>' +
                                '<td><a href="' + linkCompra + '" target="_blank" rel="noopener">Ir a la tienda</a></td>';

                            tablaPreciosDetalle.appendChild(fila);
                        });

                        // Rellenar mejor precio en el banner de arriba
                        var mejorDeal = deals[0];
                        var precioMin = parseFloat(mejorDeal.price).toFixed(2);
                        var precioOriginalMin = parseFloat(mejorDeal.retailPrice);
                        if (precioOriginalMin < maxRetail) precioOriginalMin = maxRetail;
                        
                        var descMax = 0;
                        if (precioOriginalMin > 0 && parseFloat(mejorDeal.price) < precioOriginalMin) {
                            descMax = Math.round((1 - parseFloat(mejorDeal.price) / precioOriginalMin) * 100);
                        }

                        document.getElementById('juego-precio-min').textContent = '$' + precioMin;
                        var descSpan = document.getElementById('juego-descuento-max');
                        if (descSpan) {
                            descSpan.textContent = descMax > 0 ? '-' + descMax + '%' : 'Precio Normal';
                        }
                        document.getElementById('juego-mejor-tienda').textContent = tiendas[mejorDeal.storeID] || 'Tienda';
                        
                        var linkCompraBanner = document.getElementById('juego-link-compra');
                        if (linkCompraBanner) {
                            linkCompraBanner.href = 'https://www.cheapshark.com/redirect?dealID=' + mejorDeal.dealID;
                        }
                    }
                }

                // 3. Rellenar histórico
                var histMin = parseFloat(detalle.cheapestPriceEver.price).toFixed(2);
                document.getElementById('historico-min').textContent = '$' + histMin;
                var histMax = maxRetail > 0 ? maxRetail.toFixed(2) : (parseFloat(histMin) * 1.5).toFixed(2);
                document.getElementById('historico-max').textContent = '$' + histMax;
                
                var vsMin = 0;
                if (parseFloat(histMin) > 0) {
                    vsMin = Math.round(((parseFloat(deals[0] ? deals[0].price : histMin) - parseFloat(histMin)) / parseFloat(histMin)) * 100);
                }
                document.getElementById('historico-vs-min').textContent = vsMin > 0 ? '+' + vsMin + '%' : 'Mínimo Histórico';

                
                // GRÁFICO CON CHART.JS
                var ctx = document.getElementById('grafico-historico');
                if (ctx && window.Chart) {
                    var precioActualNumber = deals[0] ? parseFloat(deals[0].price) : parseFloat(histMin);
                    var minNumber = parseFloat(histMin);
                    var maxNumber = parseFloat(histMax);

                    // Formatear fechas para los labels (podemos aproximarlas o usar etiquetas genéricas)
                    var fechaMinima = new Date(detalle.cheapestPriceEver.date * 1000).toLocaleDateString('es-AR');
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Lanzamiento', 'Mínimo (' + fechaMinima + ')', 'Hoy'],
                            datasets: [{
                                label: 'Evolución de Precio (USD)',
                                data: [maxNumber, minNumber, precioActualNumber],
                                borderColor: '#10b981', 
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                borderWidth: 3,
                                pointBackgroundColor: '#ffffff',
                                pointBorderColor: '#10b981',
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#e5e7eb'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: '#e5e7eb',
                                        callback: function(value) {
                                            return '$' + value;
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    },
                                    ticks: {
                                        color: '#e5e7eb'
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(function(err) {
                if (mensajeCarga) mensajeCarga.hidden = true;
                if (mensajeError) mensajeError.hidden = false;
                console.error('Error cargando ficha:', err);
            });
    }
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
            showToast('Primero buscá un juego', 'error');
            return;
        }

        fetch('php/wishlist/wishlist.php', {
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
                showToast('Juego guardado en tu wishlist', 'success');
            } else {
                showToast(resultado.mensaje, 'error');
            }
        })
        .catch(function(error) {
            console.log('Error wishlist:', error);
        });
    });
}


// ================================================
// CARGAR WISHLIST EN PERFIL
// ================================================
var listaWishlist = document.getElementById('lista-wishlist');

if (listaWishlist) {
    fetch('php/wishlist/obtener_wishlist.php')
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
                    '<img id="wishlist-img-' + j.game_id + '" src="assets/img/placeholder.jpg" alt="' + j.game_nombre + '" />' +
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

            // Fetch thumbs from API
            var ids = juegos.map(function(j) { return j.game_id; }).join(',');
            if (ids !== '') {
                fetch('https://www.cheapshark.com/api/1.0/games?ids=' + ids)
                    .then(function(r) { return r.json(); })
                    .then(function(detalles) {
                        for (var i = 0; i < juegos.length; i++) {
                            var gid = juegos[i].game_id;
                            if (detalles[gid] && detalles[gid].info && detalles[gid].info.thumb) {
                                var imgElement = document.getElementById('wishlist-img-' + gid);
                                if (imgElement) {
                                    imgElement.src = detalles[gid].info.thumb;
                                }
                            }
                        }
                    })
                    .catch(function(e) { console.error('Error fetching thumbs:', e); });
            }

            // Botones de quitar wishlist
            var botonesQuitar = document.querySelectorAll('.btn-quitar-wishlist');
            botonesQuitar.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var gameId = this.getAttribute('data-id');
                    fetch('php/wishlist/quitar_wishlist.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'game_id=' + gameId
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(resultado) {
                        if (resultado.ok) {
                            showToast('Juego quitado de la wishlist', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(resultado.mensaje, 'error');
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
    fetch('php/historial/obtener_historial.php')
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

var btnLimpiarHistorial = document.getElementById('btn-limpiar-historial');

if (btnLimpiarHistorial) {
    btnLimpiarHistorial.addEventListener('click', function() {
        fetch('php/historial/obtener_historial.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'accion=limpiar'
        })
        .then(function(r) { return r.json(); })
        .then(function(resultado) {
            if (resultado.ok) location.reload();
        });
    });
}