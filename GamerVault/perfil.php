<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: auth.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi perfil — GamerVault</title>
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

      <div class="nav-usuario">
        <!--
          Esto se llena con JS usando los datos de $_SESSION.
          Muestra el nombre del usuario logueado.
        -->
        <span id="nav-nombre-usuario">Hola, <?php echo $_SESSION['usuario_nombre']; ?></span>
        <a href="php/logout.php">Cerrar sesión</a>
      </div>
    </nav>
  </header>

  <!-- ===== CONTENIDO PRINCIPAL ===== -->
  <main>

    <!--
      PROTECCIÓN DE RUTA:
      Este chequeo se hace en PHP al inicio del archivo.
      Si no hay sesión activa, redirige a auth.php:

      <?php
        session_start();
        if (!isset($_SESSION['usuario_id'])) {
          header('Location: ../auth.php');
          exit();
        }
      ?>
    -->

    <!-- CABECERA DEL PERFIL -->
    <section class="perfil-cabecera">
      <h1>Mi perfil</h1>
      <ul class="perfil-meta">
        <li>Usuario: <strong id="perfil-nombre"><?php echo $_SESSION['usuario_nombre']; ?></strong></li>
        <li>Email: <strong id="perfil-email"><?php echo $_SESSION['usuario_email']; ?></strong></li>
        <li>Miembro desde: <strong id="perfil-fecha"><?php echo date('d/m/Y', strtotime($_SESSION['usuario_fecha'])); ?></strong></li>
      </ul>
    </section>

    <!-- TABS DE NAVEGACIÓN INTERNA -->
    <div class="perfil-tabs">
      <button type="button" id="tab-wishlist" class="tab activo">♥ Mi Wishlist</button>
      <button type="button" id="tab-alertas" class="tab">🔔 Mis Alertas</button>
      <button type="button" id="tab-historial" class="tab">🕓 Historial</button>
    </div>

    <!-- ===== WISHLIST ===== -->
    <section id="seccion-wishlist">
      <h2>Mi Wishlist</h2>

      <!--
        Esta lista se llena dinámicamente desde la BD (tabla wishlist)
        a través de php/wishlist.php que devuelve JSON.
        Hardcodeada por ahora para definir la estructura.
      -->
      <ul class="lista-juegos" id="lista-wishlist">
      </ul>

      <!-- Mensaje si la wishlist está vacía -->
      <p id="wishlist-vacia" hidden>
        Todavía no agregaste juegos a tu wishlist.
        <a href="index.html">Buscá un juego</a>
      </p>

    </section>

    <!-- ===== ALERTAS DE PRECIO ===== -->
    <section id="seccion-alertas" hidden>
      <h2>Mis Alertas de precio</h2>

      <p>Te avisamos cuando un juego baje del precio que definiste.</p>

      <!--
        Lista de alertas activas desde la tabla `alertas` de la BD.
      -->
      <ul class="lista-alertas" id="lista-alertas">

        <li class="alerta-card">
          <div class="alerta-info">
            <h3><a href="juego.html?id=7">Red Dead Redemption 2</a></h3>
            <p>Precio máximo definido: <strong class="precio-maximo">$1.200</strong></p>
            <p>Precio actual más bajo: <strong class="precio-actual">$1.800</strong></p>
            <p class="alerta-estado">⏳ Esperando oferta...</p>
          </div>
          <div class="alerta-acciones">
            <button type="button" class="btn-editar-alerta" data-id="7">
              Editar precio
            </button>
            <button type="button" class="btn-eliminar-alerta" data-id="7">
              Eliminar alerta
            </button>
          </div>
        </li>

      </ul>

      <!-- Mensaje si no hay alertas -->
      <p id="alertas-vacias" hidden>
        No tenés alertas activas.
        <a href="index.html">Buscá un juego para crear una</a>
      </p>

    </section>

    <!-- ===== HISTORIAL DE BÚSQUEDAS ===== -->
    <section id="seccion-historial" hidden>
      <h2>Historial de búsquedas</h2>

      <!--
        Lista desde la tabla `historial_busquedas` de la BD.
      -->
      <ul class="lista-historial" id="lista-historial">
      </ul>

      <button type="button" id="btn-limpiar-historial">Limpiar historial</button>

      <!-- Mensaje si el historial está vacío -->
      <p id="historial-vacio" hidden>
        Tu historial de búsquedas está vacío.
      </p>

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