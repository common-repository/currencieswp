<?php
namespace Dnolbon\Currencies;

abstract class CurrenciesProvider
{
    /**
     * @var int $timeout
     */
    protected $timeout = 30;

    /**
     * @var string $userAgent
     */
    protected $userAgent = 'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

    /**
     * @var Currency[] $currencies
     */
    private $currencies;

    /**
     * CurrenciesProvider constructor.
     */
    public function __construct()
    {
        $this->addDefaultCurrency();
    }

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return Currency
     */
    abstract public function getDefaultCurrency();

    /**
     * @param \DateTime $date
     * @return bool
     */
    abstract public function downloadCurrenciesForDate($date);

    /**
     * @param Currency $currency
     */
    public function addCurrency($currency)
    {
        $this->currencies[$currency->getCode()] = $currency;
    }

    /**
     * @return Currency[]
     */
    public function getCurrencies()
    {
        return $this->currencies;
    }

    /**
     * @return resource
     */
    public function getCurlInstance()
    {
        $curlInstance = curl_init();
        curl_setopt($curlInstance, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlInstance, CURLOPT_FAILONERROR, true);
        curl_setopt($curlInstance, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlInstance, CURLOPT_AUTOREFERER, true);

        if ($this->userAgent !== '') {
            curl_setopt($curlInstance, CURLOPT_USERAGENT, $this->userAgent);
        }

        if ($this->timeout > 0) {
            curl_setopt($curlInstance, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        }

        return $curlInstance;
    }

    /**
     *
     */
    protected function addDefaultCurrency()
    {
        $this->addCurrency($this->getDefaultCurrency());
    }

    /**
     * @param string $currencyFromCode
     * @param string $currencyToCode
     * @param float $amount
     * @return float
     */
    public function convert($currencyFromCode, $currencyToCode, $amount)
    {
        if (!array_key_exists($currencyFromCode, $this->getCurrencies())) {
            throw new \InvalidArgumentException('Bad currencyFrom');
        }

        if (!array_key_exists($currencyToCode, $this->getCurrencies())) {
            throw new \InvalidArgumentException('Bad currencyFrom');
        }

        $currencyFrom = $this->getCurrencies()[$currencyFromCode];
        $currencyTo = $this->getCurrencies()[$currencyToCode];

        $amountInDefault = $amount * $currencyFrom->getRate();
        $amountConverted = $amountInDefault / $currencyTo->getRate();

        return $amountConverted;
    }
}
