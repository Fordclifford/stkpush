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
    $data=array('amount' => 10,
	 'PhoneNumber'=>'254711401187',
        'accountReference' => '2',
        'Timestamp'=>date("YmdHis"),
        'callBackURL' => 'http://41.90.111.246:8181/stkpush/example/confirm2.php',
        'queueTimeOutURL' => 'https://41.90.111.246:8181/stkpush/example/stkconfirmation.php',
      //'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
        "businessShortCode"=>715423,
        'TransactionDesc' => 'deposit');
    $response =$mpesa->STKPush($data);
    //$formattedresponse=json_decode($response);
    //print_r($response);
    if($response->ResponseCode==0){
   $data1=array("CheckoutRequestID"=>$response->CheckoutRequestID,"MerchantRequestID"=>$response->MerchantRequestID);
    $data3=  array_merge($data,$data1);
    print_r($data3);
   //die(print_r($data3));
  // saveInDb($data3);
    }
    
    
    
    
    
    
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

