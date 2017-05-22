#!/usr/bin/env bash

./vendor/bin/phpcs --standard=PSR2 src --exclude=Generic.Files.LineLength
./vendor/bin/phpcbf --standard=PSR2 bin --exclude=Generic.Files.LineLength
# Running phpunit globally can cause Composer autoloading to fail.
# Xdebug must be enabled in order for code coverage driver to load!
./vendor/bin/phpunit
