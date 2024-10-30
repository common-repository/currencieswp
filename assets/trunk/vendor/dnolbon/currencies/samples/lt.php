<?php
use Dnolbon\Currencies\Providers\Lt\EuCurrencyProvider;
use Dnolbon\Currencies\Providers\Lt\LtCurrencyProvider;

include_once dirname(__DIR__) . '/autoload.php';

$ltProvider = new LtCurrencyProvider();
$ltProvider->downloadCurrenciesForDate(new DateTime());
echo $ltProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $ltProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;

$euProvider = new EuCurrencyProvider();
$euProvider->downloadCurrenciesForDate(new DateTime());
echo $euProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $euProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
