<?php
namespace Dnolbon\CurrenciesWp;

use DateTime;
use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\CurrenciesWp\Pages\ArchivePage;
use Dnolbon\CurrenciesWp\Pages\SettingsPage;
use Dnolbon\CurrenciesWp\Pages\ShortcodePage;
use Dnolbon\CurrenciesWp\WooCommerce\ProductsColumns;
use Dnolbon\Wordpress\Db\Db;
use Dnolbon\Wordpress\MainClassAbstract;
use Dnolbon\Wordpress\Menu\MenuFactory;

class CurrenciesWp extends MainClassAbstract
{
    /**
     * DB version
     */
    const VERSION = 1;

    /**
     * Main table name
     */
    const TABLECURRENCIES = 'dnolbon_currencies';

    public static function getCurrencies()
    {
        $list = Db::getInstance()->getDb()->get_results(Db::getInstance()->getDb()->prepare(
            'select * from ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s order by currency asc',
            date('Y-m-d')
        ));
        return $list;
    }

    public static function getRate($from, $to, $decimals = 2)
    {
        $isExists = Db::getInstance()->getDb()->get_row(Db::getInstance()->getDb()->prepare(
            'select count(*) as c from ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s',
            date('Y-m-d')
        ));

        if (!$isExists || (int)$isExists->c === 0) {
            self::downloadForToday();
        }

        $rateFrom = Db::getInstance()->getDb()->get_row(Db::getInstance()->getDb()->prepare(
            'select rate from ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
            date('Y-m-d'),
            $from
        ));

        $rateTo = Db::getInstance()->getDb()->get_row(Db::getInstance()->getDb()->prepare(
            'select rate from ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
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
                    $isExists = Db::getInstance()->getDb()->get_row(Db::getInstance()->getDb()->prepare(
                        'select id from ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ' where date = %s and currency = %s',
                        date('Y-m-d'),
                        $currency->getCode()
                    ));

                    if ($isExists) {
                        Db::getInstance()->getDb()->update(
                            Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES,
                            ['rate' => $currency->getRate()],
                            ['id' => $isExists->id]
                        );
                    } else {
                        Db::getInstance()->getDb()->insert(
                            Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES,
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

    public static function getClassName()
    {
        // TODO: Implement getClassName() method.
    }

    /**
     * Database uninstall script
     */
    public function uninstall()
    {
        $sql = 'DROP TABLE IF EXISTS ' . Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES . ';';
        Db::getInstance()->getDb()->query($sql);
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

    public function activationHook()
    {
        // TODO: Implement activationHook() method.
    }

    public function deactivationHook()
    {
        // TODO: Implement deactivationHook() method.
    }

    public function onAdminInit()
    {
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
        $charsetCollate = '';
        $charset = Db::getInstance()->getDb()->charset;
        $collate = Db::getInstance()->getDb()->collate;

        if ($charset !== null && $charset !== '') {
            $charsetCollate = "DEFAULT CHARACTER SET {$charset}";
        }

        if ($collate !== null && $collate !== '') {
            $charsetCollate .= " COLLATE {$collate}";
        }

        $tableName = Db::getInstance()->getDb()->prefix . CurrenciesWp::TABLECURRENCIES;
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (" .
            "`id` int(20) unsigned NOT NULL AUTO_INCREMENT," .
            "`currency` varchar(5) NOT NULL," .
            "`date` DATE NOT NULL," .
            "`rate` DOUBLE (10,4) NOT NULL DEFAULT 0," .
            "PRIMARY KEY (`id`)" .
            ") {$charsetCollate} ENGINE=InnoDB;";
        dbDelta($sql);
    }

    public function registerAssets()
    {
        // TODO: Implement registerAssets() method.
    }

    public function registerActionLinks($links)
    {
        // TODO: Implement registerActionLinks() method.
    }

    public function getClassPrefix()
    {
        // TODO: Implement getClassPrefix() method.
    }

    protected function actionsForAdmin()
    {
        $showInWooCommerce = get_option(CurrenciesWpName . '_show_in_woocommerce', false);
        $wooCommerceCurrency = get_option(CurrenciesWpName . '_woocommerce_currency', '');

        if ($showInWooCommerce) {
            new ProductsColumns($wooCommerceCurrency);
        }
    }

    protected function actionsForUser()
    {
        // TODO: Implement actionsForUser() method.
    }

    protected function actionsForAll()
    {
        // TODO: Implement actionsForAll() method.
    }
}
