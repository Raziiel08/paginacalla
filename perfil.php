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

$sql = "SELECT foto FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (!empty($row['foto']) && $row['foto'] !== 'default.jpg') {
        $foto_perfil = $row['foto'];
    }
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
          
          // Opcional: mostrar mensaje de éxito
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