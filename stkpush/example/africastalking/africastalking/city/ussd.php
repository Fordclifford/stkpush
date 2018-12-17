<?php

session_start();
require 'autoload.php';
require_once 'dbconnect.php';

use AfricasTalking\SDK\AfricasTalking;

// $sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text = $_POST["text"];


$text = str_replace(' ', '', $text);
$_SESSION["text"] = strtoupper($text);
$_SESSION["shortcode"] = $serviceCode;
$_SESSION["phone"] = $phoneNumber;


$text = $_SESSION["text"];
$shortCode = $_SESSION["shortcode"];
$phoneNumber = $_SESSION["phone"];
$prefix = trim(substr($text, 8, 2));  // returns "query message"   
$txt = trim(substr($text, 10, 40));  // returns "query message"    
//1.	EXPERT REGISTRATION 

$pref = preg_replace('/[^a-zA-Z]+/', '', $prefix);
$number = preg_replace('/[^0-9]+/', '', $prefix);
switch ($number) {

    case "":
        $msg = "Thank you for contacting City Corporation \n";
        $msg .= "Kindly reply with: \n";
        $msg .= "citycorp1 - To register \n";
        $msg .= "citycorp2 - To appy for a loan ";
        $number;
        sendmessage($msg);



        break;

    case 1:
        $msg = "Please enter your firstname,lastname and ID Number as: \n";
        $msg .= "citycorp 3#firstname#lastname#ID e.g\n";
        $msg .= "citycorp 3#John#Doe#123456";
        sendmessage($msg);


        break;

    case 2:
        $msg = "Please reply with Loan Product \n";
        $msg .= "citycorp 4 Motorbike\n";
        $msg .= "citycorp 5 Casino\n";
        $msg .= "citycorp 6 TukTuk e.g.\n";
        $msg .= "citycorp 6";

        sendmessage($msg);
        break;
    case 3:
        $data = explode('#', $txt, 5);
        $details = Array();
        $details['firstname'] = $data[0];
        $details['lastname'] = $data[1];
        $details['officeId'] = 1;
        $details['active'] = true;
        $details['activationDate'] = date("d F Y");
        $details['externalId'] = $data[2];
        $details['dateFormat'] = "dd MMMM yyyy";
        $details['locale'] = "en";

        //print_r($details);

        $url = "https://localhost:8443/fineract-provider/api/v1/clients?tenantIdentifier=default";

        // {"address":[],"familyMembers":[],"officeId":1,"firstname":"Clifford","middlename":"Oryosa","lastname":"Masi","mobileNo":"711401187","genderId":16,"locale":"en","active":false,"dateFormat":"dd MMMM yyyy","activationDate":"05 December 2018","submittedOnDate":"08 December 2018","savingsProductId":null}        
        get_details_to_mifos($url, $details);
        $msg = "Your account has been registered successfully ";
        sendmessage($msg);


        break;
    case 4:
        // {"clientId":"3","productId":2,"disbursementData":[],"principal":10000,"loanTermFrequency":6,"loanTermFrequencyType":0,"numberOfRepayments":6,"repaymentEvery":1,"repaymentFrequencyType":0,"interestRatePerPeriod":15,"amortizationType":1,"isEqualAmortization":true,"interestType":0,"interestCalculationPeriodType":1,"allowPartialPeriodInterestCalcualtion":false,"graceOnArrearsAgeing":30,"transactionProcessingStrategyId":1,"locale":"en","dateFormat":"dd MMMM yyyy","loanType":"individual","submittedOnDate":"05 December 2018"}
        $msg = "Please reply with Your ID number as \n";
        $msg .= "citycorp 7# ID e.g.\n";
        $msg .= "citycorp 7#44335544 ";

        break;

    case 7:
       //{"clientId":"3","productId":1,"disbursementData":[],"principal":94000,"loanTermFrequency":365,"loanTermFrequencyType":0,
       //"numberOfRepayments":365,"repaymentEvery":1,
       //"repaymentFrequencyType":0,"interestRatePerPeriod":9.5,"
       //amortizationType":0,"isEqualAmortization":true,"interestType":0
       //,"interestCalculationPeriodType":1,"allowPartialPeriodInterestCalcualtion":false,
       //"inArrearsTolerance":365,
       //"graceOnPrincipalPayment":1,
        //"graceOnInterestPayment":1,"transactionProcessingStrategyId":1,"locale":"en",
        //"dateFormat":"dd MMMM yyyy","loanType":"individual","submittedOnDate":"05 December 2018","charges":[{"chargeId":36,"amount":3.16},{"chargeId":38,"amount":20000}]}   

        $sql = "SELECT id, account_no, external_id FROM m_client where external_id=$txt";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
        
            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
               // echo "id: " . $row["id"] . " - Name: " . $row["account_no"] . " " . $row["external_id"] . "<br>";
           $details = Array();
           $row['id'];           
          $details['clientId']=$row['id'];
          $details['productId']=1;
          $details['principal'] = "94,000.00";
          $details['loanTermFrequency'] = 365;
          $details['loanTermFrequencyType'] =0;
          $details['numberOfRepayments'] =365;
          $details['repaymentEvery'] =1;
          $details['repaymentFrequencyType'] =0;
           $details['loanTermFrequencyType'] =0;
          $details['repaymentFrequencyType'] =0;
          $details['interestRatePerPeriod'] =9.5;
          $details['amortizationType'] =0;
          $details['interestType'] =0;
           $details['interestCalculationPeriodType'] =1;
          $details['transactionProcessingStrategyId'] =1;
          $details['interestRatePerPeriod'] =9.5;
           $details['expectedDisbursementDate'] =date("d F Y");
          $details['submittedOnDate'] =date("d F Y");
          $details['locale'] ="en";
           $details['loanType'] ='individual';
          $details['dateFormat'] = "dd MMMM yyyy";
          $details['charges'] =array(array("chargeId"=>36,"amount"=>3.16),array("chargeId"=>38,"amount"=>20000));
            //$details['charges'][0]={"chargeId"=>36,"amount"=>3.16},{"chargeId"=>38,"amount"=>20000}
          $details['dateFormat'] = "dd MMMM yyyy";
         // print_r($details);
           //json_decode($details);
            //$out = array_values($details);
            $url = "https://192.168.1.243:8443/fineract-provider/api/v1/loans?tenantIdentifier=default";

  // echo $js = json_encode($details);
   get_details_to_mifos($url, $details);
 
            }
           // clientId, productId, principal, loanTermFrequency, loanTermFrequencyType, loanType,
           //  numberOfRepayments, repaymentEvery, repaymentFrequencyType, interestRatePerPeriod,
           //   amortizationType, interestType, interestCalculationPeriodType, 
           //   transactionProcessingStrategyId, expectedDisbursementDate, submittedOnDate, loanType
           } else {
            echo $msg = "Invalid ID number";
            sendmessage($msg);
        }
       

        mysqli_close($conn);

       
        break;

    default:
}

function sendmessage($msg) {
    $shortCode = $_SESSION["shortcode"];
    $phoneNumber = $_SESSION["phone"];
//$username = "city";
//$apiKey  = "77dd5801e1bfcb65614fb66d0cdb11b924c4a63e6b22d8099cce4efa78a6238a";
    $username = "sandbox";
    $apiKey = "33bc41547a92c073b33fb867e1ec8fbf6e050fe4cd85c308ff35c69a3189235c";

    $AT = new AfricasTalking($username, $apiKey);

// Get one of the services
    $sms = $AT->sms();

// Use the service
    $result = $sms->send([
        'to' => $phoneNumber,
        'message' => $msg
    ]);
    print_r($result);
    //$application = $AT->application();

    session_destroy();
}

function get_details_to_mifos($url, $details) {
    $out = array_values($details);
     $js = json_encode($details);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        //curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem"),
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
        //CURLOPT_CAINFO=> dirname(__FILE__)."/cacert.pem",
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_PORT => "8443",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $js,
        CURLOPT_HTTPHEADER => array(
            "authorization: Basic YWRtaW46cGFzc3dvcmQ=",
            "cache-control: no-cache",
            "content-type: application/json",
            "postman-token: 82f71847-a076-7a85-a4d7-ed6745be0190"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}
