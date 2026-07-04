<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>GamerVault Comparador de precios</title>
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
        <li><a href="perfil.php?tab=alertas">Alertas</a></li>
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

    <!-- OFERTAS DESTACADAS -->
    <section class="ofertas-destacadas">
      <h2>🔥 Ofertas destacadas hoy</h2>

      <ul class="lista-juegos" id="lista-ofertas">
        <!-- Skeleton loaders (se reemplazan con datos reales de la API) -->
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
        <li class="juego-card skeleton-card"><div class="skeleton-img"></div><div class="juego-info"><div class="skeleton-line skeleton-title"></div><div class="skeleton-line skeleton-price"></div><div class="skeleton-line skeleton-store"></div></div></li>
      </ul>

      <p id="ofertas-error" hidden>No se pudieron cargar las ofertas. Intentá de nuevo más tarde.</p>

      <a href="resultados.php?categoria=ofertas">Ver todas las ofertas</a>
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