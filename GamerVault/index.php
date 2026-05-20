<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GamerVault Comparador de precios</title>

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
      </div>
    </nav>
  </header>

  <!-- ===== CONTENIDO PRINCIPAL ===== -->
  <main>

    <!-- HERO: Buscador principal -->
    <section class="hero">
      <h1>Encontrá el precio más bajo de cualquier videojuego</h1>
      <p>Comparamos Steam, Epic Games, Xbox, GOG, Fanatical y más — en un solo lugar.</p>

      <form id="busqueda-form" action="resultados.php" method="GET">
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
      </form>

      <!-- Categorías rápidas -->
      <nav aria-label="Categorías rápidas">
        <ul>
          <li><a href="resultados.php?categoria=ofertas">🔥 Ofertas</a></li>
          <li><a href="resultados.php?categoria=accion">Acción</a></li>
          <li><a href="resultados.php?categoria=rpg">RPG</a></li>
          <li><a href="resultados.php?categoria=indie">Indie</a></li>
          <li><a href="resultados.php?categoria=multijugador">Multijugador</a></li>
          <li><a href="resultados.php?categoria=estrategia">Estrategia</a></li>
        </ul>
      </nav>
    </section>

    <!-- OFERTAS DESTACADAS -->
    <section class="ofertas-destacadas">
      <h2>🔥 Ofertas destacadas hoy</h2>

      <!--
        Esta lista va a ser generada dinámicamente con JS
        cuando conectes la CheapShark API.
        Por ahora está hardcodeada para ver la estructura.
      -->
      <ul class="lista-juegos" id="lista-ofertas">

        <li class="juego-card">
          <a href="juego.html?id=1">
            <img src="assets/img/placeholder.jpg" alt="ARK: Survival Evolved" />
            <div class="juego-info">
              <h3>ARK: Survival Evolved</h3>
              <p class="precio-actual">$250</p>
              <p class="precio-original">$1.000</p>
              <span class="descuento">-75%</span>
              <p class="mejor-tienda">Mejor precio: Xbox Store</p>
            </div>
          </a>
        </li>

        <li class="juego-card">
          <a href="juego.html?id=2">
            <img src="assets/img/placeholder.jpg" alt="Elden Ring" />
            <div class="juego-info">
              <h3>Elden Ring</h3>
              <p class="precio-actual">$1.400</p>
              <p class="precio-original">$3.500</p>
              <span class="descuento">-60%</span>
              <p class="mejor-tienda">Mejor precio: Steam</p>
            </div>
          </a>
        </li>

        <li class="juego-card">
          <a href="juego.html?id=3">
            <img src="assets/img/placeholder.jpg" alt="No Man's Sky" />
            <div class="juego-info">
              <h3>No Man's Sky</h3>
              <p class="precio-actual">$900</p>
              <p class="precio-original">$1.800</p>
              <span class="descuento">-50%</span>
              <p class="mejor-tienda">Mejor precio: Epic Games</p>
            </div>
          </a>
        </li>

        <li class="juego-card">
          <a href="juego.html?id=4">
            <img src="assets/img/placeholder.jpg" alt="Resident Evil 4" />
            <div class="juego-info">
              <h3>Resident Evil 4</h3>
              <p class="precio-actual">$1.800</p>
              <p class="precio-original">$3.000</p>
              <span class="descuento">-40%</span>
              <p class="mejor-tienda">Mejor precio: Steam</p>
            </div>
          </a>
        </li>

      </ul>

      <a href="resultados.php?categoria=ofertas">Ver todas las ofertas</a>
    </section>

  </main>

  <!-- ===== PIE DE PÁGINA ===== -->
  <footer>
    <p>© 2026 GamerVault — Grupo 7.2</p>
    <p>Datos provistos por <a href="https://www.cheapshark.com" target="_blank" rel="noopener">CheapShark API</a></p>
  </footer>


  <script src="js/main.js"></script>

</body>
</html>