export PROJECT_IP = 127.0.0.1
export MEMCACHED_PORT = 11211

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


start-mariadb: ##@servers Starts the database server
	mysqld &
.PHONY: start-mariadb

stop-mariadb: ##@servers kills running database server
	kill -9 $$(pgrep -f mysqld)
.PHONY: stop-mariadb

start-php-server: ##@servers Starts the Symfony webserver
	php bin/console server:start &
.PHONY: start-php-server

stop-php-server: ##@servers kills running Symfony webserver
	kill -9 $$(pgrep -f php bin/console server:start)
.PHONY: stop-php-server

start-memcached: ##@servers Starts the Memcached server
	memcached -d -p $(MEMCACHED_PORT)
.PHONY: start-memcached

stop-memcached: ##@servers kills running Memcached server
	kill -9 $$(pgrep -f memcached)
.PHONY: stop-memcached

start: ##@servers Starts all servers
	make start-mariadb
	make start-php-server
	make start-memcached
.PHONY: start

stop: ##@servers Kills all servers
	make stop-memcached
	make stop-mariadb
	make stop-php-server
.PHONY: stop

unit-test: ##@testing Starts all servers
	./bin/phpunit
.PHONY: unit-test


