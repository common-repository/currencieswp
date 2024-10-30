<?php
namespace Dnolbon\Currencies\Providers\Eu;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class EuCurrencyProvider extends CurrenciesProvider
{
    /**
     * EuCurrencyProvider constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->userAgent = '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'European Central Bank';
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        return CurrencyFactory::create('Euro', 'EUR', 1);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function downloadCurrenciesForDate($date)
    {
//        $dateFormatted = date('Ymd', $date->getTimestamp());
        $url = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $xmlData = new \SimpleXMLElement($curlData);

        /**
         * @var \SimpleXMLElement $element
         */
        foreach ($xmlData->Cube->Cube->Cube as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element->attributes()->currency,
                (string)$element->attributes()->currency,
                (1 / (float)(str_replace(",", ".", (string)$element->attributes()->rate)))
            ));
        }

        return true;
    }
}
