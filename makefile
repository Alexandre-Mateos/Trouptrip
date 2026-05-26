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