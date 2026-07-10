<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: auth.php');
    exit();
}

// Conectar a BD para obtener la foto del usuario
require_once 'php/conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$foto_perfil = 'default.jpg';
$wishlist_publica = 1;

$sql = "SELECT foto, wishlist_publica FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['foto']) && $row['foto'] !== 'default.jpg') {
        $foto_perfil = $row['foto'];
    }
    $wishlist_publica = isset($row['wishlist_publica']) ? intval($row['wishlist_publica']) : 1;
}

$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi perfil — GamerVault</title>
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
      <div style="display: flex; gap: 2rem; align-items: flex-start;">
        <div class="perfil-foto-contenedor">
          <img 
            id="perfil-foto" 
            class="perfil-foto" 
            src="assets/img/perfiles/<?php echo htmlspecialchars($foto_perfil); ?>" 
            alt="Foto de perfil"
            onerror="this.src='assets/img/perfiles/default.jpg'"
          />
          <div class="perfil-foto-overlay">📷</div>
          <input 
            type="file" 
            id="perfil-foto-input" 
            class="perfil-foto-input" 
            accept=".jpg,.jpeg,.png,.gif"
          />
        </div>
        
        <div class="perfil-datos">
          <h1>Mi perfil</h1>
          <ul class="perfil-meta">
            <li>Usuario: <strong id="perfil-nombre"><?php echo $_SESSION['usuario_nombre']; ?></strong></li>
            <li>ID: <strong id="perfil-id">#<?php echo $_SESSION['usuario_id']; ?></strong></li>
            <li>Email: <strong id="perfil-email"><?php echo $_SESSION['usuario_email']; ?></strong></li>
            <li>Miembro desde: <strong id="perfil-fecha"><?php echo date('d/m/Y', strtotime($_SESSION['usuario_fecha'])); ?></strong></li>
          </ul>
        </div>
      </div>
    </section>

    <!-- TABS DE NAVEGACIÓN INTERNA -->
    <div class="perfil-tabs">
      <button type="button" id="tab-wishlist" class="tab activo">♥ Mi Wishlist</button>
      <button type="button" id="tab-alertas" class="tab">🔔 Mis Alertas</button>
      <button type="button" id="tab-historial" class="tab">🕓 Historial</button>
      <button type="button" id="tab-amigos" class="tab">👥 Amigos</button>
    </div>

    <!-- ===== WISHLIST ===== -->
    <section id="seccion-wishlist">
      <div class="seccion-header-flex">
        <h2>Mi Wishlist</h2>
        <div class="wishlist-privacidad">
          <label for="privacidad-wishlist">Visibilidad: </label>
          <select id="privacidad-wishlist">
            <option value="1" <?php echo $wishlist_publica === 1 ? 'selected' : ''; ?>>Pública</option>
            <option value="0" <?php echo $wishlist_publica === 0 ? 'selected' : ''; ?>>Privada</option>
          </select>
        </div>
      </div>

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
        <a href="resultados.php">Buscá un juego</a>
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
      </ul>

      <!-- Mensaje si no hay alertas -->
      <p id="alertas-vacias" hidden>
        No tenés alertas activas.
        <a href="resultados.php">Buscá un juego para crear una</a>
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

      <div class="historial-acciones-flex">
        <button type="button" id="btn-limpiar-historial">Limpiar historial</button>
        <button type="button" id="btn-limpiar-cache-app" class="btn-cache">Limpiar caché de la app</button>
      </div>

      <!-- Mensaje si el historial está vacío -->
      <p id="historial-vacio" hidden>
        Tu historial de búsquedas está vacío.
      </p>

    </section>

    <!-- ===== SECCIÓN AMIGOS ===== -->
    <section id="seccion-amigos" hidden>
      <h2>Mis Amigos</h2>

      <div class="amigos-buscar-contenedor">
        <h3>Agregar amigo</h3>
        <p class="amigos-ayuda-texto">Ingresá el nombre de usuario o el #ID de tu amigo para enviarle una solicitud.</p>
        <form id="form-agregar-amigo">
          <input type="text" id="input-amigo-busqueda" placeholder="Ej: elias o #3" required />
          <button type="submit" class="btn-primario-verde">Agregar Amigo</button>
        </form>
      </div>

      <!-- Solicitudes pendientes -->
      <div id="contenedor-solicitudes" class="solicitudes-seccion" hidden>
        <h3>Solicitudes Pendientes</h3>
        <div class="solicitudes-grids">
          <div id="solicitudes-recibidas-contenedor" class="columna-solicitud">
            <h4>Recibidas</h4>
            <ul id="lista-solicitudes-recibidas" class="lista-solicitudes"></ul>
          </div>
          <div id="solicitudes-enviadas-contenedor" class="columna-solicitud">
            <h4>Enviadas</h4>
            <ul id="lista-solicitudes-enviadas" class="lista-solicitudes"></ul>
          </div>
        </div>
      </div>

      <!-- Grid de amigos -->
      <div class="lista-amigos-seccion">
        <h3>Tus Amigos</h3>
        <ul id="lista-amigos" class="lista-juegos-amigos">
          <!-- Cargado con JS -->
        </ul>
        <p id="amigos-vacio" hidden>
          Todavía no tenés amigos agregados.
        </p>
      </div>
    </section>

  </main>

  <!-- ===== MODAL WISHLIST AMIGO ===== -->
  <div id="modal-wishlist-amigo" class="modal-overlay">
    <div class="modal-content modal-wishlist-amigo-content">
      <div class="modal-header modal-wishlist-amigo-header">
        <h2 id="modal-wishlist-titulo">Wishlist de Amigo</h2>
        <button type="button" id="btn-cerrar-wishlist-modal">&times;</button>
      </div>
      <div class="modal-body modal-wishlist-amigo-body">
        <ul class="lista-juegos" id="modal-lista-wishlist-amigo">
          <!-- Juegos del amigo -->
        </ul>
        <p id="modal-wishlist-amigo-vacia" hidden>
          Este amigo no tiene juegos en su wishlist.
        </p>
      </div>
    </div>
  </div>

  <!-- ===== PIE DE PÁGINA ===== -->
  <footer>
    <p>© 2026 GamerVault — Grupo 7.2</p>
    <p>Datos provistos por <a href="https://www.cheapshark.com" target="_blank" rel="noopener">CheapShark API</a></p>
  </footer>

  <script src="js/main.js?v=<?php echo time(); ?>"></script>
  
  <script>
    // Foto de perfil circular
    const fotoPerfil = document.getElementById('perfil-foto');
    const fotoPerfílInput = document.getElementById('perfil-foto-input');
    const perfilFotoContenedor = document.querySelector('.perfil-foto-contenedor');

    // Abrir selector de archivos al hacer click en la foto
    fotoPerfil.addEventListener('click', () => {
      fotoPerfílInput.click();
    });

    perfilFotoContenedor.addEventListener('click', () => {
      fotoPerfílInput.click();
    });

    // Manejar cambio de archivo
    fotoPerfílInput.addEventListener('change', async (e) => {
      const archivo = e.target.files[0];
      if (!archivo) return;

      // Validación básica en cliente
      const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
      const tamanioMaximo = 2 * 1024 * 1024; // 2MB

      if (!tiposPermitidos.includes(archivo.type)) {
        alert('Solo se permiten imágenes JPG, PNG o GIF');
        return;
      }

      if (archivo.size > tamanioMaximo) {
        alert('La imagen debe ser menor a 2MB');
        return;
      }

      // Preparar FormData
      const formData = new FormData();
      formData.append('foto', archivo);

      try {
        // Mostrar estado de carga
        fotoPerfil.style.opacity = '0.5';
        
        // Enviar a servidor
        const response = await fetch('php/subir_foto.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (response.ok) {
          // Actualizar la foto en la página
          fotoPerfil.src = data.foto;
          fotoPerfil.style.opacity = '1';
          
          console.log('✓ Foto de perfil actualizada');
        } else {
          fotoPerfil.style.opacity = '1';
          alert('Error: ' + (data.error || 'No se pudo guardar la foto'));
        }
      } catch (error) {
        fotoPerfil.style.opacity = '1';
        console.error('Error al enviar la foto:', error);
        alert('Error al procesar la imagen');
      }

      // Limpiar input para permitir subir el mismo archivo de nuevo
      fotoPerfílInput.value = '';
    });
  </script>

</body>
</html>