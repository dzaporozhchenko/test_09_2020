<?php

namespace App\ApiHandlers;

interface CurrencyRateHandlerInterface
{
    /**
     * @return array {
     *      Array of currency rates or error message
     *
     *      array['data']                   Currency rates data
     *          array [currency charCode]   Specific currency data
     *              string 'charCode',
     *              string 'name',
     *              string 'nominal',
     *              string 'numCode',
     *              string 'rate'
     *      array['errorMsg']               Error message in case if currency rate data returning is impossible
     * }
     */
    public function getCurrencyRatesForYesterday() : array;
}