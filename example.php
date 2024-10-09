<?php

require 'vendor/autoload.php';

use Bank\Currency;
use Bank\ExchangeRateProvider;
use Bank\MultiCurrencyAccount;

// Инициализация валют
$rub = new Currency('RUB', 'Российский рубль');
$usd = new Currency('USD', 'Доллар США');
$eur = new Currency('EUR', 'Евро');

// Инициализация провайдера курсов
$rateProvider = new ExchangeRateProvider();
// Получаем курсы обмена
try {
    $eurToRub = $rateProvider->getRate('EUR', 'RUB');
    $usdToRub = $rateProvider->getRate('USD', 'RUB');
    $eurToUsd = $rateProvider->getRate('EUR', 'USD');

    echo "Курс EUR к RUB: {$eurToRub}\n"; // Вывод: Курс EUR к RUB
    echo "Курс USD к RUB: {$usdToRub}\n"; // Вывод: Курс USD к RUB
    echo "Курс EUR к USD: {$eurToUsd}\n"; // Вывод: Курс EUR к USD
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage(); // Обработка ошибок, если курс не найден
}

// Создание мультивалютного счета с основной валютой RUB
$account = new MultiCurrencyAccount($rub, $rateProvider);

// Подключение дополнительных валют
$account->addCurrency($usd);
$account->addCurrency($eur);

// Пополнение счетов
$account->deposit(1000, 'RUB');
$account->deposit(50, 'EUR');
$account->deposit(50, 'USD');
echo "==========" . PHP_EOL;

// 2. Клиент хочет увидеть суммарный баланс счета в основной валюте, либо в валюте на
// Вывод балансов
$balance_rub = $account->getBalance('RUB'); // Получаем баланс в RUB
echo "Баланс: " . $balance_rub . " RUB" . PHP_EOL;
$balance_usd = $account->getBalance('USD'); // Получаем баланс в USD
echo "Баланс: " . $balance_usd . " USD" . PHP_EOL;
$balance_eur = $account->getBalance('EUR'); // Получаем баланс в EUR
echo "Баланс: " . $balance_eur . " EUR" . PHP_EOL;
echo "==========" . PHP_EOL;

// 3. Клиент совершает операции пополнения/списания со счета.
$account->deposit(1000, 'RUB');
$account->deposit(50, 'EUR');
$account->withdraw(10, 'USD');
// Вывод балансов
$balance_rub = $account->getBalance('RUB'); // Получаем баланс в RUB
echo "Баланс: " . $balance_rub . " RUB" . PHP_EOL;
$balance_usd = $account->getBalance('USD'); // Получаем баланс в USD
echo "Баланс: " . $balance_usd . " USD" . PHP_EOL;
$balance_eur = $account->getBalance('EUR'); // Получаем баланс в EUR
echo "Баланс: " . $balance_eur . " EUR" . PHP_EOL;
echo "==========" . PHP_EOL;

// 4. Банк меняет курс валюты для EUR и USD по отношению к рублю на 150 и 100
// соответственно
// Установим курсы обмена
$rateProvider->setRate('EUR/RUB', 150.0); // Устанавливаем курс EUR к RUB
$rateProvider->setRate('USD/RUB', 100.0); // Устанавливаем курс USD к RUB
// Получаем курсы обмена
try {
    $eurToRub = $rateProvider->getRate('EUR', 'RUB');
    $usdToRub = $rateProvider->getRate('USD', 'RUB');

    echo "Курс EUR к RUB: {$eurToRub}\n"; // Вывод: Курс EUR к RUB: 150
    echo "Курс USD к RUB: {$usdToRub}\n"; // Вывод: Курс USD к RUB: 100
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage(); // Обработка ошибок, если курс не найден
}
echo "==========" . PHP_EOL;

// 5. Клиент хочет увидеть суммарный баланс счета в рублях, после изменения курса валют.
// Получаем балансы по каждой валюте
$rubBalance = $account->getBalance('RUB');
$usdBalance = $account->getBalance('USD');
$eurBalance = $account->getBalance('EUR');
// Конвертируем балансы в рубли
$usdToRub = $rateProvider->getRate('USD', 'RUB');
$eurToRub = $rateProvider->getRate('EUR', 'RUB');
$totalBalanceInRub = $rubBalance + ($usdBalance * $usdToRub) + ($eurBalance * $eurToRub);
// Выводим суммарный баланс в рублях
echo "Суммарный баланс в RUB: " . $totalBalanceInRub . " RUB\n";
echo "==========" . PHP_EOL;


// 6. После этого клиент решает изменить основную валюту счета на EUR, и запрашивает текущий баланс/
// Установка основной валюты в EUR
$account->setBaseCurrency('EUR');
// Проверка, установилась ли основная валюта
echo "Основная валюта: " . $account->getBaseCurrency()->getCode() . PHP_EOL; // Должно вывести 'EUR'
// Получение текущего баланса счета в новой основной валюте EUR, в задании не указано что нужно вывести общий баланс, поэтому выводим баланс в валюте EUR
echo "Текущий баланс: " . $account->getBalance($account->getBaseCurrency()->getCode()) . PHP_EOL;
echo "==========" . PHP_EOL;

// 7. Чтобы избежать дальнего ослабления рубля клиент решает сконвертировать
// рублевую часть счета в EUR, и запрашивает баланс
// Списание с баланса
try {
    $densredstva = 1000; // RUB 
    $account->withdraw($densredstva, 'RUB'); // Списываем 1000 RUB
    echo "Списание прошло успешно. Новый баланс RUB: " . $account->getBalance('RUB') . "\n";
    $densredstva_eur = $densredstva / $eurToRub;
    $account->deposit($densredstva_eur, 'EUR');
} catch (\InvalidArgumentException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
echo "Текущий баланс: " . $account->getBalance($account->getBaseCurrency()->getCode()) . PHP_EOL;


// Вывод балансов
print_r($account->getAllBalances());

// 8. Банк меняет курс валюты для EUR к RUB на 120
$rateProvider->setRate('EUR/RUB', 120.0); // Устанавливаем курс EUR к RUB


// 9. После изменения курса клиент проверяет, что баланс его счета не изменился
echo "Текущий баланс: " . $account->getBalance($account->getBaseCurrency()->getCode()) . PHP_EOL;


// 10. Банк решает, что не может больше поддерживать обслуживание следующих валют EUR и USD. Согласовывает с клиентом изменение основной валюты счета на RUB, с конвертацией балансов неподдерживаемых валют.
// Установка основной валюты в RUB
$account->setBaseCurrency('RUB');
$account->removeCurrency('EUR');
$account->removeCurrency('USD');
// Список поддерживаепмых валют
print_r($account->getSupportedCurrencies());


// Вывод балансов
print_r($account->getAllBalances());
