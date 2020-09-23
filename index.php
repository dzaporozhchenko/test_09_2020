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
    $currencies = [];
    foreach (($xml->Valute ?? []) as $item) {
        $currencies[(string) $item->CharCode] = [
            'numCode' => (string) $item->NumCode,
            'charCode' => (string) $item->CharCode,
            'nominal'   => (string) $item->Nominal,
            'name'      => (string) $item->Name,
            'rate'      => (string) $item->Value
        ];
    }

    if (empty($currencies)) {
        exitWithError('Server response doesn\'t contain currency rates');
    }

    echo json_encode([
        'error' => false,
        'data'  => $currencies
    ]);
}

function exitWithError($errorMsg) {
    echo json_encode([
        'error' => true,
        'errorMsg' => $errorMsg
    ]);
    exit(1);
}