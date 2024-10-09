<?php
// Включаем строгую типизацию
declare(strict_types=1);

namespace Bank;

class Currency
{
    private string $code;
    private string $name;

    public function __construct(string $code, string $name)
    {
        $this->code = strtoupper($code);
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
