#!/bin/bash

if [ "$2" == "-db" ]
then
echo "rebuilding database ..."
sudo php bin/console doctrine:schema:drop -n -q --force --full-database
sudo rm migrations/*.php
sudo php bin/console make:migration
sudo php bin/console doctrine:migrations:migrate -n -q
sudo php bin/console doctrine:fixtures:load -n -q
fi

if [ -n "$1" ]
then
sudo php ./bin/phpunit $1
else
sudo php ./bin/phpunit
fi
