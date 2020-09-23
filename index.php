<?php
error_reporting(E_ERROR | E_PARSE);
header("Content-Type: application/json; charset=UTF-8");

$yesterdayDate = new DateTime('yesterday', new DateTimeZone('UTC'));
$url = 'http://www.cbr.ru/scripts/XML_daily_eng.asp?date_req=' . $yesterdayDate->format('d/m/Y');
$response = file_get_contents($url);

if ($response === false) {
    exitWithError('Unable to get information from the server');
}

$xml = simplexml_load_string($response);
if ($xml === false) {
    exitWithError('Unable to parse server response');
} else {
    $currencies = array();

    if (empty($xml->Valute)) {
        exitWithError('Server response doesn\'t contain currency rates');
    }

    foreach (($xml->Valute) as $item) {
        $currencies[(string) $item->CharCode] = array(
            'numCode' => (string) $item->NumCode,
            'charCode' => (string) $item->CharCode,
            'nominal'   => (string) $item->Nominal,
            'name'      => (string) $item->Name,
            'rate'      => (string) $item->Value
        );
    }

    echo json_encode(array(
        'error' => false,
        'data'  => $currencies
    ));
}

function exitWithError($errorMsg) {
    echo json_encode(array(
        'error' => true,
        'errorMsg' => $errorMsg
    ));
    exit(1);
}