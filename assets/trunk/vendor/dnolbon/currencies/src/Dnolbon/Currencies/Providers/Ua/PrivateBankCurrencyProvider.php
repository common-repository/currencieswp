<?php
namespace Dnolbon\Currencies\Providers\Ua;

use Dnolbon\Currencies\CurrenciesProvider;
use Dnolbon\Currencies\Currency;
use Dnolbon\Currencies\CurrencyFactory;

class PrivateBankCurrencyProvider extends CurrenciesProvider
{

    /**
     * @return string
     */
    public function getName()
    {
        return "ПриватБанк";
    }

    /**
     * @return Currency
     */
    public function getDefaultCurrency()
    {
        return CurrencyFactory::create('UAH', 'UAH', 1);
    }

    /**
     * @param \DateTime $date
     * @return bool
     */
    public function downloadCurrenciesForDate($date)
    {
        $dateFormatted = date('d.m.Y', $date->getTimestamp());

        $url = 'https://api.privatbank.ua/p24api/exchange_rates?json&date=' . $dateFormatted;

        $curlInstance = $this->getCurlInstance();
        curl_setopt($curlInstance, CURLOPT_URL, $url);
        $curlData = curl_exec($curlInstance);
        curl_close($curlInstance);

        $jsonData = json_decode($curlData, true);

        foreach ($jsonData['exchangeRate'] as $element) {
            $this->addCurrency(CurrencyFactory::create(
                (string)$element['currency'],
                (string)$element['currency'],
                (float)(str_replace(",", ".", (string)$element['purchaseRateNB']))
            ));
        }
    }
}
