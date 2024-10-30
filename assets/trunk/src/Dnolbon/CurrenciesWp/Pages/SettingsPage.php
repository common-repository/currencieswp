<?php
namespace Dnolbon\CurrenciesWp\Pages;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Twig\Twig;

class SettingsPage
{
    public function render()
    {
        $this->checkForAction();

        $list = json_decode(file_get_contents(CurrenciesWpRoot . '/assets/banklist.json'), true);

        $currentBank = get_option(CurrenciesWpName . '_bank', '');

        $template = Twig::getInstance()->getTwig()->load('settings.html');
        echo $template->render(['list' => $list, 'current' => $currentBank]);
    }

    public function checkForAction()
    {
        if (filter_input(INPUT_POST, 'dnolbon_currencies_wp_action') === 'settings') {
            $currentBank = get_option(CurrenciesWpName . '_bank', '');
            $newBank = sanitize_text_field(filter_input(INPUT_POST, 'bank'));

            if ($currentBank !== $newBank) {
                global $wpdb;
                $sql = 'TRUNCATE TABLE ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ';';
                $wpdb->query($sql);
            }

            update_option(CurrenciesWpName . '_bank', $newBank);
        }
    }
}
