<?php
namespace Dnolbon\CurrenciesWp\Shortcodes;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Wordpress\Shortcode\ShortcodeAbstract;

class CurrencieconvertShortcode extends ShortcodeAbstract
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'currencieconvert';
    }

    /**
     * @param array $params
     * @return mixed
     */
    public function render($params)
    {
        if (array_key_exists('from', $params) && array_key_exists('to', $params)) {
            $from = $params['from'];
            $to = $params['to'];
            $decimals = array_key_exists('decimals', $params) ? (int)$params['decimals'] : 2;

            return CurrenciesWp::getRate($from, $to, $decimals);
        }
        return '';
    }
}
