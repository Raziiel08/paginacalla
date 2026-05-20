<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detalle del juego — GamerVault</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Rajdhani:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css" />
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
    </nav>
  </header>

  <!-- ===== CONTENIDO PRINCIPAL ===== -->
  <main>

    <!-- BREADCRUMB: para que el usuario sepa dónde está -->
    <nav aria-label="Ruta de navegación">
      <ol>
        <li><a href="index.php">Inicio</a></li>
        <li><a href="resultados.php" id="link-volver-resultados">Resultados</a></li>
        <li aria-current="page" id="breadcrumb-nombre">ARK: Survival Evolved</li>
      </ol>
    </nav>

    <!-- FICHA DEL JUEGO -->
    <article class="ficha-juego" id="ficha-juego">

      <!-- CABECERA: imagen + info principal -->
      <section class="ficha-cabecera">
        <img
          src="assets/img/placeholder.jpg"
          alt="Portada de ARK: Survival Evolved"
          id="juego-portada"
        />

        <div class="ficha-info-principal">
          <h1 id="juego-nombre">ARK: Survival Evolved</h1>

          <ul class="juego-meta">
            <li>Desarrollador: <span id="juego-desarrollador">Studio Wildcard</span></li>
            <li>Géneros: <span id="juego-generos">Supervivencia, Acción, Multijugador</span></li>
            <li>Fecha de lanzamiento: <span id="juego-fecha">2017</span></li>
          </ul>

          <!-- Precio más bajo actual -->
          <div class="precio-destacado">
            <p>Mejor precio ahora:</p>
            <strong id="juego-precio-min">$250</strong>
            <span id="juego-descuento-max">-75%</span>
            <p>en <span id="juego-mejor-tienda">Xbox / Microsoft Store</span></p>
            <a href="#" id="juego-link-compra" target="_blank" rel="noopener">
              Ir a la tienda →
            </a>
          </div>

          <!-- Acciones del usuario (requieren login) -->
          <div class="ficha-acciones">
            <button type="button" id="btn-wishlist">♥ Agregar a wishlist</button>
            <button type="button" id="btn-alerta">🔔 Crear alerta de precio</button>
          </div>
        </div>
      </section>

      <!-- DESCRIPCIÓN -->
      <section class="ficha-descripcion">
        <h2>Descripción</h2>
        <!--
          Este párrafo se llena dinámicamente desde la API.
          id="juego-descripcion"
        -->
        <p id="juego-descripcion">
          Cargando descripción...
        </p>
      </section>

      <!-- COMPARATIVA DE PRECIOS POR TIENDA -->
      <section class="ficha-comparativa">
        <h2>Comparativa de precios</h2>

        <!--
          Misma estructura que resultados.html.
          Se llena dinámicamente con la API.
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
          <tbody id="tabla-precios-detalle">

            <tr class="precio-ganador">
              <td>🥇 Xbox / Microsoft Store</td>
              <td class="precio-actual">$250</td>
              <td class="descuento">-75%</td>
              <td><a href="#" target="_blank" rel="noopener">Ir a la tienda</a></td>
            </tr>

            <tr>
              <td>Steam</td>
              <td class="precio-actual">$2.500</td>
              <td class="descuento">-50%</td>
              <td><a href="#" target="_blank" rel="noopener">Ir a la tienda</a></td>
            </tr>

            <tr>
              <td>Epic Games Store</td>
              <td class="precio-actual">$3.000</td>
              <td class="descuento">-40%</td>
              <td><a href="#" target="_blank" rel="noopener">Ir a la tienda</a></td>
            </tr>

            <tr>
              <td>GOG</td>
              <td class="precio-actual">$3.200</td>
              <td class="descuento">-38%</td>
              <td><a href="#" target="_blank" rel="noopener">Ir a la tienda</a></td>
            </tr>

          </tbody>
        </table>
      </section>

      <!-- HISTÓRICO DE PRECIOS -->
      <section class="ficha-historico">
        <h2>Histórico de precios</h2>

        <ul class="historico-stats">
          <li>Precio más bajo histórico: <strong id="historico-min">$99</strong></li>
          <li>Precio más alto registrado: <strong id="historico-max">$4.200</strong></li>
          <li>Precio actual vs. mínimo histórico: <strong id="historico-vs-min">+150%</strong></li>
        </ul>

        <div id="grafico-historico-container">
          <p>[Gráfico de evolución de precios — se implementa con Chart.js]</p>
        </div>
      </section>

    </article>

    <!-- Estado de carga -->
    <p id="estado-carga" hidden>Cargando información del juego...</p>

    <!-- Mensaje de error -->
    <p id="error-juego" hidden>No se pudo cargar la información del juego.</p>

  </main>

  <!-- ===== PIE DE PÁGINA ===== -->
  <footer>
    <p>© 2026 GamerVault — Grupo 7.2</p>
    <p>Datos provistos por <a href="https://www.cheapshark.com" target="_blank" rel="noopener">CheapShark API</a></p>
  </footer>

  <script src="js/main.js"></script>

</body>
</html>