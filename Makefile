install:
	composer install

lint:
	composer run-script phpcs -- --standard=PSR12 src bin
	composer run-script phpstan

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 src tests

test:
	composer run-script test

test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

validate:
	composer validate

autoload-update:
	composer dump-autoload