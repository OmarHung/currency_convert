<?php

$fromCurrency = isset($_GET["from_currency"])? $_GET["from_currency"]:NULL;
$toCurrency = isset($_GET["to_currency"])? $_GET["to_currency"]:NULL;
$amount = isset($_GET["amount"])? $_GET["amount"]:NULL;

include "CurrencyConvert.php";

$currencyConvert = new CurrencyConvert();

try {
    $toAmount = $currencyConvert->convert($fromCurrency, $toCurrency, $amount);
    echo json_encode([
        "status" => "success",
        "code" => 0,
        "amount" => $toAmount
    ]);
} catch (\Exception $e) {
    echo json_encode([
        "status" => "fail",
        "code" => $e->getCode(),
        "message" => $e->getMessage()
    ]);
}