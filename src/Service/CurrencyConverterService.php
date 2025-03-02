<?php

declare(strict_types=1);

namespace App\Service;

use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Exchange\FixedExchange;
use Money\Money;

class CurrencyConverterService
{
    private Converter $converter;

    public function __construct()
    {
        $this->converter = $this->createCurrencyConverter();
    }

    /**
     * @param string $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return string
     */
    public function convertCurrency(string $amount, string $fromCurrency, string $toCurrency): string
    {
        $money = new Money($amount, new Currency($fromCurrency));
        $convertedMoney = $this->converter->convert($money, new Currency($toCurrency));

        return $convertedMoney->getAmount();
    }

    /**
     * @return array[]
     */
    private function getExchangeRates(): array
    {
        // имитация api сервиса курса валют
        return [
            'USD' => [
                'RUB' => '75.0',
            ],
            'RUB' => [
                'USD' => '0.013',
            ],
        ];
    }

    /**
     * @return Converter
     */
    private function createCurrencyConverter(): Converter
    {
        $currencies = new ISOCurrencies();
        $exchange = new FixedExchange($this->getExchangeRates());

        return new Converter($currencies, $exchange);
    }
}