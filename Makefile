SHELL:=/bin/bash
UID:=$(shell id -u)
DC_RUN:=docker-compose run -u ${UID} --rm

clear:
	clear;

setup:
	docker-compose down -v
	${DC_RUN} test composer install
	${DC_RUN} test sleep 15

migrate:
	${DC_RUN} test vendor/bin/phinx rollback
	${DC_RUN} test vendor/bin/phinx migrate

regen:
	${DC_RUN} test bin/laminator

clean:
	${DC_RUN} test vendor/bin/php-cs-fixer fix

test: clear setup migrate regen clean
	${DC_RUN} test vendor/bin/phpunit