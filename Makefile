INJECT_HOST = DB_HOST=127.0.0.1
BACKEND_FOLDER = backend
BACKEND_SEEDER = DeliveriesSeeder

help: ## Show this help message
	@printf "$(COLOR_MESSAGE_BOLD)VueBI - Makefile help.\n$(COLOR_RESET)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

up: ## Starts all containers and runs the application
	docker compose up

stop: ## Stops all running containers
	docker compose stop

down: ## Stops and removes all containers
	docker compose down

prepare-database: ## Perform the database migration
	cp ${BACKEND_FOLDER}/.env.example ${BACKEND_FOLDER}/.env
	docker compose up -d mysql
	sleep 20
	${INJECT_HOST} php ${BACKEND_FOLDER}/artisan migrate
	${INJECT_HOST} php ${BACKEND_FOLDER}/artisan db:seed --class=${BACKEND_SEEDER}

more-seed: ## Seed more data in Database
	${INJECT_HOST} php ${BACKEND_FOLDER}/artisan db:seed --class=${BACKEND_SEEDER}
