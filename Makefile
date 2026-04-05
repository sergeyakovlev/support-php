.DEFAULT_GOAL := help
.PHONY: help build clean sh install update cs cs\:fix qa qa\:fix rector rector\:fix stan test

SERVICE := php

DOCKER := docker
DOCKER_COMPOSE := docker compose

# Do not allocate a TTY when the input is not a terminal (CI, pipes)
RUN := $(DOCKER_COMPOSE) run --rm $(shell [ -t 0 ] || echo -T) $(SERVICE)

STYLE_GREEN := \033[32m
STYLE_DEFAULT := \033[0m

# A colon in a target name must be escaped with a backslash (cs\:fix); the escaping is removed in the output
help: ## Print available commands
	@grep -E '^[a-zA-Z_:\\-]+: ## ' $(MAKEFILE_LIST) | awk -F': ## ' '{gsub(/\\/, "", $$1); printf "$(STYLE_GREEN)make %-10s$(STYLE_DEFAULT) %s\n", $$1, $$2}'

build: ## Build the Docker image
	$(DOCKER_COMPOSE) build

clean: ## Remove the containers, the volumes and the Docker image
	$(DOCKER_COMPOSE) down --volumes --remove-orphans
	-$(DOCKER) rmi $$($(DOCKER_COMPOSE) config --images)

sh: ## Run the shell inside the container
	$(RUN) sh

install: ## Install the dependencies (composer install)
	$(RUN) composer install

update: ## Update the dependencies (composer update)
	$(RUN) composer update

cs: ## Check the code style with ECS (composer cs)
	$(RUN) composer cs

cs\:fix: ## Fix the code style with ECS (composer cs:fix)
	$(RUN) composer cs:fix

qa: ## Run Rector, ECS, PHPStan and the tests (composer qa)
	$(RUN) composer qa

qa\:fix: ## Run the same, but fix what can be fixed (composer qa:fix)
	$(RUN) composer qa:fix

rector: ## Check the code with Rector, no changes are made (composer rector)
	$(RUN) composer rector

rector\:fix: ## Refactor the code with Rector (composer rector:fix)
	$(RUN) composer rector:fix

stan: ## Run PHPStan (composer stan)
	$(RUN) composer stan

test: ## Run the tests (composer test)
	$(RUN) composer test
