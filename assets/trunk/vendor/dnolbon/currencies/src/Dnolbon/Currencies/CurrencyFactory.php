<?php
namespace Dnolbon\Currencies;

class CurrencyFactory
{
    /**
     * @param string $name
     * @param string $code
     * @param float $rate
     * @return Currency
     */
    public static function create($name, $code, $rate)
    {
        $currency = new Currency();
        $currency->setName($name);
        $currency->setCode($code);
        $currency->setRate($rate);
        return $currency;
    }
}
