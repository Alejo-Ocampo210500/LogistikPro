COMPOSE := docker compose --env-file env/api.env -f docker/compose/docker-compose.yml

.PHONY: up down build logs shell-back migrate seed test dev-back dev-front

up:
	$(COMPOSE) up --build -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build

logs:
	$(COMPOSE) logs -f

shell-back:
	$(COMPOSE) exec backend sh

migrate:
	$(COMPOSE) exec backend php artisan migrate

seed:
	$(COMPOSE) exec backend php artisan db:seed

test:
	$(COMPOSE) run --rm backend php artisan test

dev-back:
	set -a; . ./env/api.env; set +a; cd apps/backend/api && DB_HOST=127.0.0.1 php artisan serve

dev-front:
	set -a; . ./env/frontend.env; set +a; npm --prefix apps/frontend run serve
