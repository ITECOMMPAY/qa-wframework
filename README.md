# qa-wframework

Это пример нашего фреймворка для тестирования веб-приложений.

Он пока не пригоден для удобного использования за рамками наших проектов и выложен здесь, как сопроводительный материал для доклада на SQA Days 27.

## Установка

* Фреймворк работает только под GNU/Linux
* Для его работы требуется PHP 7.4 или выше, composer и бинарные расширения PHP (в одних дистрибутивах они ставятся отдельными пакетами, в других - вкомпилированы в PHP, в третьих - нужно самому собрать): `ext-curl, ext-dom, ext-imagick, ext-json, ext-libxml, ext-mbstring, ext-pcre, ext-phar, ext-posix, ext-simplexml, ext-tokenizer, ext-xml, ext-xmlwriter, ext-zip`
* В корне где лежит composer.json нужно выполнить `composer install`

## Описание примера тестов

Тестовый проект лежит в каталоге `./tests/dodge`

Настройки проекта находятся в файле `./tests/dodge/codeception.yml`

Пример теста находится в файле `./tests/dodge/Tests/exampleCest.php`

Тест представляет собой последовательность шагов.

Шаги лежат в StepObject'ах в каталоге `./tests/dodge/_support/Helper/Steps`

Шаги содержат в себе ссылки на Блоки. Блоки лежат в каталоге `./tests/dodge/_support/Helper/Blocks`

Блоки состоят из элементов. Элементы лежат в каталоге `./tests/dodge/_support/Helper/Elements`

Помимо тестового примера в каталоге cases находятся файлы storeShotsCest.php и selfCheckCest.php. storeShotsCest.php - загружает скриншоты элементов в S3. selfCheckCest.php - перебирает все блоки проекта и валидирует их локаторы.

В остальном, тестовый проект представляет собой обычный проект на Codeception.


## Запуск примеров

Запустить пример теста можно командой: `./vendor/bin/codecept run webui exampleCest -c ./tests/dodge --env dodge-loc,dodge-loc-chrome,dodge-loc-1920`

## Создание нового проекта

Новый тестовый проект можно создать с помощью визарда: `./vendor/bin/codecept init --path ./tests WProject`

## Архитектура

Подробное описание архитектуры см. в файле ARCHITECTURE.md
