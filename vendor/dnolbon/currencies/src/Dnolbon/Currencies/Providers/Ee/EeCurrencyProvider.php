<?php
namespace Dnolbon\Currencies\Providers\Ee;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class EeCurrencyProvider extends CurrenciesProvider
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Eesti Pank';
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
        $dateFormatted = date('d.m.Y', $date->getTimestamp());
        $url = 'https://www.eestipank.ee/valuutakursid/export/xml/latest?';
        $url .= 'imported_at=' . $dateFormatted;

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
    }
}
