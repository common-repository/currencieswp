<?php
namespace Dnolbon\CurrenciesWp;

use DateTime;
use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\CurrenciesWp\Pages\ArchivePage;
use Dnolbon\CurrenciesWp\Pages\SettingsPage;
use Dnolbon\CurrenciesWp\Pages\ShortcodePage;
use Dnolbon\Wordpress\MainClassInterface;
use Dnolbon\Wordpress\MenuFactory;

class CurrenciesWp implements MainClassInterface
{
    /**
     * DB version
     */
    const VERSION = 1;

    /**
     * Main table name
     */
    const TABLECURRENCIES = 'dnolbon_currencies';

    /**
     * CurrenciesWp constructor.
     */
    public function __construct()
    {
        register_activation_hook(__FILE__, [$this, 'install']);
        register_deactivation_hook(__FILE__, [$this, 'uninstall']);
        add_action('admin_menu', [$this, 'registerMenu']);

        $this->checkVersion();
    }

    /**
     * Check version for database upgrade
     */
    public function checkVersion()
    {
        $optionName = CurrenciesWpName . '_version';

        if (\get_option($optionName, 0) < CurrenciesWp::VERSION) {
            $this->install();
            update_option($optionName, CurrenciesWp::VERSION);
        }
    }

    /**
     * Database install or upgrade script
     */
    public function install()
    {
        global $wpdb;

        $charsetCollate = '';
        if ($wpdb->charset !== null && $wpdb->charset !== '') {
            $charsetCollate = "DEFAULT CHARACTER SET {$wpdb->charset}";
        }
        if ($wpdb->collate !== null && $wpdb->collate !== '') {
            $charsetCollate .= " COLLATE {$wpdb->collate}";
        }

        $tableName = $wpdb->prefix . CurrenciesWp::TABLECURRENCIES;
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (" .
            "`id` int(20) unsigned NOT NULL AUTO_INCREMENT," .
            "`currency` varchar(5) NOT NULL," .
            "`date` DATE NOT NULL," .
            "`rate` DOUBLE (10,4) NOT NULL DEFAULT 0," .
            "PRIMARY KEY (`id`)" .
            ") {$charsetCollate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    public static function getCurrencies()
    {
        global $wpdb;

        $list = $wpdb->get_results($wpdb->prepare(
            'select * from ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s order by currency asc',
            date('Y-m-d')
        ));
        return $list;
    }

    public static function getRate($from, $to, $decimals = 2)
    {
        global $wpdb;

        $isExists = $wpdb->get_row($wpdb->prepare(
            'select count(*) as c from ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s',
            date('Y-m-d')
        ));

        if (!$isExists || (int)$isExists->c === 0) {
            self::downloadForToday();
        }

        $rateFrom = $wpdb->get_row($wpdb->prepare(
            'select rate from ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
            date('Y-m-d'),
            $from
        ));

        $rateTo = $wpdb->get_row($wpdb->prepare(
            'select rate from ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
            date('Y-m-d'),
            $to
        ));

        if ($rateFrom && $rateTo) {
            return number_format_i18n($rateFrom->rate / $rateTo->rate, $decimals);
        }

        return '';
    }

    public static function downloadForToday()
    {
        global $wpdb;

        $currentBank = get_option(CurrenciesWpName . '_bank', '');

        $list = json_decode(file_get_contents(CurrenciesWpRoot . '/assets/banklist.json'), true);
        foreach ($list as $bank) {
            if ($bank['code'] === $currentBank) {
                $className = $bank['class'];

                /**
                 * @var CurrenciesProvider $provider
                 */
                $provider = new $className();
                $provider->downloadCurrenciesForDate(new DateTime());

                $currencies = $provider->getCurrencies();

                foreach ($currencies as $currency) {
                    $isExists = $wpdb->get_row($wpdb->prepare(
                        'select id from ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
                        date('Y-m-d'),
                        $currency->getCode()
                    ));

                    if ($isExists) {
                        $wpdb->update(
                            $wpdb->prefix . CurrenciesWp::TABLECURRENCIES,
                            ['rate' => $currency->getRate()],
                            ['id' => $isExists->id]
                        );
                    } else {
                        $wpdb->insert(
                            $wpdb->prefix . CurrenciesWp::TABLECURRENCIES,
                            [
                                'date' => date('Y-m-d'),
                                'currency' => $currency->getCode(),
                                'rate' => $currency->getRate()
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Database uninstall script
     */
    public function uninstall()
    {
        global $wpdb;
        $sql = 'DROP TABLE IF EXISTS ' . $wpdb->prefix . CurrenciesWp::TABLECURRENCIES . ';';
        $wpdb->query($sql);
    }

    /**
     * Admin menu registration
     */
    public function registerMenu()
    {
        $menu = MenuFactory::addMenu(
            CurrenciesWpName,
            'manage_options',
            CurrenciesWpName,
            [
                'icon' => 'dnolbon_logo_18x18_white.png',
                'function' => [new SettingsPage(), 'render']
            ]
        );

        $menu->addChild(
            MenuFactory::addMenu(
                'Shortcode',
                'manage_options',
                'shortcode',
                [
                    'function' => [new ShortcodePage(), 'render']
                ]
            )
        );

        $menu->addChild(
            MenuFactory::addMenu(
                'Archive',
                'manage_options',
                'archive',
                [
                    'function' => [new ArchivePage(), 'render']
                ]
            )
        );

        $menu->show();
    }

}
