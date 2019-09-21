Dockerfileexport PROJECT_IP=127.0.0.1
export MEMCACHED_PORT=11211
export XDEBUG_CONFIG="idekey=VSCODE"

#constants
CONT_REVERSEPROXY=reverseproxy
CONT_WEB=web
CONT_DB=db
DATABASE=house_hunt
DATABASE_TEST=$(DATABASE)_test
DB_PASSWORD=passpass
CURRENT_BRANCH=$(shell git branch | grep \* | cut -d ' ' -f2)


# Add the following 'help' target to your Makefile
# And add help text after each target name starting with '\#\#'
# A category can be added with @category
HELP_FUN = \
	%help; \
	while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
	print "usage: make [target]\n\n"; \
	for (sort keys %help) { \
	print "${WHITE}$$_:${RESET}\n"; \
	for (@{$$help{$$_}}) { \
	$$sep = " " x (32 - length $$_->[0]); \
	print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
	}; \
	print "\n"; }

# Process parameters/options
ifeq (cli,$(firstword $(MAKECMDGOALS)))
    ifndef container
        CONTAINER := cli
        CUSTOM_SHELL := zsh
    else
        ifeq ($(filter $(container),$(CONTAINERS)),)
            $(error Invalid container. $(CONTAINER) does not exist in $(CONTAINERS))
        endif
        CONTAINER := $(container)
        CUSTOM_SHELL := /bin/sh
    endif

    ifdef shell
        CUSTOM_SHELL := $(shell)
    endif
#    RUN_ARGS := $(DOCKER_TAG)_$(CONTAINER)_1
endif

ifeq (composer-update,$(firstword $(MAKECMDGOALS)))
    PACKAGES =
    ifdef packages
        PACKAGES := $(packages)
    endif
endif

ifeq (logs,$(firstword $(MAKECMDGOALS)))
    LOGS_TAIL := 0
    ifdef tail
        LOGS_TAIL := $(tail)
    endif
endif

TEST_GROUP := -g Acceptance

ifeq (acceptance-tests,$(firstword $(MAKECMDGOALS)))
    ifdef group
        TEST_GROUP := -g $(group)
    endif
endif

help: ##@other Show this help.
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)
.PHONY: help

open: ##@other Opens all containers in browser tabs.
	open http://househunt/
	open http://adminer/
	open http://selenium:4444/grid/console
.PHONY: help

setup-doctrine: ##@doctrine Drops and re-creates the database with data
	docker-compose exec $(CONT_WEB) sh /var/www/html/setup-doctrine.sh
.PHONY: setup-doctrine

create-test-database: ##@doctrine Creates or edits an entity
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS $(DATABASE_TEST);"
.PHONY: create-test-database


dump-database: ##@doctrine Dumps the database into a file
	docker-compose exec $(CONT_DB) mysqldump --password=$(DB_PASSWORD) $(DATABASE) > db/dumps/$(CURRENT_BRANCH).sql
.PHONY: dump-database

create-migration: ##@doctrine Creates a new migration
	make console COM="make:migration"
.PHONY: create-migration

migrate: ##@doctrine Execute new migrations
	make console COM=" doctrine:migrations:migrate"
.PHONY: migrate

load-seed-data: ##@doctrine Load Development Database Fixtures
	make console COM="doctrine:fixtures:load -n"
.PHONY: load-seed-data

build: ##@servers Builds the project
	docker-compose build --pull --parallel --force-rm
.PHONY: build

setup: ##@servers Sets up all containers
	docker-compose down
	make build
	docker-compose up -d
	make start
	make setup-doctrine
	make load-seed-data
	make open

.PHONY: setup

clean: ##@servers Tears down all containers
	docker-compose down
	docker-compose kill
.PHONY: clean

start: ##@servers Starts all containers
	#docker-compose start
	docker-compose up --no-start
	docker-compose up -d db
	docker-compose up -d web adminer mailcatcher selenium-hub selenium-chrome reverseproxy
.PHONY: start

stop: ##@servers Stops all containers
	docker-compose stop
.PHONY: stop

reload: ##@servers Reloads all containers
	make stop
	make start
.PHONY: reload

cli: ##@servers Open a bash shell inside the container
	docker-compose exec $(CONT_WEB) bash
.PHONY: cli

run: ##@servers Run a command inside the web container
	docker-compose exec $(CONT_WEB) $(COM)
.PHONY: run

composer: ##@servers Run a composer command inside the web container
	docker-compose exec $(CONT_WEB) bin/composer $(COM)
.PHONY: composer

console: ##@servers Run console inside the web container
	docker-compose exec $(CONT_WEB) bin/console $(COM)
.PHONY: console

apache-reload: ##@servers Reload the apache server
	make run COM="service apache2 reload"
.PHONY: apache-reload

rpcli: ##@servers Open a bash shell in the reverse proxy container
	docker-compose exec $(CONT_REVERSEPROXY) bash
.PHONY: rpcli

dbshell: ##@database Open a database shell
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) --database=$(DATABASE)
.PHONY: dbshell

dbcli: ##@database Open a bash shell in the database container
	docker-compose exec $(CONT_DB) bash
.PHONY: dbcli

dbrun: ##@database Run a command inside the databse container
	docker-compose exec $(CONT_DB) $(COM)
.PHONY: dbrun

mysql: ##@database Run a command inside the databse container
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) --database=$(DATABASE) -e "$(COM)"
.PHONY: mysql

drop-database: ##@database Deletes the database
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) -e "DROP DATABASE IF EXISTS $(DATABASE);"
.PHONY: drop-database

entity:
	make console COM="make:entity"
.PHONY: entity

create-database: ##@database Creates the database
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS $(DATABASE);"
.PHONY: create-database

fetchdata: ##@commands Use commands to fetch all data from APIs
	make console COM="idealista:fetch"
.PHONY: fetchdata

cache-clear: ##@dev Clear the cache
	make console COM="cache:clear"
.PHONY: cache-clear

encore: ##@dev Run Encore to build the assets
	(cd htdocs && yarn encore dev)
.PHONY: encore

encore-watch: ##@dev Watch changes and run Encore to build the assets
	(cd htdocs && yarn encore dev --watch)
.PHONY: encore-watch

#acceptance-tests: ##@testing Run the acceptance tests
#	make run COM="vendor/bin/codecept run acceptance"
#.PHONY: acceptance-tests

all-codeception-tests: ##@testing Run all tests using codeception
	make run COM="vendor/bin/codecept run"
.PHONY: all-codeception-tests


acceptance-tests: ##@testing Run Acceptance tests. example: make acceptance-tests FILTER="FirstCest:canLogin"
	make run COM="vendor/bin/codecept run acceptance $(FILTER)"
.PHONY: acceptance-tests

unit-tests: ##@testing Run Unit tests. example: make unit-tests FILTER="FirstCest:canLogin"
	make run COM="vendor/bin/codecept run unit $(FILTER)"
.PHONY: unit-tests

functional-tests: ##@testing Run Functional tests. example: make unit-tests FILTER="FirstCest:canLogin"
	make run COM="vendor/bin/codecept run functional $(FILTER)"
.PHONY: functional-tests

codecept: ##@testing Execute Codeception command COM="generate:pageobject acceptance Login"
	make run COM="vendor/bin/codecept $(COM)"
.PHONY: codecept