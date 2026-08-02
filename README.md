# LogistikPro

Monorepo de LogistikPro, organizado con el mismo patron monorepo:

- `apps/backend/api`: API Laravel 12 con PHP 8.2.
- `apps/frontend`: interfaz Vue 2 servida por Nginx.
- `docker`: Compose, proxy Nginx y scripts de contenedores.
- `env`: ejemplos de configuracion por aplicacion.
- `docs`: documentacion de arquitectura y despliegue.
- `packages`: espacio reservado para paquetes compartidos futuros.

## Inicio rapido con Docker

Requisito: Docker Desktop.

```bash
cp env/api.env.example env/api.env
cp env/frontend.env.example env/frontend.env
docker compose --env-file env/api.env -f docker/compose/docker-compose.yml up --build -d
docker compose --env-file env/api.env -f docker/compose/docker-compose.yml exec backend php artisan migrate --seed
```

La interfaz queda disponible en <http://localhost:8080> y la API en
<http://localhost:8000/api>. El Nginx del frontend tambien redirige `/api` al
backend, por lo que el navegador no depende de una direccion interna de Docker.

Para detener todo:

```bash
docker compose --env-file env/api.env -f docker/compose/docker-compose.yml down
```

Los comandos abreviados equivalentes estan disponibles en el `Makefile`:
`make up`, `make logs`, `make migrate`, `make seed`, `make test` y `make down`.

## Base de datos de pruebas

PostgreSQL 16 corre en el servicio `database`, se publica en el puerto `5432`
y conserva sus datos en el volumen `postgres_data`. Las credenciales por
defecto son solo para desarrollo local y se pueden cambiar en `env/api.env`.

Para borrar completamente la base de pruebas y empezar de cero:

```bash
docker compose --env-file env/api.env -f docker/compose/docker-compose.yml down -v
```

## Variables de entorno

Toda la configuracion real esta centralizada en `env/`:

- `env/api.env`: configuracion del backend Laravel.
- `env/frontend.env`: configuracion del frontend Vue.

Los archivos reales estan ignorados por Git. Solo se versionan los archivos
terminados en `.env.example`.

Para ejecutar las aplicaciones localmente usando estos archivos:

```bash
make dev-back
make dev-front
```

## Historial anterior

Los directorios `.git` que venian dentro de cada aplicacion fueron renombrados
a `.git.backup`. Estan ignorados por el repositorio raiz y permiten recuperar
los historiales independientes si fueran necesarios.
