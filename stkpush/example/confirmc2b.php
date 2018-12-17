<?php
require 'SaveInDB.php';
 //$json = file_get_contents('php://input');
//die(print_r($json));
//$post = json_decode($json, TRUE);

//$file = fopen('response.json', 'w');
//fwrite($file, $json);
$filecontents = file_get_contents('response.json');
$contents=json_decode($filecontents,TRUE);
//print_r($contents);
//echo $contents->TransactionType;
//saveInDb($post);
//if(count($contents)>0){
    //$contents->Body->stkCallback->MerchantRequestID." ".$contents->Body->stkCallback->CheckoutRequestID.
//print_r($contents->Body->stkCallback->CallbackMetadata->Item[1]->Value); //MPESA RECEIPT no
//$result=confirmPayment($contents->Body->stkCallback->MerchantRequestID, $contents->Body->stkCallback->CheckoutRequestID,$contents->Body->stkCallback->CallbackMetadata->Item[1]->Value);

$result=confirmC2BPayment($contents);


//}
//else{
// $logcontents = file_get_contents('error.log', 'r+');
//  $logfile = fopen('error.log', 'w');
//  fwrite($logfile,$logcontents."<br>Error confirming stk payment".date("mdHis"));  
//}


