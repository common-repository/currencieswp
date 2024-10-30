<?php
namespace Dnolbon\Currencies\Providers\Ru;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class CbrCurrencyProvider extends CurrenciesProvider
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Центральный банк Российской Федерации';
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function downloadCurrenciesForDate($date)
    {
        $dateFormatted = date('d/m/Y', $date->getTimestamp());
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $dateFormatted;

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $xmlData = new \SimpleXMLElement($curlData);
        foreach ($xmlData->Valute as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element->Name,
                (string)$element->CharCode,
                (float)(str_replace(",", ".", (string)$element->Value))
            ));
        }
        return true;
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        return CurrencyFactory::create('Рубль', 'RUB', 1);
    }
}
