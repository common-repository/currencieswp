<?php
use Dnolbon\Currencies\Providers\Lv\LvCurrencyProvider;

include_once dirname(__DIR__) . '/autoload.php';

$lvProvider = new LvCurrencyProvider();
$lvProvider->downloadCurrenciesForDate(new DateTime());
echo $lvProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $lvProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
