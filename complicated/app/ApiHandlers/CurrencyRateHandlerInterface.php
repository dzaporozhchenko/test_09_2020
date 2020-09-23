<?php

namespace App\ApiHandlers;

interface CurrencyRateHandlerInterface
{
    public function getCurrencyRatesForYesterday() : array;
}