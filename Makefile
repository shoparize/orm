test-blog-app:
	clear;
	CURRENT_UID=`id -u`:`id -g` docker-compose run blog-app ./src/Generator/zenderator --workdir=./examples/blog-app

setup:
	composer install

test: setup test-blog-app