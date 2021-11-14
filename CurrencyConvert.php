<?php

class CurrencyConvert
{
    const ERROR_MSG_NO_FROM_CURRENCY = "From currency error";
    const ERROR_MSG_NO_TO_CURRENCY = "To currency error";
    const ERROR_MSG_AMOUNT_NOT_STRING = "Amount is not a string";
    const ERROR_MSG_NEED_FROM_CURRENCY = "From currency not found";
    const ERROR_MSG_NEED_TO_CURRENCY = "To currency not found";
    const ERROR_MSG_NEED_AMOUNT = "Amount not found";
    const ERROR_MSG_AMOUNT_NOT_NUMBER_STRING = "Amount is not a number string";

    const ERROR_CODE_NO_FROM_CURRENCY = -1;
    const ERROR_CODE_NO_TO_CURRENCY = -2;
    const ERROR_CODE_AMOUNT_NOT_STRING = -3;
    const ERROR_CODE_NEED_FROM_CURRENCY = -4;
    const ERROR_CODE_NEED_TO_CURRENCY = -5;
    const ERROR_CODE_NEED_AMOUNT = -6;
    const ERROR_CODE_AMOUNT_NOT_NUMBER_STRING = -7;

    public function convert($fromCurrency, $toCurrency, $amount)
    {
        // try {
            // bcscale(100);
            
            if(is_null($fromCurrency)) {
                throw new \Exception(self::ERROR_MSG_NEED_FROM_CURRENCY, self::ERROR_CODE_NEED_FROM_CURRENCY);
            }
            if(is_null($toCurrency)) {
                throw new \Exception(self::ERROR_MSG_NEED_TO_CURRENCY, self::ERROR_CODE_NEED_TO_CURRENCY);
            }
            if(is_null($amount)) {
                throw new \Exception(self::ERROR_MSG_NEED_AMOUNT, self::ERROR_CODE_NEED_AMOUNT);
            }

            if(!is_string($amount)) {
                throw new \Exception(self::ERROR_MSG_AMOUNT_NOT_STRING, self::ERROR_CODE_AMOUNT_NOT_STRING);
            }

            if(!preg_match("/^[-+]?\d+(\.\d+)?$/", $amount)) {
                throw new \Exception(self::ERROR_MSG_AMOUNT_NOT_NUMBER_STRING, self::ERROR_CODE_AMOUNT_NOT_NUMBER_STRING);
            }

            // 先轉大寫
            $fromCurrency = strtoupper($fromCurrency);
            $toCurrency = strtoupper($toCurrency);

            // 取匯率
            $exchangeRate = $this->getRate($fromCurrency, $toCurrency);

            // 目標金額
            $toAmount = $this->bcround(bcmul($amount, $exchangeRate, 3), 2);

            return $this->bcformat($toAmount);
            // echo json_encode([
            //     "status" => "success",
            //     "code" => 0,
            //     "amount" => $toAmount
            // ]);
        // } catch (\Exception $e) {
        //     echo json_encode([
        //         "status" => "fail",
        //         "code" => $e->getCode(),
        //         "message" => $e->getMessage()
        //     ]);
        // }
    }

    public function getRate($fromCurrency, $toCurrency)
    {
        $allExchangeRate = $this->getAllExchangeRate();
        if(!isset($allExchangeRate[$fromCurrency])) {
            throw new \Exception(self::ERROR_MSG_NO_FROM_CURRENCY, self::ERROR_CODE_NO_FROM_CURRENCY);
        }

        if(!isset($allExchangeRate[$fromCurrency][$toCurrency])) {
            throw new \Exception(self::ERROR_MSG_NO_TO_CURRENCY, self::ERROR_CODE_NO_TO_CURRENCY);
        }

        return floatval($allExchangeRate[$fromCurrency][$toCurrency]);
    }

    private function getAllExchangeRate()
    {
        $allRate = json_decode('
            { 
                "currencies": { 
                    "TWD": { 
                        "TWD": 1, 
                        "JPY": 3.669, 
                        "USD": 0.03281 
                    }, 
                    "JPY": { 
                        "TWD": 0.26956, 
                        "JPY": 1, 
                        "USD": 0.00885 
                    }, 
                    "USD": { 
                        "TWD": 30.444, 
                        "JPY": 111.801, 
                        "USD": 1 
                    } 
                } 
            }', true);
        
        return $allRate["currencies"];
    }

    function bcround($number, $precision = 0)
    {
        if(strpos($number, '.') !== false) {
            if($number[0] != '-') return bcadd($number, '0.'.str_repeat('0', $precision).'5', $precision);
            return bcsub($number, '0.'.str_repeat('0', $precision).'5', $precision);
        }

        return $number;
    }

    function bcformat($number)
    {
        $dotPos = strpos($number, '.');
        $numberLen = strlen($number);
        $intString = substr($number, 0, $dotPos===false? $numberLen:$dotPos);
        $floatString = $dotPos === false? "":(".".substr($number, $dotPos+1));
        $newIntString = "";
        $count = 0;
        for($i=strlen($intString)-1; $i>=0; $i--) {
            $newIntString = substr($intString, $i, 1).$newIntString;
            $count++;
            if($count % 3 == 0 && $i != 0) {
                $newIntString = ",".$newIntString;
            }
        }

        return $newIntString.$floatString;
    }
}