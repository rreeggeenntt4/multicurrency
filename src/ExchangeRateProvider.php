<?php

// Включаем строгую типизацию
declare(strict_types=1);

namespace Bank;

class ExchangeRateProvider
{
    /**
     * Ключ: "BASE/QUOTE", значение: курс
     *
     * @var array<string, float>
     */
    private array $rates = [];

    public function __construct()
    {
        // Инициализация базовых курсов
        $this->setRate('EUR/RUB', 80.0);
        $this->setRate('USD/RUB', 70.0);
        $this->setRate('EUR/USD', 1.0);
    }

    public function setRate(string $pair, float $rate): void
    {
        $pair = strtoupper($pair);
        $this->rates[$pair] = $rate;
        // Автоматически устанавливаем обратный курс
        [$base, $quote] = explode('/', $pair);
        if ($rate != 0.0) {
            $inverseRate = 1 / $rate;
            $this->rates["{$quote}/{$base}"] = $inverseRate;
        }
    }

    public function getRate(string $base, string $quote): float
    {
        $pair = strtoupper("{$base}/{$quote}");
        if (isset($this->rates[$pair])) {
            return $this->rates[$pair];
        }
        throw new \InvalidArgumentException("Курс для пары {$pair} не найден.");
    }
}
