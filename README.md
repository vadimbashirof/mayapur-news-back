cp .env.example .env
Обязательно поменяйте там UID GID и USERNAME остальное моно оставить

Узнать UID и GID можно так
id -u
id -g

Потом так
docker-compose up

потом зайдете внутрь контейнера
docker-compose exec php-fpm bash

Внутри контейнера запустите
composer install
php artisan migrate
php artisan db:seed --class=AdminSeeder
