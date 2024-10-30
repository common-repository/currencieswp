<?php
namespace Dnolbon\Currencies\Providers\Lv;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class LvCurrencyProvider extends CurrenciesProvider
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Latvijas banka';
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
        $dateFormatted = date('Ymd', $date->getTimestamp());
        $url = 'https://www.bank.lv/vk/ecb.xml?';
        $url .= 'date=' . $dateFormatted;

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $xmlData = new \SimpleXMLElement($curlData);
        foreach ($xmlData->Currencies->Currency as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element->ID,
                (string)$element->ID,
                (1 / (float)(str_replace(",", ".", (string)$element->Rate)))
            ));
        }
    }
}
