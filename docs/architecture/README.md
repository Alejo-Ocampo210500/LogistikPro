# Arquitectura

LogistikPro contiene dos aplicaciones desplegables: una API Laravel en
`apps/backend/api` y una aplicacion Vue en `apps/frontend`.

El frontend se sirve mediante Nginx. Las solicitudes a `/api` se envian por la
red interna de Docker al servicio `backend`. PostgreSQL es una dependencia
externa y no forma parte del Compose para mantener exactamente dos servicios.
