<?php

session_start();
include 'AfricasTalkingGateway.php';
$servername = "localhost:3306";
$username = "root";
$password = "mysql";
$dbname = "mifostenant-default";

require 'autoload.php';

use AfricasTalking\SDK\AfricasTalking;

// Create connection
global $conn;
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendmessage($msg) {
    $shortCode = 40495;
    $phoneNumber = $_SESSION["phone"];
    $keyword = "";
    $retryDurationInHours = "2";
    $linkId = "";
//$username = "city";
//$apiKey  = "d0f242c73a33b50b779704b4600c81867e31c25915bef344d43797a63ee2715d";
    $username = "krufed";
    $apiKey = "87ad91890e1fa73f90734f521baffd3af452360bb6c269f95fd83c32d3062bff";

    $AT = new AfricasTalking($username, $apiKey);

// Get one of the services
    $sms = $AT->sms();

// Use the service

    $result = $sms->sendPremium([
        'to' => "+" . $phoneNumber,
        'message' => $msg,
        'from' => $shortCode,
        'keyword' => $keyword,
        'retryDurationInHours' => $retryDurationInHours,
        'linkId' => $linkId
    ]);
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

    if ($conn->query($sql) === TRUE) {
        // "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

function saveInDb2($data) {
    global $conn;
    $time = date("YmdHis");
//businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL
    $sql = "INSERT INTO stk_push_requests (TransactionType,TransID,TransTime,TransAmount,BusinessShortCode,BillRefNumber,InvoiceNumber,OrgAccountBalance,ThirdPartyTransID,MSISDN,FirstName,MiddleName,LastName)
VALUES ('" . $data['TransactionType'] . "','" . $data['TransID'] . "','" . $data['TransTime'] . "', '" . $data['TransAmount'] . "','" . $data['BusinessShortCode'] . "','" . $data['BillRefNumber'] . "','" . $data['InvoiceNumber'] . "','" . $data['OrgAccountBalance'] . "','" . $data['ThirdPartyTransID'] . "','" . $data['MSISDN'] . "','" . $data['FirstName'] . "','" . $data['MiddleName'] . "','" . $data['LastName'] . "')";

    if ($conn->query($sql) === TRUE) {
        // "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

confirmPayment($merchantid, $requestid, "tieet", "200", "254711401187");

function confirmPayment($array) {
    global $conn;


    $resultDesc = $array["resultDesc"];
    $resultCode = $array['resultCode'];
    $merchantRequestID = $array['merchantRequestID'];
    $checkoutRequestID = $array['checkoutRequestID'];
    $mpesaReceiptNumber = $array['mpesaReceiptNumber'];
    $transactionDate = $array['transactionDate'];
    $phoneNumber = $array['phoneNumber'];
    $_SESSION['phone'] = $phoneNumber;

////businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL
    $sql = "SELECT id,account_reference,phone_number,transaction_desc,amount from stk_push_requests where merchantID like '%$merchantRequestID%' AND checkoutid like '%$checkoutRequestID%'";
//
//
    $result = $conn->query($sql);
//    //print_r($result);
    while ($row = mysqli_fetch_array($result)) {
//        //  print_r($row);
        if ($row["id"]) {
            
        
        $transactiondesc = $row['transaction_desc'];
        $accountReference = $row['acount_reference'];
        $receipt = $mpesaReceiptNumber;
        $date = date("YmdHis");
        $amount = $row['amount'];


        $sqll = "UPDATE  stk_push_requests SET result_desc='$resultDesc', result_code='$resultCode',mpesa_receipt_number= '$mpesaReceiptNumber',confirm_date='$date' WHERE id='" . $row["id"] . "' ";
        // echo $sqll;
         $conn->query($sqll) or die($conn->error);
        //update loan or deposit payment
        if (stripos("loan", $transactiondesc) !== false) {
            LoanAccount($accountReference, $date, $amount, $receipt);
        } else if (stripos("deposit", $transactiondesc) !== false) {


            DepositAccount($accountReference, $date, $amount, $receipt);
        }

        if (stripos("Member Registration", $accountReference) !== false) {
            $sql = "SELECT id, account_no, external_id,display_name FROM m_client where mobile_no=$phoneNumber";
            $result = $conn->query($sql) or die($conn->error);

            //echo $msg = "Unable to activate client account";
           
            //print_r($result);
            while ($row = mysqli_fetch_array($result)) {
                $acc = $row['id'];
                // echo $msg = "Unable to activate client account";
                $file = fopen('test.log', 'a');
                fwrite($file, "Client" . $acc);

                $url = "https://localhost:8443/fineract-provider/api/v1/clients/$acc?tenantIdentifier=default&command=activate";
                $details = Array();
                $details['locale'] = "en";
                $details['activationDate'] = date("d F Y");
                $details['dateFormat'] = "dd MMMM yyyy";
                $res = json_decode(get_details_to_mifos($url, $details), TRUE);
                // var_dump($res);
                if (isset($res['clientId']) && $res['clientId'] != "") {
                    $acc = $row['id'];
                    $url = "https://localhost:8443/fineract-provider/api/v1/savingsaccounts?tenantIdentifier=default";
                    $details = Array();
                    $details['locale'] = "en";
                    $details['clientId'] = $acc;
                    $details['productId'] = 1;
                    $details['submittedOnDate'] = date("d F Y");
                    $details['dateFormat'] = "dd MMMM yyyy";
                    $res = json_decode(get_details_to_mifos($url, $details), TRUE);

                    if (isset($res['savingsId']) && $res['savingsId'] != "") {
                        $savAcc = $res['savingsId'];

                        $url = "https://localhost:8443/fineract-provider/api/v1/savingsaccounts/$savAcc?tenantIdentifier=default&command=approve";
                        $details = Array();
                        $details['locale'] = "en";
                        $details['approvedOnDate'] = date("d F Y");
                        $details['dateFormat'] = "dd MMMM yyyy";
                        $res = json_decode(get_details_to_mifos($url, $details), TRUE);


                        if (isset($res['savingsId']) && $res['savingsId'] != "") {
                            $savAcc = $res['savingsId'];

                            $url = "https://localhost:8443/fineract-provider/api/v1/savingsaccounts/$savAcc?tenantIdentifier=default&command=activate";
                            $details = Array();
                            $details['locale'] = "en";
                            $details['activatedOnDate'] = date("d F Y");
                            $details['dateFormat'] = "dd MMMM yyyy";
                            $res = json_decode(get_details_to_mifos($url, $details), TRUE);
                            // var_dump($savAcc);

                            if (isset($res['savingsId']) && $res['savingsId'] != "") {
                                $respo = json_decode(DepositAccount($res['savingsId'], $date, $amount, $receipt), true);
                                if (isset($respo['savingsId']) && $respo['savingsId'] != "") {
                                    echo $msg = "Dear " . $row['display_name'] . " your account has been activated, kindly reply with E1 to check and apply a loan.";
                                    sendmessage($msg);
                                }

                                //sendmessage($msg);
                            } else {

                                echo $msg = "Unable to activate savings account";
                                $file = fopen('error.log', 'a');
                                fwrite($file, $msg);
                            }
                        } else {
                            echo $msg = "Unable to approve savings account";
                            $file = fopen('error.log', 'a');
                            fwrite($file, $msg);
                        }
                    } else {
                        echo $msg = "Unable to create savings account";
                        $file = fopen('error.log', 'a');
                        fwrite($file, $msg);
                    }
                } else {
                    echo $msg = "Unable to activate client account";
                    $file = fopen('error.log', 'a');
                    fwrite($file, $msg);
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
        $conn->close();
    }
}}
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

    function confirmC2BPayment($array) {
        global $conn;
        if ($array['BillRefNumber'] != "") {
            $accountNo1 = $array['BillRefNumber'];
            $accountNo2 = str_replace(' ', '', $accountNo1);
            $accountNo = strtoupper($accountNo2);
            $mobile = $array['MSISDN'];

            $sql = "SELECT id,account_no,display_name from m_client where account_no like '%$accountNo%' or display_name like '%$accountNo%'";
            $result = $conn->query($sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $cid = $row['id'];
                    if ($array['TransAmount'] > 1000) {
                        $sql = "SELECT id,client_id,account_no from m_savings_account where client_id='$cid' group by client_id";
                        $result = $conn->query($sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                echo $savingsAcc = $row['accoun_no'];
                                DepositAccount($savingsAcc, $array['TransTime'], $array['TransAmount'], $array['TransID']);
                            }
                        } else {
                            echo 'client has no savings account';
                        }
                    } else {
                        $sql = "SELECT id,client_id,account_no from m_loan where client_id='$cid' group by client_id";
                        $result = $conn->query($sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                echo $row['account_no'];
                                LoanAccount($row['account_no'], $array['TransTime'], $array['TransAmount'], $array['TransID']);
                            }
                        } else {
                            echo 'client has no loan account';
                        }
                    }
                }
            }
        } else {

            echo 'no client found';
        }

//businessShortCode,timestamp,transactionType,amount,phoneNumber,callBackURL
        // $sql = "SELECT id,accountReference,phoneNumber,transactionDesc,amount,MpesaReceiptNumber from stk_push_requests where merchantID like '%$merchantid%' AND checkoutID like '%$requestid%'";
        //  $result = $conn->query($sql);
        //print_r($result);
//    while ($row = mysqli_fetch_array($result)) {
//        //  print_r($row);
//        if ($row["id"]) {
//            $transactiondesc = $row["transactionDesc"];
//            $accountReference = $row["accountReference"];
//            $phoneNumber = $row["phoneNumber"];
//            $amount = $row["amount"];
//            $receipt = $row["MpesaReceiptNumber"];
//            $date = date("YmdHis");
//            $sqll = "UPDATE  stk_push_requests SET MpesaReceiptNumber= '$receiptno',confirmTime='$date' WHERE id='" . $row["id"] . "' ";
//
//            // echo $sqll;
//            $conn->query($sqll) or die($conn->error);
//            //update loan or deposit payment
//            if (stripos("loan", $transactiondesc) !== false) {
//                LoanAccount($accountReference, $date, $amount, $receipt);
//            } else if (stripos("deposit", $transactiondesc) !== false) {
//                DepositAccount($accountReference, $date, $amount, $receipt);
//            }
//            return TRUE;
//        } else {
//            return FALSE;
//        }
//    }


        $conn->close();
    }

    function DepositAccount($savingsact, $date, $amount, $receipt) {
        $date = date("j M Y", strtotime($date));
        $serverurl = "https://localhost:8443";
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

    function LoanAccount($loanacct, $date, $amount, $receipt) {
        $loanacct = substr($loanacct, -9);
        $date = date("d F Y", strtotime($date));
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
        $result = ( json_decode($response));
        print_r($result);
        echo $result->defaultArray;
        //echo "\n"."\n". $result['defaultUserMessage'];
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    function SendCustomerSms($smsdetails) {
// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
        $recipients = $smsdetails['phone'];
// And of course we want our recipients to know what we really do
        $message = $smsdetails['message'];
// Create a new instance of our awesome gateway class

        $gateway = new AfricasTalkingGateway($settings["afist_username"], $settings["afist_pwd"]);
// Any gateway errors will be captured by our custom Exception class below,
// so wrap the call in a try-catch block
        try {
// Thats it, hit send and we'll take care of the rest.
            $results = $gateway->sendMessage($recipients, $message, $settings["afist_account_name"]);
            foreach ($results as $result) {
                
            }
        } catch (AfricasTalkingGatewayException $e) {
            echo "Encountered an error while sending: " . $e->getMessage();
        }
    }
    