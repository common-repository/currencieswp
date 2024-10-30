<?php
use Dnolbon\Currencies\Providers\By\ByCurrencyProvider;

include_once dirname(__DIR__).'/autoload.php';

$byProvider = new ByCurrencyProvider();
$byProvider->downloadCurrenciesForDate(new DateTime());
echo $byProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $byProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
