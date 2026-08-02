# Despliegue local

La configuracion principal vive en `docker/compose/docker-compose.yml`.

1. Copiar `.env.example` a `.env`.
2. Copiar `env/api.env.example` a `env/api.env` y ajustar PostgreSQL.
3. Ejecutar `make up`.
4. Ejecutar `make migrate` y, si corresponde, `make seed`.

El frontend se publica en el puerto `8080` y la API en el `8000` por defecto.
