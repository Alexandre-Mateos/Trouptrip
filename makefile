back:
	docker compose exec -it php bash

logs-back:
	docker compose logs php -f

front:
	docker compose exec -it node-front bash

logs-front:
	docker compose logs node-front -f
