<?php


namespace App;


use App\ApiHandlers\CurrencyRateHandlerInterface;

class App
{
    protected $currencyRateHandler;

    public function __construct(CurrencyRateHandlerInterface $currencyRateHandler)
    {
        $this->currencyRateHandler = $currencyRateHandler;
    }

    public function run()
    {
        header("Content-Type: application/json; charset=UTF-8");

        $response = $this->currencyRateHandler->getCurrencyRatesForYesterday();
        $response['error'] = ! empty($response['errorMsg']);

        echo json_encode($response);
    }
}