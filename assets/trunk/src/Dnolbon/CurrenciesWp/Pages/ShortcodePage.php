<?php
namespace Dnolbon\CurrenciesWp\Pages;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Twig\Twig;

class ShortcodePage
{
    public function render()
    {
        CurrenciesWp::downloadForToday();

        $template = Twig::getInstance()->getTwig()->load('shortcode.html');
        echo $template->render([
            'list' => CurrenciesWp::getCurrencies(),
            'from' => filter_has_var(INPUT_POST, 'from') ? filter_input(INPUT_POST, 'from') : '',
            'to' => filter_has_var(INPUT_POST, 'to') ? filter_input(INPUT_POST, 'to') : '',
            'decimals' => filter_has_var(INPUT_POST, 'decimals') ? filter_input(INPUT_POST, 'decimals') : 2
        ]);
    }
}
