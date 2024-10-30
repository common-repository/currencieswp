<?php
use Dnolbon\Currencies\Providers\Ee\EeCurrencyProvider;

include_once dirname(__DIR__) . '/autoload.php';

$eeProvider = new EeCurrencyProvider();
$eeProvider->downloadCurrenciesForDate(new DateTime());
echo $eeProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $eeProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
