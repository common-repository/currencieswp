<?php
use Dnolbon\Currencies\Providers\Ua\PrivateBankCurrencyProvider;

include_once dirname(__DIR__) . '/autoload.php';

$uaPrivateBankProvider = new PrivateBankCurrencyProvider();
$uaPrivateBankProvider->downloadCurrenciesForDate(new DateTime('2016-12-25'));
echo $uaPrivateBankProvider->convert('EUR', 'USD', 10);
echo PHP_EOL;
echo $uaPrivateBankProvider->convert('EUR', 'RUB', 10);
echo PHP_EOL;
