<?php

require './vendor/autoload.php';

$app = new \App\App(new \App\ApiHandlers\CbRfCurrencyRateHandler());
$app->run();