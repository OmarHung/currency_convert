<?php
include "CurrencyConvert.php";
function test($testList)
{
    foreach($testList as $test) {
        $fromCurrency = $test[0];
        $toCurrency = $test[1];
        $amount = $test[2];
        $correctData = $test[3];
        echo "=== input ===\n";
        echo "fromCurrency: {$fromCurrency}\n";
        echo "toCurrency: {$toCurrency}\n";
        echo "amount: {$amount}\n";
        echo "=== result ===\n";
        $reault = "";
        try {
            $currencyConvert = new CurrencyConvert();
            $toAmount = $currencyConvert->convert($fromCurrency, $toCurrency, $amount);
            $reault = $toAmount;
        } catch (\Exception $e) {
            $reault = $e->getMessage();
        }
        echo $reault."\n";
        if($reault == $correctData) {
            echo "!!! PASS !!!\n";
        }else {
            echo "!!! ERROR !!!\n";
            echo "correct: {$correctData}\n";
        }
        echo "\n";
        echo "\n";
    }
}

$testList = [
    // 單一貨幣
    ["TWD", "TWD", "500", "500.00"],
    // 對幣
    ["TWD", "JPY", "500", "1,834.50"],
    // 錯誤來源幣名
    ["TWDG", "TWD", "500", CurrencyConvert::ERROR_MSG_NO_FROM_CURRENCY],
    // 錯誤目標幣名
    ["TWD", "EUR", "500", CurrencyConvert::ERROR_MSG_NO_TO_CURRENCY],
    // 金額 9999999999999999999999999999999999999999999999999
    ["JPY", "TWD", "9999999999999999999999999999999999999999999999999", "2,695,599,999,999,999,999,999,999,999,999,999,999,999,999,999,999.73"],
    // 金額 -9999999999999999999999999999999999999999999999999 
    ["JPY", "TWD", "-9999999999999999999999999999999999999999999999999", "-2,695,599,999,999,999,999,999,999,999,999,999,999,999,999,999,999.73"],
    // 無來源貨幣
    [NULL, "TWD", "500", CurrencyConvert::ERROR_MSG_NEED_FROM_CURRENCY],
    // 無目標貨幣
    ["TWD", NULL, "500", CurrencyConvert::ERROR_MSG_NEED_TO_CURRENCY],
    // 無金額
    ["TWD", "JPY", NULL, CurrencyConvert::ERROR_MSG_NEED_AMOUNT],
    // 金額 0
    ["TWD", "JPY", "0", "0.00"],
    // 金額 小數點
    ["USD", "JPY", "102.444235740288548", "11,453.37"],
    // 金額 不為字串形態
    ["USD", "JPY", 500.31, CurrencyConvert::ERROR_MSG_AMOUNT_NOT_STRING],
    ["USD", "JPY", -500.31, CurrencyConvert::ERROR_MSG_AMOUNT_NOT_STRING],
    // 金額 不為數字字串
    ["USD", "JPY", "+d500.31", CurrencyConvert::ERROR_MSG_AMOUNT_NOT_NUMBER_STRING],
    ["USD", "JPY", "f+d500.31", CurrencyConvert::ERROR_MSG_AMOUNT_NOT_NUMBER_STRING],
    ["USD", "JPY", "*500.31", CurrencyConvert::ERROR_MSG_AMOUNT_NOT_NUMBER_STRING],
];

test($testList);