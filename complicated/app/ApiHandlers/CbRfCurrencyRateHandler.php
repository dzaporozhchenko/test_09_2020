<?php


namespace App\ApiHandlers;


use Carbon\Carbon;
use SoapClient;
use SoapFault;
use stdClass;

class CbRfCurrencyRateHandler implements CurrencyRateHandlerInterface
{
    protected $response;

    const ATTRIBUTES_MAP = [
        'VchCode'   => 'charCode',
        'Vname'     => 'name',
        'Vnom'      => 'nominal',
        'Vcode'     => 'numCode',
        'Vcurs'     => 'rate'
    ];

    public function getCurrencyRatesForYesterday() : array
    {
        try {
            $this->makeRequest();
            return ['data' => $this->prepareResponseToJson()];
        } catch (\Exception $e) {
            return ['errorMsg' => $e->getMessage()];
        }
    }

    protected function makeRequest() : void
    {
        try {
            $now = Carbon::now('UTC');
            $client = new SoapClient("https://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL");

            $callParams = new stdClass();
            $callParams->On_date = $now->subDay()->toDateTimeLocalString();
            $response = $client->GetCursOnDate($callParams);
        } catch (SoapFault $fault) {
            throw new \Exception(sprintf(
                "An error occurred while connecting to server. Error code: %s. Error message: %s",
                $fault->faultcode,
                $fault->faultstring
            ));
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while connecting to server.');
        }

        $this->response = $response;
    }

    protected function prepareResponseToJson() : array
    {
        $parsedResponse = simplexml_load_string($this->response->GetCursOnDateResult->any);

        if (empty($parsedResponse)) {
            throw new \Exception('Unable to parse server response');
        }

        $currencyRates = $parsedResponse->ValuteData->ValuteCursOnDate;
        $currencies = [];

        foreach ($currencyRates as $currencyRate) {
            $currency = [];
            foreach ($currencyRate as $key => $item) {
                if (array_key_exists($key, self::ATTRIBUTES_MAP)) {
                    $currency[self::ATTRIBUTES_MAP[$key]] = trim((string) $item);
                } else {
                    $currency[$key] = trim((string) $item);
                }
            }
            $currencies[$currency['charCode']] = $currency;
        }

        if (empty($currencies)) {
            throw new \Exception('Server response doesn\'t contain currency rates');
        }

        return $currencies;
    }
}