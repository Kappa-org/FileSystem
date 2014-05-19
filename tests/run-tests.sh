#!/bin/bash

composer=composer.json
composer_backup=composer_backup.json

trap rollback INT

function backup()
{
	cp $composer $composer_backup
}

function rollback()
{
	echo "Rollback original composer.json and exit test"
	mv $composer_backup $composer
	composer update
	exit 0;
}

backup
echo "Default composer"
./vendor/bin/tester tests/ $@
variables="nette-2.1 nette-dev"
for variable in $variables; do
	export NETTE=$variable
	php ./tests/prepare-composer.php
	composer update
	./vendor/bin/tester tests/ $@
done
rollback