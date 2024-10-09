## Задание:
Задание «Мультивалютный банковский счет»
Разработать класс (пакет классов), реализующих функционал банковского
мультивалютного счета.
### Требования к реализации:
- 1. Мультивалютный счет обслуживает сбережения в нескольких валютах, которые
можно подключать и отключать от счета динамически. Пример валют:
российский рубль (RUB), доллар США (USD), евро (EUR).
- 2. Одна из валют счета является основной (для разных счетов может быть
разной).
- 3. Мультивалютный счет поддерживает операции пополнения/списания в
конкретной валюте.
- 4. При попытке списания в конкретной валюте, нельзя списать больше количества
этой валюты на счете. Например, на счете 1000 RUB и 10 USD, при попытке
списать 11 USD, нельзя списать 10 USD из долларовой части счета, а
оставшуюся с рублевой.
- 5. Курс валют со временем может изменяться. Базовый курс валют: EUR/RUB =
80, USD/RUB = 70, EUR/USD = 1
- 6. Клиент должен иметь возможность изменить основную валюту счета.              
### Требования к коду:
- 1. Использование ООП, и дробление на функциональные классы.
- 2. Использование типизации (можно использовать все возможности PHP 8.1).
- 3. Основная бизнес-логика должна быть покрыта тестами.
- 4. Соответствие кода PHP Standards Recommendations.
### Тестовый сценарий:
1. Клиент открывает мультивалютный счет, включающий сбережения в 3-х валютах с
основной валютой российский рубль, и пополняет его следующими суммами: 1000
RUB, 50 EUR, 40 USD.<br>
Счет = Банк->ОткрытьНовыйСчет()<br>
Счет->ДобавитьВалюту(RUB)<br>
Счет->ДобавитьВалюту(EUR)<br>
Счет->ДобавитьВалюту(USD)<br>
Счет->УстановитьОсновнуюВалюту(RUB)<br>
Счет->СписокПоддеживаемыхВалют() // [RUB, EUR, USD]<br>
Счет->ПополнитьБаланс(RUB(1000))<br>
Счет->ПополнитьБаланс(EUR(50))<br>
Счет->ПополнитьБаланс(USD(50))<br>

2. Клиент хочет увидеть суммарный баланс счета в основной валюте, либо в валюте на
выбор.<br>
Счет->ПолучитьБаланс() => xxxxx RUB<br>
Счет->ПолучитьБаланс(USD) => xxxxx USD<br>
Счет->ПолучитьБаланс(EUR) => xxxxx EUR<br>

3. Клиент совершает операции пополнения/списания со счета.<br>
Счет->ПополнитьБаланс(RUB(1000))<br>
Счет->ПополнитьБаланс(EUR(50))<br>
Счет->СписатьСБаланса(USD(10))<br>

4. Банк меняет курс валюты для EUR и USD по отношению к рублю на 150 и 100
соответственно<br>
EUR->УстановитьКурсОбменаВалюты(RUR, 150)<br>
USD->УстановитьКурсОбменаВалюты(RUR, 100)<br>

5. Клиент хочет увидеть суммарный баланс счета в рублях, после изменения курса
валют.<br>
Счет->ПолучитьБаланс() => xxxxx RUB<br>

6. После этого клиент решает изменить основную валюту счета на EUR, и запрашивает
текущий баланс<br>
Счет->УстановитьОсновнуюВалюту(EUR)<br>
Счет->ПолучитьБаланс() => xxx EUR<br>
7. Чтобы избежать дальнего ослабления рубля клиент решает сконвертировать
рублевую часть счета в EUR, и запрашивает баланс<br>
ДенежныеСредства = Счет->СписатьСБаланса(RUB(1000))<br>
Счет->ПополнитьБаланс(EUR(ДенежныеСредства))<br>
Счет->ПолучитьБаланс() => xxx EUR<br>
8. Банк меняет курс валюты для EUR к RUB на 120<br>
EUR->УстановитьКурсОбменаВалюты(RUR, 120)<br>
9. После изменения курса клиент проверяет, что баланс его счета не изменился
Счет->ПолучитьБаланс() => xxx EUR<br>
10. Банк решает, что не может больше поддерживать обслуживание следующих валют
EUR и USD. Согласовывает с клиентом изменение основной валюты счета на RUB, с
конвертацией балансов неподдерживаемых валют.<br>
Счет->УстановитьОсновнуюВалюту(RUB)<br>
Счет->ОтключитьВалюту(EUR)<br>
Счет->ОтключитьВалюту(USD)<br>
Счет->СписокПоддеживаемыхВалют() // [RUB]<br>
Счет->ПолучитьБаланс() => xxxxx RUB<br>
### Приемка
Для приемки выложить код в одном из публичном git-репозитории (GitHub, GitLab) и
предоставить к нему доступ. В процессе выполнения задания можно задавать
уточняющие вопросы.<br>
Наличие покрытия тестами будет плюсом.<br>
### Оценка результата
При оценке будет учитываться как сам код, так и структура полученного проекта,
использование типизации, процент покрытия кода, история коммитов

---


## Решение:
Разработка Мультивалютного Банковского Счета на PHP<br>

### Классы и их описание
Currency: Представляет валюту с кодом и названием.<br>
ExchangeRateProvider: Управляет курсами валют и предоставляет актуальные курсы.<br>
Money: Представляет сумму денег в определенной валюте.<br>
MultiCurrencyAccount: Основной класс счета, который управляет балансами в разных валютах, основной валютой и операциями пополнения/списания.<br>
Структура проекта:
```css
multicurrency/
├── src/
│   ├── Currency.php
│   ├── ExchangeRateProvider.php
│   ├── Money.php
│   ├── MultiCurrencyAccount.php
├── tests/
│   ├── MultiCurrencyAccountTest.php
├── vendor/
│   └── ... (установленные зависимости)
│── composer.json
│── README.md
│── phpunit.md


```
### Установка и запуск тестов
Установка зависимостей через Composer

Создайте composer.json:
```json
{
    "name": "bank/multicurrency",
    "description": "Мультивалютный счет.",
    "type": "project",
    "require": {
        "php": "^8.1"
    },
    "require-dev": {
        "phpunit/phpunit": "10.5.35",
        "friendsofphp/php-cs-fixer": "^3.64"
    },
    "autoload": {
        "psr-4": {
            "Bank\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Bank\\Tests\\": "tests/"
        }
    },
    "scripts": {
        "test": "phpunit"
    },
    "license": "MIT", 
    "minimum-stability": "stable"
}
```
Затем выполните команды:
```bash
composer install
```

Запуск тестов:<br>
Добавьте в composer.json скрипт для тестов:
```json
"scripts": {
    "test": "phpunit"
}
```
Запустите тесты:
```bash
composer test
```

Запуск PHP CS Fixer в режиме "dry-run" (т.е. без внесения изменений), чтобы увидеть, какие изменения будут предложены. <br>
Поменяйте C:\OSPanel\home\multicurrency\bank\multicurrency на свой путь.
```bash
vendor/bin/php-cs-fixer fix C:\OSPanel\home\multicurrency\bank\multicurrency --dry-run --diff
```