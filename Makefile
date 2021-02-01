install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin

validate:
	composer validate

autoload-update:
	composer dump-autoload