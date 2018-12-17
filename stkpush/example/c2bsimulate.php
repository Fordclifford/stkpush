<?php
require "../src/autoload.php";
require 'SaveInDB.php';
use Kabangi\Mpesa\Init as Mpesa;

// You can also pass your own config here.
// Check the folder ./config/mpesa.php for reference

$mpesa = new Mpesa();
try {
  
//    saveInDb($data);
//     $json= file_get_contents('php://input');
//   
//         $post=  json_decode($json,TRUE);
         
    $data=array("ShortCode" => "715423",
         "CommandID" =>"CustomerPayBillOnline",
         "Amount" => "200",
         "Msisdn" => "254722537792",
         "BillRefNumber" =>"2");
    $response=$mpesa->C2BSimulate($data);
    //print_r($response);
    
    
    
    // $mpesa->C2BSimulate([]);
    
    // $mpesa->B2C([])
    
    // $mpesa->B2B([]);
    
    // $mpesa->accountBalance([])
    
    // $mpesa->reversal([]);
    
    // $mpesa->transactionStatus([]);
    
    // $mpesa->reversal([]);
}catch(\Exception $e){
    $response = json_decode($e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);

