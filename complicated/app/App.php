<?php


namespace App;


use App\ApiHandlers\CurrencyRateHandlerInterface;

class App
{
    // Object of class that receives the exchange rate
    protected CurrencyRateHandlerInterface $currencyRateHandler;

    /**
     * App constructor. Dependency injection
     * @param CurrencyRateHandlerInterface $currencyRateHandler
     */
    public function __construct(CurrencyRateHandlerInterface $currencyRateHandler)
    {
        $this->currencyRateHandler = $currencyRateHandler;
    }

    /**
     * Main application method
     */
    public function run() : void
    {
        header("Content-Type: application/json; charset=UTF-8");

        $response = $this->currencyRateHandler->getCurrencyRatesForYesterday();
        $response['error'] = ! empty($response['errorMsg']);

        echo json_encode($response);
    }
}