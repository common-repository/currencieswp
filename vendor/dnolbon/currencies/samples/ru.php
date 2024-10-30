<?php
use Dnolbon\Currencies\Providers\Ru\CbrCurrencyProvider;

include_once dirname(__DIR__).'/autoload.php';

$cbrProvider = new CbrCurrencyProvider();
$cbrProvider->downloadCurrenciesForDate(new DateTime());
echo $cbrProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $cbrProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
