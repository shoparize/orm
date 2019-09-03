test-blog-app:
	clear;
	docker-compose run blog-app ./src/Generator/zenderator --workdir=./examples/blog-app

setup:
	composer install

test: setup test-blog-app