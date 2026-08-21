COMPOSE := docker compose

.PHONY: init up down restart shell composer-install migrate test stan cs cs-fix check logs

init:
	@test -f .env || cp .env.example .env
	$(COMPOSE) build app
	$(COMPOSE) run --rm app composer install
	$(COMPOSE) up -d
	$(COMPOSE) exec app php yii migrate --interactive=0

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) restart

shell:
	$(COMPOSE) exec app sh

composer-install:
	$(COMPOSE) run --rm app composer install

migrate:
	$(COMPOSE) exec app php yii migrate --interactive=0

test:
	$(COMPOSE) exec app composer test

stan:
	$(COMPOSE) exec app composer stan

cs:
	$(COMPOSE) exec app composer cs

cs-fix:
	$(COMPOSE) exec app composer cs-fix

check: test stan cs

logs:
	$(COMPOSE) logs -f --tail=100
