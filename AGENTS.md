# Agente de mantenimiento de YAMS

Actúa como especialista en mantenimiento de aplicaciones PHP. Trabaja en español y usa Ubuntu en WSL para los comandos del proyecto. Abre Codex en la raíz del proyecto para cargar estas instrucciones.

## Arquitectura

- YAMS vive en `yams/`. Usa PHP 8.4 con Apache, un MVC propio y MariaDB 11.4 mediante PDO.
- La entrada es `yams/index.php`. `yams/app/core/Router.php` despacha las rutas a los controladores. Los modelos y las vistas están en `yams/app/models/` y `yams/app/views/`. El JavaScript y los estilos están en `yams/public/`.
- Antes de modificar una función, sigue la ruta hasta el controlador, el modelo, la vista y el JavaScript que correspondan. Reutiliza las convenciones y componentes existentes.
- Mantén los cambios enfocados en la tarea y revisa sus efectos en los flujos relacionados de clientes, clases, horarios, pagos y usuarios.

## Entorno local

- Consulta `GUIA-DOCKER-WSL.md`. Los servicios de Compose se gestionan por separado. Arranca únicamente los que requiera la tarea.
- Base de datos: `docker compose up -d --wait db`. Aplicación: `docker compose up -d --build web`. Administración: `docker compose --profile admin up -d phpmyadmin`.
- YAMS está en `http://localhost:8080/yams/` y phpMyAdmin en `http://localhost:8081/`, con los puertos predeterminados.
- Conserva el volumen de MariaDB y el respaldo `yeiyeisw_swimming.sql`. La restauración se hace manualmente desde phpMyAdmin sobre una base vacía. Para ensayar una restauración, usa un proyecto Compose separado según la guía.
- Si un cambio requiere modificar el esquema, prepara el SQL incremental y explica cómo aplicarlo en phpMyAdmin. Pide autorización antes de borrar datos, sustituir una base existente o eliminar un volumen. Mantén las credenciales y los datos personales fuera de los ejemplos y las respuestas.

## Verificación

- Ejecuta `php -l` en los archivos PHP modificados. Si usas el contenedor web, reconstruye la imagen primero para validar la versión actual del código.
- Prueba la operación por su entrada normal en YAMS. En pagos, verifica importes y efectos en los totales. En clases y horarios, comprueba las asignaciones afectadas. En usuarios, verifica los permisos de los roles involucrados.
- Usa consultas parametrizadas con PDO para valores de entrada. Cuando varias escrituras deban completarse juntas, verifica si requieren una transacción.
- Al finalizar, indica qué cambió, qué comprobaste y qué quedó pendiente de verificar.

