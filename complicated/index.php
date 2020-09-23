<?php
// Loading project dependencies
require './vendor/autoload.php';

// Create application instance and handle request
$app = new \App\App(new \App\ApiHandlers\CbRfCurrencyRateHandler());
$app->run();