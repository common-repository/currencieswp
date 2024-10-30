<?php
use Dnolbon\Currencies\Providers\Eu\EuCurrencyProvider;

include_once dirname(__DIR__) . '/autoload.php';

$eeProvider = new EuCurrencyProvider();
$eeProvider->downloadCurrenciesForDate(new DateTime());
echo $eeProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $eeProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
