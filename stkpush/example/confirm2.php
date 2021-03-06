<?php
require 'SaveInDB.php';
$file = fopen('callback.json', 'a');
fwrite($file, file_get_contents('php://input'));

function processSTKPushRequestCallback(){
  $callbackJSONData=file_get_contents('php://input');
//$callbackJSONData = file_get_contents('test.json');
        $callbackData=json_decode($callbackJSONData);
        $resultCode=$callbackData->Body->stkCallback->ResultCode;
        $resultDesc=$callbackData->Body->stkCallback->ResultDesc;
        $merchantRequestID=$callbackData->Body->stkCallback->MerchantRequestID;
        $checkoutRequestID=$callbackData->Body->stkCallback->CheckoutRequestID;
        $amount=$callbackData->stkCallback->Body->CallbackMetadata->Item[0]->Value;
        $mpesaReceiptNumber=$callbackData->Body->stkCallback->CallbackMetadata->Item[1]->Value;
        $balance=$callbackData->stkCallback->Body->CallbackMetadata->Item[2]->Value;
       // $b2CUtilityAccountAvailableFunds=$callbackData->Body->stkCallback->CallbackMetadata->Item[3]->Value;
        $transactionDate=$callbackData->Body->stkCallback->CallbackMetadata->Item[3]->Value;
        $phoneNumber=$callbackData->Body->stkCallback->CallbackMetadata->Item[4]->Value;
        $result=[
            "resultDesc"=>$resultDesc,
            "resultCode"=>$resultCode,
            "merchantRequestID"=>$merchantRequestID,
            "checkoutRequestID"=>$checkoutRequestID,
            "amount"=>$amount,
            "mpesaReceiptNumber"=>$mpesaReceiptNumber,
            "balance"=>$balance,
            "transactionDate"=>$transactionDate,
            "phoneNumber"=>$phoneNumber
        ];
        return json_encode($result);
    }
    $array = json_decode(processSTKPushRequestCallback(),true);
    confirmPayment($array);
  
    $file = fopen('callback.json', 'a');
fwrite($file, $resultDesc = $array["resultDesc"].
    $resultCode = $array['resultCode'].
    $merchantRequestID = $array['merchantRequestID'].
    $checkoutRequestID = $array['checkoutRequestID'].
    $mpesaReceiptNumber = $array['mpesaReceiptNumber'].
    $transactionDate = $array['transactionDate'].
    $phoneNumber = $array['phoneNumber']);
    