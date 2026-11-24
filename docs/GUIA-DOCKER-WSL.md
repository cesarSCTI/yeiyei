# Levantar YAMS desde WSL

Ejecuta todos los comandos en Ubuntu. Compose tiene tres servicios: `db` ejecuta MariaDB y guarda los datos, `phpmyadmin` permite importarlos y administrarlos desde el navegador, y `web` ejecuta YAMS con PHP y Apache.

## 1. Comprueba que Docker esté disponible

```bash
cd ~/systems/yeiyei
docker info
docker compose config --quiet
```

Si usas Docker Desktop como motor, debe estar en ejecución y tener habilitada la integración con Ubuntu.

## 2. Levanta MariaDB

```bash
docker compose up -d --wait db
```

En un volumen nuevo, MariaDB crea la base vacía `yeiyeisw_swimming` y la cuenta `yams`. El respaldo se carga manualmente en el paso 3.

## 3. Importa el respaldo desde phpMyAdmin

```bash
docker compose --profile admin up -d phpmyadmin
```

1. Abre [phpMyAdmin](http://localhost:8081/).
2. Entra con el usuario `yams` y la contraseña local `yams-local-dev`. Si configuraste `DB_PASSWORD` al crear el volumen, usa esa contraseña. El servidor `db` ya está configurado.
3. Selecciona `yeiyeisw_swimming` en el panel izquierdo. Si ya aparecen las 14 tablas de la restauración anterior, puedes pasar al paso 4. Importa el respaldo sobre una base vacía.
4. Abre **Importar** → **Seleccionar archivo** y elige `yeiyeisw_swimming.sql`, que está en la raíz del proyecto. Si el selector de archivos se abre en Windows, pega esta ruta:

   ```text
   \\wsl.localhost\Ubuntu\home\cguzman\systems\yeiyei\yeiyeisw_swimming.sql
   ```

5. Deja **SQL** como formato y pulsa **Importar**. Verifica que aparezcan 14 tablas, entre ellas `users`, `clientes` y `pagos`. [Documentación de importación de phpMyAdmin](https://docs.phpmyadmin.net/en/latest/import_export.html#import)

Para hacer una importación desde cero conservando el volumen anterior, detén primero los servicios con `docker compose --profile admin stop` y ejecuta `export COMPOSE_PROJECT_NAME=yeiyei-prueba`. Repite desde el paso 2 en esa misma terminal. Compose creará un volumen separado. Para volver al proyecto original, detén los servicios de prueba y ejecuta `unset COMPOSE_PROJECT_NAME`.

## 4. Levanta YAMS

Con MariaDB en ejecución y las tablas disponibles, ejecuta:

```bash
docker compose up -d --build web
docker compose ps
```

Abre [YAMS](http://localhost:8080/yams/) e inicia sesión con una cuenta de la aplicación incluida en el respaldo.

## Controla cada servicio

Cada comando `up` de esta guía inicia únicamente el servicio indicado. Puedes detener cada uno por separado:

```bash
docker compose --profile admin stop phpmyadmin
docker compose stop web
docker compose stop db
```

Puedes cerrar phpMyAdmin después de importar. YAMS sigue funcionando mientras MariaDB esté en ejecución. Para detener todo y conservar los datos, usa `docker compose --profile admin stop`. Para revisar un servicio, usa `docker compose logs --tail=50 db` y cambia `db` por el nombre que corresponda.

Los puertos locales se pueden cambiar con `WEB_PORT` y `PHPMYADMIN_PORT`. Por ejemplo, `PHPMYADMIN_PORT=8082 docker compose --profile admin up -d phpmyadmin` deja phpMyAdmin en el puerto 8082.

