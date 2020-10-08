# adaptive app project
Шаблон адаптивного приложения

#### Стек технологий
1. PHP - 7.2.22
2. DB - 10.4.6-MariaDB



***

#### Команды
1. Запуск миграций `./vendor/bin/doctrine-migrations migrate`
2. Запуск скрипта `php path/to/web/server/testParser/app/console/script.php`
3. Проверка кода  `.\vendor\bin\phpcs --standard=Doctrine app/common/HttpClient/HttpClient.php`
4. Правка кода  `.\vendor\bin\phpcbf --standard=Doctrine app/common/HttpClient/HttpClient.php`
5. Миграции ОРМ `./vendor/bin/doctrine orm:schema-tool:update --force`
