# httpLogger
**Проект по логированию запросов**

При наличии в параметре или в заголовке http-запроса специального флага (**request-log**) этот запрос и результат его выполнения будут сохранены в лог.

Лог можно посмотреть на странице по адресу **/admin/http-log**

На этой странице лог будет показан в виде таблицы с основным полями.

Количество выводимых записей настраивается через  параметр `max_rows_per_page` в services.yaml. 
Есть пагинация (возможность ходить вперед-назад по страницам лога). 

Также можно фильтровать записи по IP клиента.

**Требования для запуска**
- **Symfony CLI** (https://symfony.com/download)
- **php** >=8.0.2
- **php-fpm**
- **composer**
- **docker-compose**

**Инструкция по запуску**
- mkdir httpLoggerTest
- cd httpLoggerTest
- git clone https://github.com/Svarog83/httpLogger .
- composer install
- docker-compose up -d
- php bin/console doctrine:migrations:migrate
- symfony server:ca:install (если раньше не ставился сертификат)
- symfony server:start -d
- symfony open:local (или открыть в браузере https://localhost:8000/)
- php bin/phpunit - чтобы запустить unit-тесты

**Инструкция по работе**
Чтобы добавить новую запись в лог, нужно к адресу в браузере добавить параметр ?request-log=1  
Например, https://localhost:8000/?request-log=1  
Лог можно смотреть по адресу https://localhost:8000/admin/http-log
