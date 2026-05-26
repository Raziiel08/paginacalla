<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resultados  GamerVault</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css?v=<?php echo time(); ?>" />
</head>

<body>

  <!-- ===== NAVEGACIÓN ===== -->
  <header>
    <nav>
      <div class="logo">
        <a href="index.php">🎮 GamerVault</a>
      </div>

      <ul>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="resultados.php">Buscar</a></li>
        <li><a href="perfil.php">Mi Lista</a></li>
        <li><a href="perfil.php">Alertas</a></li>
      </ul>

      <div class="nav-auth">
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <span>Hola, <?php echo $_SESSION['usuario_nombre']; ?></span>
          <a href="php/logout.php">Cerrar sesión</a>
        <?php else: ?>
          <a href="auth.php">Login</a>
          <a href="auth.php#seccion-registro">Registro</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <!-- ===== CONTENIDO PRINCIPAL ===== -->
  <main>

    <!-- BUSCADOR (se mantiene visible en esta página) -->
    <section class="buscador">
      <form id="busqueda-form" action="resultados.php" method="GET" style="position: relative;">
        <label for="busqueda">Buscá tu juego:</label>
        <input
          type="search"
          id="busqueda"
          name="q"
          placeholder="Escribí el nombre del juego..."
          required
          autocomplete="off"
        />
        <button type="submit">Buscar</button>
        <ul id="busqueda-sugerencias" class="sugerencias-lista" hidden></ul>
      </form>
    </section>

    <!-- ENCABEZADO DE RESULTADOS -->
    <section class="resultados">

      <!--
        El título va a mostrar el término buscado.
        Con JS lo completás así:
          const params = new URLSearchParams(window.location.search);
          document.getElementById('termino-busqueda').textContent = params.get('q');
      -->
      <h1>Resultados para: <span id="termino-busqueda"></span></h1>

      <!-- Botón wishlist (visible solo si el usuario está logueado) -->
      <button type="button" class="btn-wishlist" id="btn-wishlist">
        ♥ Agregar a wishlist
      </button>

      <!-- TABLA DE PRECIOS POR TIENDA -->
      <!--
        Esta tabla va a ser generada dinámicamente desde la CheapShark API.
        La primera fila (🥇) es siempre el precio más bajo.

      -->
      <table>
        <thead>
          <tr>
            <th scope="col">Tienda</th>
            <th scope="col">Precio</th>
            <th scope="col">Descuento</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody id="tabla-precios">
        </tbody>
      </table>

      <!-- Estado de carga (se muestra mientras llega la respuesta de la API) -->
      <p id="estado-carga" hidden>Cargando precios...</p>

      <!-- Mensaje si no hay resultados -->
      <p id="sin-resultados" hidden>No encontramos resultados para tu búsqueda.</p>

      <!-- Link a la ficha completa del juego -->
      <a href="#" id="link-detalle" hidden>Ver ficha completa del juego →</a>

    </section>

  </main>

  <!-- ===== PIE DE PÁGINA ===== -->
  <footer>
    <p>© 2026 GamerVault — Grupo 7.2</p>
    <p>Datos provistos por <a href="https://www.cheapshark.com" target="_blank" rel="noopener">CheapShark API</a></p>
  </footer>

  <script src="js/main.js?v=<?php echo time(); ?>"></script>

</body>
</html>