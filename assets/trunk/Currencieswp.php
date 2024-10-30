<?php
/*
Plugin Name: CurrenciesWp
Description: Currency rates
Version: 1.0.1
Author: Dnolbon
Author URI: https://dnolbon.com
*/
use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Twig\Twig;

include __DIR__ . '/vendor/autoload.php';

// defines
define('CurrenciesWpName', 'CurrenciesWp');
define('CurrenciesWpRoot', __DIR__);

// Twig
Twig::getInstance()->setTemplatePath(__DIR__ . '/templates');

// load
$main = new CurrenciesWp();

// tmp
add_shortcode('currencieconvert', function ($params) {
    if (array_key_exists('from', $params) && array_key_exists('to', $params)) {
        $from = $params['from'];
        $to = $params['to'];
        $decimals = array_key_exists('decimals', $params) ? (int)$params['decimals'] : 2;

        return CurrenciesWp::getRate($from, $to, $decimals);
    }
});