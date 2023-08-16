cp .env.example .env  
Обязательно поменяйте там UID GID и USERNAME остальное моно оставить  

Узнать UID и GID можно так  
```id -u```  
```id -g```  

Потом так  
```docker-compose -f docker-compose-dev.yml up```   

Потом зайдете внутрь контейнера  
```docker-compose -f docker-compose-dev.yml exec php-fpm bash```  

Внутри контейнера запустите  
```composer install```  
```php artisan migrate```  
```php artisan db:seed --class=AdminSeeder```
