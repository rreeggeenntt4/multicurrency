<?php

// Включаем строгую типизацию
declare(strict_types=1);

namespace Bank\Tests;

use PHPUnit\Framework\TestCase;
use Bank\Currency;
use Bank\ExchangeRateProvider;
use Bank\MultiCurrencyAccount;

final class MultiCurrencyAccountTest extends TestCase
{
    private MultiCurrencyAccount $account;
    private Currency $rub;
    private Currency $usd;
    private Currency $eur;
    private ExchangeRateProvider $rateProvider;

    protected function setUp(): void
    {
        $this->rub = new Currency('RUB', 'Российский рубль');
        $this->usd = new Currency('USD', 'Доллар США');
        $this->eur = new Currency('EUR', 'Евро');

        $this->rateProvider = new ExchangeRateProvider();
        $this->account = new MultiCurrencyAccount($this->rub, $this->rateProvider);
        $this->account->addCurrency($this->usd);
        $this->account->addCurrency($this->eur);
    }

    public function testDeposit(): void
    {
        $this->account->deposit(1000, 'RUB');
        $this->account->deposit(10, 'USD');
        $this->account->deposit(5, 'EUR');

        $this->assertEquals(1000, $this->account->getBalance('RUB'));
        $this->assertEquals(10, $this->account->getBalance('USD'));
        $this->assertEquals(5, $this->account->getBalance('EUR'));
    }

    public function testWithdrawWithinCurrency(): void
    {
        $this->account->deposit(1000, 'RUB');
        $this->account->deposit(10, 'USD');

        $this->account->withdraw(5, 'USD');

        $this->assertEquals(1000, $this->account->getBalance('RUB'));
        $this->assertEquals(5, $this->account->getBalance('USD'));
    }

    public function testWithdrawExceedingCurrency(): void
    {
        $this->account->deposit(1000, 'RUB');
        $this->account->deposit(10, 'USD');

        // Попытка списать 11 USD, при наличии только 10 USD
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Недостаточно средств в валюте USD для списания.");

        $this->account->withdraw(11, 'USD');
    }

    public function testChangeBaseCurrency(): void
    {
        $this->account->deposit(1000, 'RUB');
        $this->account->deposit(10, 'USD');
        $this->account->deposit(5, 'EUR');

        $this->account->setBaseCurrency('USD');

        $this->assertEquals('USD', $this->account->getBaseCurrency()->getCode());

        // Теперь попробуем списать 11 EUR, но у нас есть только 5 EUR
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Недостаточно средств в валюте EUR для списания.");

        $this->account->withdraw(11, 'EUR');

        // Убедимся, что баланс не изменился
        $this->assertEquals(1000, $this->account->getBalance('RUB'));
        $this->assertEquals(10, $this->account->getBalance('USD'));
        $this->assertEquals(5, $this->account->getBalance('EUR'));
    }

    public function testWithdrawNegativeAmount(): void
    {
        $this->account->deposit(1000, 'RUB');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Сумма для списания не может быть отрицательной.");

        $this->account->withdraw(-100, 'RUB');
    }
}
