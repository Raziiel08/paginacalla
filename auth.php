<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login / Registro — GamerVault</title>
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
      </ul>
    </nav>
  </header>

  <!-- ===== CONTENIDO PRINCIPAL ===== -->
  <main>

    <!--
      Tabs para alternar entre Login y Registro.
      Con JS mostrás/ocultás la sección correspondiente
      según el tab activo.
    -->
    <div class="auth-tabs">
      <button type="button" id="tab-login" class="tab activo">Iniciar sesión</button>
      <button type="button" id="tab-registro" class="tab">Crear cuenta</button>
    </div>

    <!-- ===== FORMULARIO LOGIN ===== -->
    <section id="seccion-login">
      <h1>Iniciar sesión</h1>

      <!--
        action apunta al PHP que verifica las credenciales
        y crea la sesión con $_SESSION.
        method="POST" para que los datos no vayan en la URL.
      -->
      <form action="php/login.php" method="POST">

        <div class="campo">
          <label for="login-email">Email</label>
          <input
            type="email"
            id="login-email"
            name="email"
            placeholder="tucorreo@email.com"
            required
            autocomplete="email"
          />
        </div>

        <div class="campo">
          <label for="login-password">Contraseña</label>
          <input
            type="password"
            id="login-password"
            name="password"
            placeholder="Tu contraseña"
            required
            autocomplete="current-password"
          />
        </div>

        <!-- Mensaje de error (se muestra desde PHP si las credenciales fallan) -->
        <p class="error-mensaje" id="login-error" hidden>
          Email o contraseña incorrectos.
        </p>

        <button type="submit">Ingresar</button>

        <p>
          ¿No tenés cuenta?
          <button type="button" id="ir-registro">Registrate acá</button>
        </p>

      </form>
    </section>

    <!-- ===== FORMULARIO REGISTRO ===== -->
    <section id="seccion-registro" hidden>
      <h1>Crear cuenta</h1>

      <!--
        action apunta al PHP que valida los datos,
        encripta la contraseña con bcrypt (password_hash)
        e inserta el usuario en la tabla `usuarios`.
      -->
      <form action="php/registro.php" method="POST">

        <div class="campo">
          <label for="registro-nombre">Nombre de usuario</label>
          <input
            type="text"
            id="registro-nombre"
            name="nombre"
            placeholder="Tu nombre de usuario"
            required
            minlength="3"
            maxlength="50"
            autocomplete="username"
          />
        </div>

        <div class="campo">
          <label for="registro-email">Email</label>
          <input
            type="email"
            id="registro-email"
            name="email"
            placeholder="tucorreo@email.com"
            required
            autocomplete="email"
          />
        </div>

        <div class="campo">
          <label for="registro-password">Contraseña</label>
          <input
            type="password"
            id="registro-password"
            name="password"
            placeholder="Mínimo 8 caracteres"
            required
            minlength="8"
            autocomplete="new-password"
          />
        </div>

        <div class="campo">
          <label for="registro-password-confirmar">Confirmar contraseña</label>
          <input
            type="password"
            id="registro-password-confirmar"
            name="password_confirmar"
            placeholder="Repetí tu contraseña"
            required
            minlength="8"
            autocomplete="new-password"
          />
        </div>

        <!-- Mensajes de error -->
        <p class="error-mensaje" id="registro-error" hidden>
          Las contraseñas no coinciden.
        </p>
        <p class="error-mensaje" id="registro-email-error" hidden>
          Ya existe una cuenta con ese email.
        </p>

        <button type="submit">Crear cuenta</button>

        <p>
          ¿Ya tenés cuenta?
          <button type="button" id="ir-login">Iniciá sesión acá</button>
        </p>

      </form>
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