COMPOSE := docker compose -f docker/compose/docker-compose.yml

.PHONY: up down build logs shell-back migrate seed test

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
