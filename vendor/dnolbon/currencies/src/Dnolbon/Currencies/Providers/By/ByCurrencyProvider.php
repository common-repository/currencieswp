<?php
namespace Dnolbon\Currencies\Providers\By;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class ByCurrencyProvider extends CurrenciesProvider
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'Национальный Банк Республики Беларусь';
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function downloadCurrenciesForDate($date)
    {
        $dateFormatted = date('Y-m-d', $date->getTimestamp());
        $url = 'http://www.nbrb.by/API/ExRates/Rates?onDate=' . $dateFormatted . '&Periodicity=0';

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $jsonData = json_decode($curlData, true);

        foreach ($jsonData as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element['Cur_Name'],
                (string)$element['Cur_Abbreviation'],
                (float)(str_replace(",", ".", (string)$element['Cur_OfficialRate'])) /
                (float)(str_replace(",", ".", (string)$element['Cur_Scale']))
            ));
        }
        return true;
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        return CurrencyFactory::create('Белорусский рубль', 'BYN', 1);
    }
}
