docker-build:
	docker build -t php8-cli .

deps:
	docker run -it --tty --rm --volume $(PWD):/app -w /app php8-cli bash -c "composer install"

test:
	docker run -it --tty --rm --volume $(PWD):/app -w /app php8-cli bash -c "vendor/bin/phpunit -c phpunit.xml --coverage-html .coverage"