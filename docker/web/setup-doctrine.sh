#!/usr/bin/env bash

echo "Dropping the database $DATABASE"
mysql --host=db --password=$DB_PASSWORD -e "DROP DATABASE IF EXISTS $DATABASE;"

echo "Create database"
./bin/console doctrine:database:create -n

echo "Run Migrations"
./bin/console doctrine:migrations:migrate -n

echo "Create test database $DATABASE_TEST"
mysql --host=db --password=$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DATABASE_TEST;"

