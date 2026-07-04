# 🎮 GamerVault — Comparador de Precios de Videojuegos
> TP web Primer Cuatrimestre **PISWD** — Curso 7.2  
> Integrantes: **Agustín Hermida** y **Elías González**
---
## Descripción
**GamerVault** es una aplicación web que permite comparar precios de videojuegos en tiempo real entre múltiples tiendas digitales como Steam, GOG, Epic Games Store, Humble Store, Green Man Gaming y GamersGate.
El objetivo del sitio es ayudar a los jugadores a encontrar el precio más bajo disponible para cualquier videojuego, sin tener que visitar cada tienda individualmente. Los datos son provistos por la [CheapShark API](https://www.cheapshark.com/), una API pública y gratuita que agrega precios de tiendas de videojuegos digitales.
---
##  Funcionalidades principales
- **Buscador con autocompletado** — Busca cualquier juego en tiempo real con sugerencias dinámicas.
- **Comparativa de precios por tienda** — Muestra el precio actual de cada tienda ordenado de menor a mayor.
- **Ofertas destacadas** — La página principal carga automáticamente las 6 mejores ofertas del día.
- **Wishlist** — Los usuarios registrados pueden guardar juegos para seguir su precio.
- **Alertas de precio** — El sistema notifica por email cuando un juego baja al precio objetivo configurado.
- **Historial de precios** — Visualización gráfica de la evolución de precios de cada juego.
- **Historial de búsquedas** — Registro de los últimos juegos buscados por el usuario.
   **Sistema de usuarios** — Registro, login, logout y foto de perfil personalizable.
---
## Tecnologías utilizadas

| Capa | Tecnología |
| :--- | :--- |
| Frontend | HTML5, CSS3 (Vanilla), JavaScript (ES6) |
| Backend | PHP 8 |
| Base de datos | MySQL (MariaDB) |
| API externa | [CheapShark API](https://www.cheapshark.com/) |
| Gráficos | [Chart.js](https://www.chartjs.org/) |
| Tipografías | Google Fonts (Orbitron, Rajdhani) |
## Base de datos
- **Nombre:** `gamervault`
- **Motor:** MySQL / MariaDB
- **Archivo exportado:** [`gamervault.sql`](./gamervault.sql)
### Tablas principales

| Tabla | Descripción |
| :--- | :--- |
| `usuarios` | Registro de usuarios del sistema |
| `wishlist` | Juegos guardados por cada usuario |
| `alertas_precio` | Alertas de precio configuradas |
| `historial_busquedas` | Historial de búsquedas por usuario |
## Instalación local
### Requisitos
- XAMPP (Apache + MySQL + PHP 8)
- Navegador moderno con acceso a internet (para la API)
### Pasos
1. Cloná el repositorio dentro de la carpeta `htdocs` de XAMPP:
   ```bash
   git clone https://github.com/Raziiel08/paginacalla gamervault
   ```
2. Importá la base de datos desde phpMyAdmin:
   - Creá una base de datos llamada `gamervault`
   - Importá el archivo `gamervault.sql`
3. Verificá la configuración de conexión en `php/conexion.php`:
   ```php
   $host     = "localhost";
   $usuario  = "root";
   $password = "";
   $base     = "gamervault";
   ```
4. Iniciá Apache y MySQL desde el panel de XAMPP.
5. Accedé al sitio en: `http://localhost/gamervault`
---
## Estructura del proyecto
```
gamervault/
├── index.php               # Página principal con ofertas destacadas
├── resultados.php          # Resultados de búsqueda y comparativa de precios
├── juego.php               # Ficha detallada de un juego
├── perfil.php              # Perfil de usuario (wishlist, alertas, historial)
├── auth.php                # Login y registro
├── css/
│   └── styles.css          # Estilos globales del sitio
├── js/
│   └── main.js             # Lógica JavaScript del frontend
├── php/
│   ├── conexion.php        # Conexión a la base de datos
│   ├── login.php           # Procesamiento del login
│   ├── registro.php        # Procesamiento del registro
│   ├── logout.php          # Cierre de sesión
│   ├── subir_foto.php      # Subida de foto de perfil
│   ├── wishlist/           # API de wishlist (agregar, obtener, quitar)
│   ├── alertas/            # API de alertas (crear, obtener, editar, eliminar)
│   └── historial/          # API de historial de búsquedas
├── assets/
│   └── img/                # Imágenes del sitio
└── gamervault.sql          # Exportación de la base de datos
```
---
## Integrantes

| Nombre | GitHub |
| :--- | :--- |
| Agustín Hermida | [@Raziiel08](https://github.com/Raziiel08) |
| Elías González | [@nahuxx2077](https://github.com/nahuxx2077) |
Curso 7.2 materia PISWD.  
Datos de precios: [CheapShark API](https://www.cheapshark.com/).
