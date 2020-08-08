.PHONY: ps
ps:
	docker ps

.PHONY: up
up:
	docker-compose up -d

.PHONY: build
build:
	docker-compose build --no-cache --force-rm

.PHONY: stop
stop:
	docker-compose stop

.PHONY: down
down:
	docker-compose down

.PHONY: restart
restart:
	@make down
	@make up

.PHONY: destroy
destroy:
	docker-compose down --rmi all --volumes

.PHONY: destroy-volumes
destroy-volumes:
	docker-compose down --volumes

.PHONY: web
web:
	docker-compose exec web bash

.PHONY: app
app:
	docker-compose exec app bash

.PHONY: asset
asset:
	docker-compose exec asset_watcher bash

.PHONY: mysql
mysql:
	docker-compose exec mysql bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'

.PHONY: redis
redis:
	docker-compose exec redis redis-cli
