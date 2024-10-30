<?php
namespace Dnolbon\CurrenciesWp\Pages;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\Twig\Twig;
use Dnolbon\WooCommerce\WooCommerce;
use Dnolbon\Wordpress\Db\Db;

class SettingsPage
{
    public function render()
    {
        $this->checkForAction();

        $list = json_decode(file_get_contents(CurrenciesWpRoot . '/assets/banklist.json'), true);

        $currentBank = get_option(CurrenciesWpName . '_bank', '');

        $showInWooCommerce = get_option(CurrenciesWpName . '_show_in_woocommerce', false);
        $wooCommerceCurrency = get_option(CurrenciesWpName . '_woocommerce_currency', '');

        $template = Twig::getInstance()->getTwig()->load('settings.html');
        echo $template->render(
            [
                'prefix' => CurrenciesWpName,
                'list' => $list,
                'current' => $currentBank,
                'woocommerceActive' => WooCommerce::isActive(),
                'showInWooCommerce' => $showInWooCommerce,
                'wooCommerceCurrency' => $wooCommerceCurrency,
                'currencies' => CurrenciesWp::getCurrencies()
            ]
        );
    }

    public function checkForAction()
    {
        if (filter_input(INPUT_POST, 'dnolbon_currencies_wp_action') === 'settings') {
            $currentBank = get_option(CurrenciesWpName . '_bank', '');
            $newBank = sanitize_text_field(filter_input(INPUT_POST, 'bank'));

            if ($currentBank !== $newBank) {
                $sql = 'TRUNCATE TABLE ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ';';
                Db::getInstance()->getDb()->query($sql);
            }

            update_option(CurrenciesWpName . '_bank', $newBank);
        } else if (filter_input(INPUT_POST, 'dnolbon_currencies_wp_action') === 'settings_woocommerce') {
            update_option(
                CurrenciesWpName . '_show_in_woocommerce',
                (int)filter_input(INPUT_POST, 'show_in_woocommerce')
            );
            update_option(
                CurrenciesWpName . '_woocommerce_currency',
                filter_input(INPUT_POST, 'woocommerce_currency')
            );
        }
    }
}
