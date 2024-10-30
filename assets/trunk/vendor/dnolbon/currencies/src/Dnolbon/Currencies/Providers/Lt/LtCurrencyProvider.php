<?php
namespace Dnolbon\Currencies\Providers\Lt;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class LtCurrencyProvider extends CurrenciesProvider
{

    /**
     * @return string
     */
    public function getName()
    {
        return 'Lietuvos bankas (LT)';
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
        $dateFormatted = date('Y-m-d', $date->getTimestamp());
        $url = 'http://www.lb.lt/webservices/FxRates/FxRates.asmx/getFxRates?';
        $url .= 'tp=LT&dt=' . $dateFormatted;

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $xmlData = new \SimpleXMLElement($curlData);
        foreach ($xmlData->FxRate as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element->CcyAmt[1]->Ccy,
                (string)$element->CcyAmt[1]->Ccy,
                (1 / (float)(str_replace(",", ".", (string)$element->CcyAmt[1]->Amt)))
            ));
        }
    }
}
