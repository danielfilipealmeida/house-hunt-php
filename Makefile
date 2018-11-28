export PROJECT_IP = 127.0.0.1
export MEMCACHED_PORT = 11211

#constants
CONT_WEB=web
CONT_DB=db
DATABASE=house_hunt
DB_PASSWORD=passpass


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


setup-doctrine: ##@doctrine Drops and re-creates the database with data
	make dropdatabase
	make console COM="doctrine:database:create"
	#make console COM="make:migration"
	make console COM="doctrine:migrations:migrate"
.PHONY: setup-doctrine

create-migration: ##@doctrine Creates a new migration
	make console COM="make:migration"
.PHONY: create-migration

build: ##@servers Builds the project
	docker-compose build
.PHONY: build

setup: ##@servers Sets up all containers
	make build
	docker-compose up -d
	make start
	make setup-doctrine
.PHONY: setup

clean: ##@servers Tears down all containers
	docker-compose down
	docker-compose kill
.PHONY: clean

start: ##@servers Starts all containers
	docker-compose start
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

console: ##@servers Run a composer console inside the web container
	docker-compose exec $(CONT_WEB) bin/console $(COM)
.PHONY: console

apache-reload: ##@servers Reload the apache server
	make run COM="service apache2 reload"
.PHONY: apache-reload

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

dropdatabase: ##@database Deletes the database
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) -e "DROP DATABASE IF EXISTS $(DATABASE);"
.PHONY: dropdatabase


createdatabase: ##@database Creates the database
	docker-compose exec $(CONT_DB) mysql --password=$(DB_PASSWORD) -e "CREATE DATABASE IF NOT EXISTS $(DATABASE);"
.PHONY: createdatabase

fetchdata: ##@commands Use commands to fetch all data from APIs
	make console COM="idealista:fetch"
.PHONY: fetchdata
