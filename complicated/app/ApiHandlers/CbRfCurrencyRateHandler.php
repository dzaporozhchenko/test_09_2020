<?php


namespace App\ApiHandlers;


use Carbon\Carbon;
use Exception;
use SoapClient;
use SoapFault;
use stdClass;

class CbRfCurrencyRateHandler implements CurrencyRateHandlerInterface
{
    // Storing the server response for further processing
    protected stdClass $response;

    // Server response mapping data
    const ATTRIBUTES_MAP = [
        'VchCode'   => 'charCode',
        'Vname'     => 'name',
        'Vnom'      => 'nominal',
        'Vcode'     => 'numCode',
        'Vcurs'     => 'rate'
    ];

    // Path to service WSDL file
    const WSDL_URL = 'https://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?WSDL';

    /**
     * {@inheritDoc}
     */
    public function getCurrencyRatesForYesterday() : array
    {
        try {
            $this->makeRequest();
            return ['data' => $this->prepareResponseToJson()];
        } catch (Exception $e) {
            return ['errorMsg' => $e->getMessage()];
        }
    }

    /**
     * Making request to the server and response storing
     * @throws Exception
     */
    protected function makeRequest() : void
    {
        try {
            $now = Carbon::now('UTC');
            $client = new SoapClient(self::WSDL_URL);

            $callParams = new stdClass();
            $callParams->On_date = $now->subDay()->toDateTimeLocalString();
            $response = $client->GetCursOnDate($callParams);
        } catch (SoapFault $fault) {
            throw new Exception(sprintf(
                "An error occurred while connecting to server. Error code: %s. Error message: %s",
                $fault->faultcode,
                $fault->faultstring
            ));
        } catch (Exception $e) {
            throw new Exception('An error occurred while connecting to server.');
        }

        $this->response = $response;
    }

    /**
     * Service response parsing and data mapping
     * @return array
     * @throws Exception
     */
    protected function prepareResponseToJson() : array
    {
        $parsedResponse = simplexml_load_string($this->response->GetCursOnDateResult->any);

        if (empty($parsedResponse)) {
            throw new Exception('Unable to parse server response');
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
            throw new Exception('Server response doesn\'t contain currency rates');
        }

        return $currencies;
    }
}