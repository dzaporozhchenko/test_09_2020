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
    print_r(libxml_get_errors());
    exitWithError('Unable to parse server response. Error: ' . libxml_get_errors()[0]);
} else {
    echo json_encode($xml);
}

function exitWithError($errorMsg) {
    echo json_encode([
        'error' => true,
        'errorMsg' => $errorMsg
    ]);
    exit(1);
}