<?php

// Включаем строгую типизацию
declare(strict_types=1);

namespace Bank;

class Money
{
    private float $amount;
    private Currency $currency;

    public function __construct(float $amount, Currency $currency)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Сумма не может быть отрицательной.");
        }
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function add(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Сумма для пополнения не может быть отрицательной.");
        }
        $this->amount += $amount;
    }

    public function subtract(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Сумма для списания не может быть отрицательной.");
        }
        if ($amount > $this->amount) {
            throw new \InvalidArgumentException("Недостаточно средств для списания.");
        }
        $this->amount -= $amount;
    }
}
