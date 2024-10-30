<?php
/*
Plugin Name: CurrenciesWp
Description: Currency rates
Version: 1.0.3
Author: Dnolbon
Author URI: https://dnolbon.com
*/
use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\CurrenciesWp\Shortcodes\CurrencieconvertShortcode;
use Dnolbon\Twig\Twig;

include __DIR__ . '/vendor/autoload.php';

// defines
define('CurrenciesWpName', 'CurrenciesWp');
define('CurrenciesWpRoot', __DIR__);

// Twig
Twig::getInstance()->setTemplatePath(__DIR__ . '/templates');

// load
$main = new CurrenciesWp(__FILE__, basename(__FILE__));

// shortcodes
new CurrencieconvertShortcode();