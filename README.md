# LogistikPro

Monorepo de LogistikPro, organizado con el mismo patron de Medicina Integral:

- `apps/backend/api`: API Laravel 12 con PHP 8.2.
- `apps/frontend`: interfaz Vue 2 servida por Nginx.
- `docker`: Compose, proxy Nginx y scripts de contenedores.
- `env`: ejemplos de configuracion por aplicacion.
- `docs`: documentacion de arquitectura y despliegue.
- `packages`: espacio reservado para paquetes compartidos futuros.

## Inicio rapido con Docker

Requisitos: Docker Desktop y una instancia accesible de PostgreSQL.

```bash
cp .env.example .env
cp env/api.env.example env/api.env
docker compose -f docker/compose/docker-compose.yml up --build -d
docker compose -f docker/compose/docker-compose.yml exec backend php artisan migrate --seed
```

La interfaz queda disponible en <http://localhost:8080> y la API en
<http://localhost:8000/api>. El Nginx del frontend tambien redirige `/api` al
backend, por lo que el navegador no depende de una direccion interna de Docker.

Para detener todo:

```bash
docker compose -f docker/compose/docker-compose.yml down
```

Los comandos abreviados equivalentes estan disponibles en el `Makefile`:
`make up`, `make logs`, `make migrate`, `make seed`, `make test` y `make down`.

## Base de datos

El Compose contiene deliberadamente solo los servicios `backend` y `frontend`.
Por defecto, Laravel busca PostgreSQL en el equipo anfitrion mediante
`host.docker.internal`. Ajusta `DB_HOST` y las demas variables del `.env` raiz
si la base vive en otro servidor.

## Historial anterior

Los directorios `.git` que venian dentro de cada aplicacion fueron renombrados
a `.git.backup`. Estan ignorados por el repositorio raiz y permiten recuperar
los historiales independientes si fueran necesarios.
