<?php
namespace Dnolbon\CurrenciesWp\WooCommerce;

use Dnolbon\CurrenciesWp\CurrenciesWp;
use Dnolbon\WooCommerce\Products\ProductColumnAbstract;

class ProductsColumns extends ProductColumnAbstract
{
    private $currency;

    /**
     * ProductsColumns constructor.
     * @param $currency
     */
    public function __construct($currency)
    {
        $this->currency = $currency;
        parent::__construct();
    }

    public function render($column, $postId)
    {
        if ($column === $this->getColumnName()) {
            $product = wc_get_product($postId);

            echo CurrenciesWp::getRate(get_woocommerce_currency(), $this->getCurrency()) * $product->get_price();
        }
    }

    protected function getColumnName()
    {
        return 'price_currency';
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    protected function getInsertAfter()
    {
        return 'price';
    }

    protected function getColumnTitle()
    {
        return 'Price in ' . $this->getCurrency();
    }

    protected function getInsertBefore()
    {
        return '';
    }
}
