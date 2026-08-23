back:
	docker compose exec -it php bash

logs-back:
	docker compose logs php -f

front:
	docker compose exec -it node-front bash

logs-front:
	docker compose logs node-front -f

send-mails:
	docker compose exec -it php php bin/console messenger:consume async

cache-clear:
	docker compose exec -it php php bin/console cache:clear

fixtures:
	docker compose exec -it php php bin/console doctrine:fixtures:load

fixtures-test:
	docker compose exec -T php php bin/console doctrine:database:drop --force --env=test --if-exists
	docker compose exec -T php php bin/console doctrine:database:create --env=test
	docker compose exec -T php php bin/console doctrine:migrations:migrate --no-interaction --env=test
	docker compose exec -T php php bin/console doctrine:fixtures:load --no-interaction --env=test