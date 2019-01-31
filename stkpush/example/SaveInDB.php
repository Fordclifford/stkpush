<?php

session_start();
include 'AfricasTalkingGateway.php';
$servername = "192.168.1.243:3306";

$username = "root";

$password = "mysql";

$dbname = "mifostenant-default";

function getDbConnection() {
    $DB_HOST = '192.168.1.243:3306';
    $DB_HOST_NAME = 'root';
    $DB_HOST_PASS = 'mysql';
    $DB_NAME = 'mifostenant-default';
    global $conn;
    $conn = mysqli_connect($DB_HOST, $DB_HOST_NAME, $DB_HOST_PASS, $DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

include '../vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;

// Create connection

global $conn;

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection

if ($conn->connect_error) {

    die("Connection failed: " . $conn->connect_error);
}

function sendmessage($phone, $msg) {

    //$username = "city";
//$apiKey  = "d0f242c73a33b50b779704b4600c81867e31c25915bef344d43797a63ee2715d";

    $username = "city";

    $apiKey = "265ac602db29596b17a03b87d27dcf564efa1dffa61019ae0a3c0b03da4a928f";



    $AT = new AfricasTalking($username, $apiKey);



// Get one of the services

    $sms = $AT->sms();


    $result = $sms->send([
        'to' => "+" . $phone,
        'from' => "CITYCORP",
        'message' => $msg
    ]);


    $file = fopen('process.log', 'a');

    fwrite($file, print_r($result));
    print_r($result);

    //$application = $AT->application();

    session_destroy();
}

function saveInDb($data) {

    global $conn;

    $time = date("YmdHis");

//businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL

    $sql = "INSERT INTO c2bpayment (TransactionType,TransID,TransTime,TransAmount,BusinessShortCode,BillRefNumber,InvoiceNumber,OrgAccountBalance,ThirdPartyTransID,MSISDN,FirstName,MiddleName,LastName)

VALUES ('" . $data['TransactionType'] . "','" . $data['TransID'] . "','" . $data['TransTime'] . "', '" . $data['TransAmount'] . "','" . $data['BusinessShortCode'] . "','" . $data['BillRefNumber'] . "','" . $data['InvoiceNumber'] . "','" . $data['OrgAccountBalance'] . "','" . $data['ThirdPartyTransID'] . "','" . $data['MSISDN'] . "','" . $data['FirstName'] . "','" . $data['MiddleName'] . "','" . $data['LastName'] . "')";

    $result = mysqli_query(getDbConnection(), $sql);

    if (!$result) {
        echo "Error: " . $sql . "<br>" . getDbConnection()->error;

        // "New record created successfully";
    }
}

function saveInDb2($data) {

    global $conn;

    $time = date("YmdHis");

//businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL

    $sql = "INSERT INTO stk_push_requests (TransactionType,TransID,TransTime,TransAmount,BusinessShortCode,BillRefNumber,InvoiceNumber,OrgAccountBalance,ThirdPartyTransID,MSISDN,FirstName,MiddleName,LastName)

VALUES ('" . $data['TransactionType'] . "','" . $data['TransID'] . "','" . $data['TransTime'] . "', '" . $data['TransAmount'] . "','" . $data['BusinessShortCode'] . "','" . $data['BillRefNumber'] . "','" . $data['InvoiceNumber'] . "','" . $data['OrgAccountBalance'] . "','" . $data['ThirdPartyTransID'] . "','" . $data['MSISDN'] . "','" . $data['FirstName'] . "','" . $data['MiddleName'] . "','" . $data['LastName'] . "')";



    $result = mysqli_query(getDbConnection(), $sql);

    if (!$result) {
        echo "Error: " . $sql . "<br>" . $conn->error;

        // "New record created successfully";
    }
}

//confirmPayment($merchantid, $requestid, "tieet", "200", "254711401187");
//
    
function confirmPayment($array) {

    global $conn;



    $resultDesc = $array["resultDesc"];

    $resultCode = $array['resultCode'];

    $merchantRequestID = $array['merchantRequestID'];

    $checkoutRequestID = $array['checkoutRequestID'];

    $mpesaReceiptNumber = $array['mpesaReceiptNumber'];

    $phoneNumber = $array['phoneNumber'];

    $_SESSION['phone'] = $phoneNumber;



////businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL

    $sql = "SELECT id,account_reference,phone_number,transaction_desc,amount from stk_push_requests where merchantid like '%$merchantRequestID%' AND checkoutid like '%$checkoutRequestID%'";


    $result = mysqli_query(getDbConnection(), $sql);

    if (!$result) {
        echo "Error: " . $sql . "<br>" . $conn->error;

        // "New record created successfully";
    }


//    //print_r($result);
//
    while ($row = mysqli_fetch_array($result)) {

        //  print_r($row);

        if ($row["id"]) {

            $transactiondesc = $row['transaction_desc'];

            $accountReference = $row['account_reference'];
            $receipt = $mpesaReceiptNumber;

            $date = date("YmdHis");

            $amount = $row['amount'];

            $sqll = "UPDATE  stk_push_requests SET result_desc='$resultDesc', result_code='$resultCode',mpesa_receipt_number= '$mpesaReceiptNumber',confirm_date='$date' WHERE id='" . $row["id"] . "' ";
//
//            // echo $sqll;
//
            $result = mysqli_query(getDbConnection(), $sqll);
// 
            if (!$result) {
                echo "Error: " . $sql . "<br>" . getDbConnection()->error;

//        // "New record created successfully";
            } else {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://localhost:443/shortcode/updatesms.php");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $output = curl_exec($ch);
                curl_close($ch);
            }
        }
    }
}

function get_details_to_mifos($url, $details) {

    // $out = array_values($details);

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

        return $response;
    }
}

$array = Array();
$array['BillRefNumber'] = "254711401187";
$array['TransID'] = "NAV9J5XH6Ti";
$array['FirstName'] = "Insurance";
$array['LastName'] = "Test";
$array['MiddleName'] = "Test";
$array['MSISDN'] = "254711401187";
$array['TransTime'] = "20190131114830";
$array['TransAmount'] = "1000.00";
confirmC2BPayment($array);

function confirmC2BPayment($array) {

    global $conn;


    if ($array['BillRefNumber'] != "") {


        $accountNo1 = $array['BillRefNumber'];

        $accountNo2 = str_replace(' ', '', $accountNo1);

        $accountNo = strtoupper($accountNo2);

        $mobile = $array['MSISDN'];

        $_SESSION['phone'] = $mobile;
        $_SESSION['fname'] = $array['FirstName'];
        $_SESSION['lname'] = $array['LastName'];
        $_SESSION['mname'] = $array['MiddleName'];
        $no = trim(substr($mobile, -9));

        if ($accountNo != "DL" && $accountNo != "TP" && $accountNo != "CO") {
            echo $sql1 = "SELECT id,account_no,client_id from m_loan where account_no like '%$accountNo%' LIMIT 1";


        $result1 = mysqli_query(getDbConnection(), $sql1);

            echo $sql = "SELECT id,account_no,display_name from m_client where account_no like '%$accountNo%' or RIGHT(mobile_no, 9)='$accountNo' like '%$accountNo%' or RIGHT(mobile_no, 9)='$no' LIMIT 1";

            $result = mysqli_query(getDbConnection(), $sql);
        }
        
if(isset($result1)){
        if (mysqli_num_rows($result1) > 0) {

                while ($row = mysqli_fetch_array($result1)) {

                    echo $row['id'];

                    echo $response = LoanAccount($row['id'], $array['TransTime'], $array['TransAmount'], $array['TransID']);
                    //create deposit account for registration
                    $res = json_decode($response, TRUE);

                    if (isset($res['loanId'])) {
                        echo $msg = "City Corporation Ltd, Dear Customer, Loan repayment of " . $array['TransAmount'] . " received";
                        SendCustomerSms(array("message" => $msg, "phone" => $_SESSION['phone']));
                        $receipt = $array['TransID'];
                        $mysqli = new mysqli("192.168.1.243:3306", "root", "mysql", "mifostenant-default");

                        $sql4 = "update c2bpayment set `STATUS`='confirmed' where TransID ='" . $receipt . "'";
                        $result4 = $conn->query($sql4);

                        if ($mysqli->query($sql4) === FALSE) {

                            $err = "Query failed with error: " . $sql4 . mysqli_error($mysqli) . "<br>";
                            $file = fopen('error.log', 'a');

                            fwrite($file, $err . "\n");
                        }


                        //getDbConnection()->query($sql4);

                        $file = fopen('error.log', 'a');

                        fwrite($file, $sql4 . ";" . "\n");
                    }
                }
}}
        

            else if (isset($result)) {

            if (mysqli_num_rows($result) > 0) {


                while ($row = mysqli_fetch_array($result)) {

                    $cid = $row['id'];

                    if ($array['TransAmount'] >= 1000 && $accountNo != "DL" && $accountNo != "TP" && $accountNo != "CO") {
                        echo $sql = "SELECT id,client_id,account_no from m_savings_account where client_id='$cid' group by client_id";
                        echo 'mh';
                        $result4 = $conn->query($sql);
                        if ($result4) {
                            if (mysqli_num_rows($result4) > 0) {

                                while ($row = mysqli_fetch_array($result4)) {

                                    echo $savingsAcc = $row['id'];

                                    echo $response = DepositAccount($savingsAcc, $array['TransTime'], $array['TransAmount'], $array['TransID']);
                                    $res = json_decode($response, TRUE);

                                    if (isset($res['clientId'])) {
                                        $receipt = $array['TransID'];
                                        $mysqli = new mysqli("192.168.1.243:3306", "root", "mysql", "mifostenant-default");

                                        $sql3 = "update c2bpayment set `STATUS` ='confirmed' where TransID ='" . $receipt . "'";
                                        if ($mysqli->query($sql3) === FALSE) {

                                            $err = "Query failed with error: " . $sql3 . mysqli_error($mysqli) . "<br>";
                                            $file = fopen('error.log', 'a');

                                            fwrite($file, $err . "\n");
                                        }
                                        // $result = $conn->query($sql3);
                                        // $result2 = $conn->query($sql3);



                                        $file = fopen('error.log', 'a');

                                        fwrite($file, $sql3 . ";" . "\n");
                                    }
                                }
                            } else {

                                // echo 'client has no savings account';
                            }
                        } else {

                            $err = "Query failed with error: " . mysqli_error(getDbConnection()) . "<br>";
                            $file = fopen('error.log', 'a');

                            fwrite($file, $err . "\n");
                        }
                    }
                    if ($array['TransAmount'] < 1000 && $accountNo != "DL" && $accountNo != "TP" && $accountNo != "CO") {
                        $sql = "SELECT id,client_id,account_no from m_loan where client_id='$cid' group by client_id";

                        $result3 = $conn->query($sql);

                        if ($result3) {

                            if (mysqli_num_rows($result3) > 0) {

                                while ($row = mysqli_fetch_array($result3)) {

                                    echo $row['id'];


                                    echo $response = LoanAccount($row['id'], $array['TransTime'], $array['TransAmount'], $array['TransID']);
                                    //create deposit account for registration
                                    $res = json_decode($response, TRUE);

                                    if (isset($res['clientId'])) {
                                        $receipt = $array['TransID'];
                                        $mysqli = new mysqli("192.168.1.243:3306", "root", "mysql", "mifostenant-default");

                                        $sql4 = "update c2bpayment set `STATUS`='confirmed' where TransID ='" . $receipt . "'";
                                        $result4 = $conn->query($sql4);

                                        if ($mysqli->query($sql4) === FALSE) {

                                            $err = "Query failed with error: " . $sql4 . mysqli_error($mysqli) . "<br>";
                                            $file = fopen('error.log', 'a');

                                            fwrite($file, $err . "\n");
                                        }


                                        //getDbConnection()->query($sql4);

                                        $file = fopen('error.log', 'a');

                                        fwrite($file, $sql4 . ";" . "\n");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

            else if ($accountNo === "DL" || $accountNo === "TP" || $accountNo === "CO") {
            echo $sql = "SELECT id,account_no,display_name from m_client where RIGHT(mobile_no, 9)='$no' LIMIT 1";

            $results = mysqli_query(getDbConnection(), $sql);
              if (mysqli_num_rows($results) > 0) {
                    if ($array['TransAmount'] >= 1 & $accountNo == "TP") {

            //call a function to create a loan account for product third party insurance and send customer the details on sms
            
             createLoan($accountNo);
        }
        if ($array['TransAmount'] >= 1 & $accountNo === "CO") {
            //call a function to create a loan account for product third party insurance and send customer the details on sms
             createLoan($accountNo);
        }
              }   else if (mysqli_num_rows($results) == 0){
            //call a function to create a loan account for product third party insurance and send customer the details on sms
            $res = createClientAcc();
            print_r($res);
            if (isset($res['clientId'])) {
                createLoan($accountNo);
         }
         
             }
             
             
            }
         else {
             
         }
            // call a function to deposit to pending payments
            //$response = DepositAccount($savingsAcc, $array['TransTime'], $array['TransAmount'], $array['TransID']);
        
    }
    
}       

    function createLoan($accountNo) {
        $loansarray = Array();
        $loansarrays = Array();

         $sql = "SELECT id,short_name,name,currency_code,principal_amount,nominal_interest_rate_per_period,number_of_repayments,fund_id,interest_method_enum,grace_on_principal_periods,amortization_method_enum,grace_on_interest_periods,is_equal_amortization FROM m_product_loan where short_name='$accountNo'";

        $result = mysqli_query(getDbConnection(), $sql);

        while ($row = mysqli_fetch_array($result)) {
            // add loans to array
            $loansarray["short_name"] = $row["short_name"];
            $loansarray["details"] = $row;
            array_push($loansarrays, $loansarray);
        }
        //   die(print_r($loansarrays));
        $applied = 0;
        foreach ($loansarrays as $loandetails) {
            //apply loan here
            if (strtoupper($accountNo) == strtoupper(trim($loandetails["short_name"]))) {
                $applied = 1;

                $charges = array();
                appyLoan($loandetails["details"]["principal_amount"], $loandetails["details"]["number_of_repayments"], $loandetails["details"]["nominal_interest_rate_per_period"], $loandetails["details"]["id"], $loandetails["details"]["fund_id"], $loandetails["details"]["interest_method_enum"], $loandetails["details"]["grace_on_principal_periods"], $loandetails["details"]["amortization_method_enum"], $loandetails["details"]["grace_on_interest_periods"], $loandetails["details"]["is_equal_amortization"]);
            }
        }
    }

    function createClientAcc() {
        $details = Array();
        $details['firstname'] = $_SESSION['fname'];

        $details['lastname'] = $_SESSION['lname'];
        if ($_SESSION['lname'] == "") {
            $details['lastName'] = $_SESSION['mname'];
        }
        $details['officeId'] = 1;
        $details['active'] = true;
        $details['dateFormat'] = "dd MMMM yyyy";
        $details['activationDate'] = date("d F Y");
        $details['locale'] = "en";
        $details['mobileNo'] = $_SESSION['phone'];
        $details['savingsProductId'] = 2;
        $url = "https://192.168.1.243:8443/fineract-provider/api/v1/clients?tenantIdentifier=default";
        $response = get_details_to_mifos($url, $details);

        //create deposit account for registration
        $res = json_decode($response, TRUE);
        return $res;
    }

    function appyLoan($principal, $loanTermFreq, $interestRate, $productId, $fundID, $interestType, $gracePrincipal, $amotizaType, $graceOnInt, $isEqualAmortization) {
        $phoneNumber = trim(substr($_SESSION['phone'], -9));
        $sql = "SELECT id, account_no, external_id FROM m_client where RIGHT(mobile_no, 9) like '%$phoneNumber%'";
        $result = mysqli_query(getDbConnection(), $sql);

        if (mysqli_num_rows($result) > 0) {

            // output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                // echo "id: " . $row["id"] . " - Name: " . $row["account_no"] . " " . $row["external_id"] . "<br>";
                $details = Array();
                $details['clientId'] = $row['id'];
                $details['productId'] = $productId;
                $details['principal'] = $principal;
                $details['loanTermFrequency'] = $loanTermFreq;
                $details['loanTermFrequencyType'] = 0;
                $details['numberOfRepayments'] = $loanTermFreq;
                $details['repaymentEvery'] = 1;
                $details['repaymentFrequencyType'] = 0;
                $details['amortizationType'] = $amotizaType;
                $details['interestType'] = $interestType;
                $details['graceOnPrincipalPayment'] = $gracePrincipal;
                $details['graceOnInterestPayment'] = $graceOnInt;
                $details['fundId'] = $fundID;
                $details['isEqualAmortization'] = $isEqualAmortization;
                $details['interestCalculationPeriodType'] = 1;
                $details['transactionProcessingStrategyId'] = 1;
                $details['interestRatePerPeriod'] = $interestRate;
                $details['expectedDisbursementDate'] = date("d F Y");
                $details['submittedOnDate'] = date("d F Y");
                $details['locale'] = "en";
                $details['loanType'] = 'individual';
                $details['dateFormat'] = "dd MMMM yyyy";
                //$details['charges'] = $charges;

                $url = "https://192.168.1.243:8443/fineract-provider/api/v1/loans?tenantIdentifier=default";
                
                $res = json_decode(get_details_to_mifos($url, $details), TRUE);
                
                if (!isset($res['clientId']) && $res['clientId'] != $row['id']) {
                    $msg = "We were unable to process your loan application.Please contact our office on 0724379003 for instructions";
                    SendCustomerSms(array("message" => $msg, "phone" => $phoneNumber));
                } else {
                    $loanId = $res['loanId'];
                    $details = Array();
                    $details['approvedOnDate'] = date("d F Y");
                    $details['approvedLoanAmount'] = $principal;
                    $details['expectedDisbursementDate'] = date("d F Y");
                    $details['disbursementData'] = [];
                    $details['locale'] = "en";
                    $details['dateFormat'] = "dd MMMM yyyy";
                    $url = "https://192.168.1.243:8443/fineract-provider/api/v1/loans/$loanId?tenantIdentifier=default&command=approve";

                    $res = json_decode(get_details_to_mifos($url, $details), TRUE);
                    
                    if (isset($res['clientId'])) {

                        $loanId = $res['loanId'];
                        $details = Array();
                        $details['transactionAmount'] = $principal;
                        $details['actualDisbursementDate'] = date("d F Y");
                        $details['paymentTypeId'] = 4;
                        $details['locale'] = "en";
                        $details['dateFormat'] = "dd MMMM yyyy";
                        $url = "https://192.168.1.243:8443/fineract-provider/api/v1/loans/$loanId?tenantIdentifier=default&command=disburse";

                        $res = json_decode(get_details_to_mifos($url, $details), TRUE);
                        
                    }
                    if (isset($res['loanId'])) {
                        $loanId = $res['loanId'];

                        $queryaccount = mysqli_query(getDbConnection(), "select account_no from m_loan WHERE id='$loanId' LIMIT 1") or die(mysqli_error(getDbConnection()));
                        while ($rowss = mysqli_fetch_array($queryaccount)) {
                            echo $accountid = $rowss["account_no"];
                        }
                        $querysum = mysqli_query(getDbConnection(), "SELECT MAX(installment) AS installment,principal_amount,interest_amount FROM `m_loan_repayment_schedule` where loan_id='$loanId' LIMIT 1") or die(mysqli_error(getDbConnection()));
                        while ($rows = mysqli_fetch_array($querysum)) {
                            $repay = number_format((float) $rows["principal_amount"], 2, '.', '');
                            $days = $rows["installment"];
                        }
                        echo $msg = "City Corporation Ltd, Insurance processed, start paying KES. $repay  daily to payill 715423 and account number $accountid for $days days.";
                        SendCustomerSms(array("message" => $msg, "phone" => $_SESSION['phone']));
                    }
                }
            }
        } else {
            echo $msg = "Invalid phone number!";
            sendmessage($msg);
        }


        mysqli_close(getDbConnection());
    }

    function DepositAccount($savingsact, $date, $amount, $receipt) {

        $date = date("j M Y", strtotime($date));

        $serverurl = "https://192.168.1.243:8443";

        $curl = curl_init();

//curl_setopt ($curl, CURLOPT_CAINFO, "cacert.pem.txt");

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8443",
            CURLOPT_URL => $serverurl . "/fineract-provider/api/v1/savingsaccounts/" . $savingsact . "/transactions?command=deposit&tenantIdentifier=default",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{

  "locale": "en",

  "dateFormat": "dd MMMM yyyy",

  "transactionDate": "' . $date . '",

  "transactionAmount": "' . $amount . '",

  "paymentTypeId": "4",

  "accountNumber": "",

  "checkNumber": "",

  "routingCode": "",

  "receiptNumber": "' . $receipt . '",

  "bankNumber": "' . $receipt . '"

}',
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic YWRtaW46cGFzc3dvcmQ=",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 4ceb67bb-486a-9ecd-77ea-3f7639d451d4"
            ),
        ));



        $response = curl_exec($curl);

        $err = curl_error($curl);



        curl_close($curl);



        if ($err) {

            echo "cURL Error #:" . $err;
        } else {

            return $response;
        }
    }

    function addSavingsAccount($clientId) {    // Date Format 27 October 2018
        $serverurl = "https://192.168.1.243";
        $curl = curl_init();
//curl_setopt ($curl, CURLOPT_CAINFO, "cacert.pem.txt");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt_array($curl, array(
            CURLOPT_PORT => "8443",
            CURLOPT_URL => $serverurl . "/fineract-provider/api/v1/savingsaccounts?tenantIdentifier=default",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
  "clientId": ' . $clientId . ',
  "productId": 1,
  "locale": "en",
  "dateFormat": "dd MMMM yyyy",
  "submittedOnDate": "29 January 2017"
}',
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic YWRtaW46cGFzc3dvcmQ=",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 4ceb67bb-486a-9ecd-77ea-3f7639d451d4"
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

    function LoanAccount($loanacct, $date, $amount, $receipt) {

        $loanacct = substr($loanacct, -9);

        echo $date = date("d F Y", strtotime($date));

        echo $serverurl = "https://192.168.1.243:8443/fineract-provider/api/v1/loans/$loanacct/transactions?command=repayment&tenantIdentifier=default";

        // {"paymentTypeId":1,"transactionAmount":1696,"transactionDate":"07 December 2018","receiptNumber":"ML77DCAVPL","locale":"en","dateFormat":"dd MMMM yyyy"}



        $curl = curl_init();

//curl_setopt ($curl, CURLOPT_CAINFO, "cacert.pem.txt");

        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_PORT => "8443",
            CURLOPT_URL => $serverurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{

  "locale": "en",

  "dateFormat": "dd MMMM yyyy",

  "transactionDate": "' . $date . '",

  "transactionAmount": "' . $amount . '",

  "paymentTypeId": "4",

  "accountNumber": "",

  "checkNumber": "",

  "routingCode": "",

  "receiptNumber": "' . $receipt . '",

  "bankNumber": "' . $receipt . '"

}',
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic YWRtaW46cGFzc3dvcmQ=",
                "cache-control: no-cache",
                "content-type: application/json",
                "postman-token: 4ceb67bb-486a-9ecd-77ea-3f7639d451d4"
            ),
        ));



        $response = curl_exec($curl);

        //$result = ( json_decode($response));
        //print_r($result);
        //echo $result->defaultArray;
        //echo "\n"."\n". $result['defaultUserMessage'];

        $err = curl_error($curl);



        curl_close($curl);



        if ($err) {

            echo "cURL Error #:" . $err;
        } else {

            return $response;
        }
    }

    function SendCustomerSms($smsdetails) {
        //$username = "krufed";
        //$apiKey = "dca4182bf9bde4c0b0b9952259089c3cb10f2334f0099ac61c1730180f55f190";
        $username = "city";
        $apiKey = "265ac602db29596b17a03b87d27dcf564efa1dffa61019ae0a3c0b03da4a928f";
        $AT = new AfricasTalking($username, $apiKey);

// Get one of the services
        $sms = $AT->sms();

// Use the service
//try{

        try {
            // Thats it, hit send and we'll take care of the rest
            $result = $sms->send([
                'from' => "CITYCORP",
                'to' => $smsdetails["phone"],
                'message' => $smsdetails["message"]
            ]);

            print_r($result);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
        session_destroy();
    }
    