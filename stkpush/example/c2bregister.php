<?php
require "../src/autoload.php";
require 'SaveInDB.php';
use Kabangi\Mpesa\Init as Mpesa;

// You can also pass your own config here.
// Check the folder ./config/mpesa.php for reference

$mpesa = new Mpesa();
try {
//    $response = $mpesa->B2C([
//        'amount' => 10,
//		'phone'=>'254722537792',
//        'accountReference' => '12',
//        'callBackURL' => 'https://example.com/v1/payments/C2B/confirmation',
//        'queueTimeOutURL' => 'https://example.com/v1/payments/C2B/confirmation',
//        'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
//        'Remarks' => 'Test'
//    ]);


    // $mpesa->STKStatus([]);
    
    // $mpesa->C2BRegister([]);
//    $data=array('amount' => 10,
//	 'PhoneNumber'=>'254722537792',
//        'accountReference' => '2',
//        'callBackURL' => 'https://41.90.111.246:8181/stkpush/example/callback.php',
//        'queueTimeOutURL' => 'https://41.90.111.246:8181/stkpush/example/confirmation.php',
//      //  'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
//        "businessShortCode"=>715423,
//        'TransactionDesc' =>'Lipa Mpesa');
//    $response =$mpesa->STKStatusQuery($data);
//    
//    saveInDb($data);
    
    $data=array("ShortCode"=>715423,
    "ResponseType"=>" ",
    "ConfirmationURL"=>"https://41.90.111.246:8181/stkpush/example/confirmc2b.php",
    "ValidationURL"=>"https://41.90.111.246:8181/stkpush/example/validatec2b.php");
    $response=$mpesa->C2BRegister($data);
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

