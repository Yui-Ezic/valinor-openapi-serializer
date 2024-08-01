init: docker-down-clear docker-pull docker-build docker-up composer-install

fix: fixcs
analyze: test checkstyle

test:
	docker-compose run php composer test

fixcs:
	docker-compose run php composer fixcs

checkstyle:
	docker-compose run php composer checkstyle

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull

composer-install:
	docker-compose run php composer install

