<?php

// Включаем строгую типизацию
declare(strict_types=1);

namespace Bank;

class MultiCurrencyAccount
{
    /**
     * Балансы по валютам
     *
     * @var array<string, Money>
     */
    private array $balances = [];

    private Currency $baseCurrency;
    private ExchangeRateProvider $exchangeRateProvider;

    public function __construct(Currency $baseCurrency, ExchangeRateProvider $exchangeRateProvider)
    {
        $this->baseCurrency = $baseCurrency;
        $this->exchangeRateProvider = $exchangeRateProvider;
        // Инициализируем баланс основной валюты
        $this->balances[$baseCurrency->getCode()] = new Money(0.0, $baseCurrency);
    }

    public function addCurrency(Currency $currency): void
    {
        $code = $currency->getCode();
        if (!isset($this->balances[$code])) {
            $this->balances[$code] = new Money(0.0, $currency);
        }
    }

    public function removeCurrency(string $code): void
    {
        $code = strtoupper($code);
        if ($code === $this->baseCurrency->getCode()) {
            throw new \InvalidArgumentException("Невозможно удалить основную валюту счета.");
        }
        unset($this->balances[$code]);
    }

    public function setBaseCurrency(string $code): void
    {
        $code = strtoupper($code);
        if (!isset($this->balances[$code])) {
            throw new \InvalidArgumentException("Валюта {$code} не подключена к счету.");
        }
        $this->baseCurrency = $this->balances[$code]->getCurrency();
    }

    public function getBaseCurrency(): Currency
    {
        return $this->baseCurrency;
    }

    public function deposit(float $amount, string $currencyCode): void
    {
        $currencyCode = strtoupper($currencyCode);
        $money = $this->getMoney($currencyCode);
        $money->add($amount);
    }

    /**
     * Списание средств из счета только из запрашиваемой валюты
     *
     * @param float $amount
     * @param string $currencyCode
     * @throws \InvalidArgumentException
     * @return void
     */
    public function withdraw(float $amount, string $currencyCode): void
    {
        $currencyCode = strtoupper($currencyCode);
        if ($amount < 0) {
            throw new \InvalidArgumentException("Сумма для списания не может быть отрицательной.");
        }

        $money = $this->getMoney($currencyCode);
        if ($money->getAmount() < $amount) {
            throw new \InvalidArgumentException("Недостаточно средств в валюте {$currencyCode} для списания.");
        }

        $money->subtract($amount);
    }

    public function getBalance(string $currencyCode): float
    {
        return $this->getMoney($currencyCode)->getAmount();
    }

    /**
     * Получить общий баланс в текущей основной валюте
     *
     * @return float
     */
    public function getTotalBalanceInBaseCurrency(): float
    {
        $totalBalance = 0.0;
        $baseCurrencyCode = $this->baseCurrency->getCode();
        
        foreach ($this->balances as $currencyCode => $money) {
            if ($currencyCode === $baseCurrencyCode) {
                $totalBalance += $money->getAmount();
            } else {
                $rate = $this->exchangeRateProvider->getRate($currencyCode, $baseCurrencyCode);
                $totalBalance += $money->getAmount() * $rate;
            }
        }
        
        return $totalBalance;
    }

    /**
     * Получить объект Money для заданной валюты
     *
     * @param string $currencyCode
     * @return Money
     */
    private function getMoney(string $currencyCode): Money
    {
        $currencyCode = strtoupper($currencyCode);
        if (!isset($this->balances[$currencyCode])) {
            throw new \InvalidArgumentException("Валюта {$currencyCode} не подключена к счету.");
        }
        return $this->balances[$currencyCode];
    }

    /**
     * Получить все балансы
     *
     * @return array<string, float>
     */
    public function getAllBalances(): array
    {
        $result = [];
        foreach ($this->balances as $code => $money) {
            $result[$code] = $money->getAmount();
        }
        return $result;
    }
}
