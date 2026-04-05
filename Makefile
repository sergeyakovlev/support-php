.PHONY: help build clean c sh

COMPOSE_PROJECT_NAME=sergeyakovlev_support
COMPOSE_CONTAINER_NAME=php

DOCKER=docker
DOCKER_COMPOSE=docker compose

STYLE_GREEN=\033[32m
STYLE_DEFAULT=\033[0m

help: ## Print available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(STYLE_GREEN)make %-8s$(STYLE_DEFAULT) %s\n", $$1, $$2}'

build: ## Build a Docker image
	$(DOCKER_COMPOSE) build

clean: ## Remove Docker image
	$(DOCKER) rmi $(COMPOSE_PROJECT_NAME)-$(COMPOSE_CONTAINER_NAME)

c: ## Run composer with arbitrary parameters
	@if [ -z "$(filter-out $@,$(MAKECMDGOALS))" ]; then \
		echo "Usage: make c <composer-command>"; \
		echo "Examples:"; \
		echo "  make c install"; \
		echo "  make c test"; \
		echo "  make c require monolog/monolog"; \
		exit 1; \
	fi
	$(DOCKER_COMPOSE) run --rm $(COMPOSE_CONTAINER_NAME) composer $(filter-out $@,$(MAKECMDGOALS))

sh: ## Run the shell inside the container
	$(DOCKER_COMPOSE) run --rm $(COMPOSE_CONTAINER_NAME) sh
