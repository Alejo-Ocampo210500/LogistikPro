# Despliegue local

La configuracion principal vive en `docker/compose/docker-compose.yml`.

1. Copiar `env/api.env.example` a `env/api.env`.
2. Copiar `env/frontend.env.example` a `env/frontend.env`.
3. Ajustar las credenciales de PostgreSQL si se desean valores diferentes.
4. Ejecutar `make up`.
5. Ejecutar `make migrate` y, si corresponde, `make seed`.

El frontend se publica en el puerto `8080` y la API en el `8000` por defecto.
