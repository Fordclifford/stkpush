<?php

session_start();
require 'autoload.php';

use AfricasTalking\SDK\AfricasTalking;

// $sessionId   = $_POST["sessionId"];
//$serviceCode = $_POST['to'];
$phoneNumber = $_POST["from"];
$text = $_POST['text'];
$linkId = $_POST["linkId"];
$loansarrays=array();
//
//
//
//$from = $_POST['from'];
//// Used To bill the user for the response
//$date        = $_POST["date"]; // The time we received the message
//$id          = $_POST["id"];   // A unique id for this message

$phone = "254" . substr($phoneNumber, -9);
$_SESSION['linkId'] = $linkId;
$text = str_replace(' ', '', $text);
$_SESSION["text"] = strtoupper($text);
$_SESSION["shortcode"] = "40945";
$_SESSION["phone"] = $phone;

if (strpos(trim($text), '*') !== false) {
 $prefix = strtoupper(trim(substr($text, 0,2)));  // returns "query message"  
}
else{
$prefix = strtoupper(trim(substr($text, 0, 4)));  // returns "query message"   
}
$txt = trim(substr($text, 3, 40));  // returns "query message"    
//1.	EXPERT REGISTRATION 

$pref = preg_replace('/[^a-zA-Z0-9+]/', '', $prefix);

$number = preg_replace('/[^0-9]+/', '', $prefix);

$file = fopen('error.log', 'r+');
fwrite($file, $number . "" . $text . date('YmdHis') . $txt . "linkid" . $linkId . "<br>");

switch (strtoupper($pref)) {

    case "":
        $msg = "Thank you for contacting City Corporation \n";
        $msg .= "To continue in English reply with: \n";
        $msg .= "1 \n ";  //E0
        $msg .= "Kuendelea Kwa kiswahili jibu kwa: \n";
        $msg .= "2 "; //K0
//       $msg .= "  3- For Mini-Ledger ";
        sendmessage($msg);
        echo $msg;
        break;

    case "1":
	$client_id=getMemberByPhone($_SESSION['phone']);
	if(!empty($client_id)){   //existing client
        $msg = "Thank you for contacting City Corporation \n";
        $msg .= "Kindly reply with: \n";
        $msg .= " E1 - To appy for a loan \n ";
        $msg .= " E2- For member statements \n ";
        $msg .= " E3- For Mini-Ledger ";
	}
	else{
	$msg = "Kindly ensure you have at least KES. 220 on mpesa for registration then reply with: \n";
        $msg .= " E6*firstname lastname*ID e.g\n";
        $msg .= " E6*John Doe*123456";	
	}
//       $msg .= " E3- For Mini-Ledger ";
        sendmessage($msg);
        echo $msg;
        break;
//    case "Eo":
//        $msg = "Thank you for contacting City Corporation \n";
//        $msg .= "Kindly reply with: \n";
//        $msg .= " E1 - To appy for a loan \n ";
//        $msg .= " E2- For member statements \n ";
//        $msg .= " E3- For Mini-Ledger ";
////       $msg .= " E3- For Mini-Ledger ";
//        sendmessage($msg);
//        echo $msg;
//        break;

    case "2":
	$client_id=getMemberByPhone($_SESSION['phone']);
	if(!empty($client_id)){
        $msg = "Asante kwa kuwasiliana na City Corporation \n";
        $msg .= "Jibu kwa: \n";
        $msg .= " K1 - Kuomba mkopo \n ";
        $msg .= " K2- Kwa taarifa \n ";
        $msg .= " K3- Kwa Mini-Ledger ";
	}
	else{
		$msg = "Kujiandikisha hakikisha una KES 200 kwa mpesa halafu jibu kwa: \n";
        $msg .= " K6*jina la kwanza jina la pili*Nambari ya kitambulisho mfano\n";
        $msg .= " K6*John Doe*123456";
       	}
//       $msg .= " E3- For Mini-Ledger ";
        sendmessage($msg);
        echo $msg;
        break;
    
//    case "Ko":
//        $msg = "Asante kwa kuwasiliana na City Corporation \n";
//        $msg .= "Jibu kwa: \n";
//        $msg .= " K1 - Kuomba mkopo \n ";
//        $msg .= " K2- Kwa taarifa \n ";
//        $msg .= " K3- Kwa Mini-Ledger ";
////       $msg .= " E3- For Mini-Ledger ";
//        sendmessage($msg);
//        echo $msg;
//        break;

//    case 5:
//        $msg = "Please enter your firstname,lastname and ID Number as: \n";
//        $msg .= " 3#firstname#lastname#ID e.g\n";
//        $msg .= " 3#John#Doe#123456";
//
//        sendmessage($msg);
//
//
//        break;

    case "E1":
        if (verifyUser() == true) {
            $shortname="";
            $msg = "Please reply with Loan Product \n";
              $sql = "SELECT short_name,name,currency_code,principal_amount,nominal_interest_rate_per_period,number_of_repayments FROM m_product_loan";
                $result = mysqli_query(getDbConnection(), $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $shortname=$row["short_name"];
                      $msg .= $row["short_name"]."-".$row["name"]." at ".$row["currency_code"]." ".number_format($row["principal_amount"],0)."\n";  
                    
                      
                    }
                    $msg .= "Example \n";
                    $msg .= $shortname;
                } 
                else{
                    $msg .= " NO LOAN FACILITY AVAILABLE FOR NOW \n";
                }
//            $msg .= " EA - MOTORBIKE TVS100CC MAGWHEELS\n";
//            $msg .= " EB - MOTORBIKE BOXER100CC\n";
//            $msg .= " EC - MOTORBIKE BOXER125CC \n";
//            $msg .= " ED - COW LOAN \n";
//            $msg .= " EE - MOTORBIKE TVS125CC \n";
//            $msg .= " EF - MOTORBIKE TVS150CC \n";
//            $msg .= " EG - MOTORBIKE BOXER150CC \n";
//            $msg .= " EH - TUKTUK \n";
//            $msg .= " EI - HONDA 125cc-Electric \n";
//            $msg .= " EJ - HONDA 125CC KICKSTART \n";
//            $msg .= " EK - BAJAJ 100CC \n";
//            $msg .= " EL - MOTORBIKE BOXER X150CC\n";
//            $msg .= "Example \n";
//            $msg .= " EA ";
            echo $msg;
            sendmessage($msg);
        }
        break;

    case "E2":
         if (verifyUserK() == true) {
             $client_id=getMemberByPhone($_SESSION['phone']);
             $loanss=getMemberLoans($client_id);
            // die(print_r($loanss));
             $deposits=getMemberSavings($client_id);
         $msg = "Your Loans are as follows: \n";
         foreach ($loanss as $value) {
            $msg.= "A/c ".$value['account_no']." ".$value["name"]." ".$value["currency_code"]." ".number_format($value["principal_disbursed_derived"])." disbursed on ".$value["disbursedon_date"].".Balance ".number_format($value["principal_disbursed_derived"]-$value["total_repayment_derived"])." \n";  //get member loans 
         }
         $msg.= "Your Deposits: \n";
       //  $sql = "SELECT m_savings_account.account_no,m_savings_account.total_deposits_derived,m_savings_account.total_withdrawals_derived,m_savings_account.total_withdrawal_fees_derived,m_savings_account.client_id=$clientid and m_savings_account.status_enum=200" ;
        foreach ($deposits as $value) {
            $msg.= "A/c ".$value["account_no"]." ".$value["currency_code"]." ".number_format($value["total_deposits_derived"]-$value["total_withdrawals_derived"])." active from ".$value["activatedon_date"]." \n";  //get member loans 
         }
            echo $msg;
            sendmessage($msg);    
         }
      break;
      
      case "E3":
         if (verifyUserK() == true) {
         $msg= "Your Latest transactions: \n";
         $msg.= "KES 200 reference MTJ4344234 11th December,2018 \n";  //get member deposits
         $msg.= "KES 1 reference MHT45434 on 18th December,2018 \n";
            echo $msg;
            sendmessage($msg);    
         }
    case "K1":
        if (verifyUserK() == true) {
            $shortname="";
             $sql = "SELECT short_name,name,currency_code,principal_amount,nominal_interest_rate_per_period,number_of_repayments FROM m_product_loan";
                $result = mysqli_query(getDbConnection(), $sql);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result)) {
                        $shortname=$row["short_name"];
                      $msg .= $row["short_name"]."-".$row["name"]." at ".$row["currency_code"]." ".number_format($row["principal_amount"],0)."\n";  
                   
                    }
                     $msg .= "mfano: \n";
            $msg .=$shortname;
                } 
                else{
                    $msg .= "HAKUNA MKOPO UMEIDHINISHWA KWA SASA \n";
                }
//            $msg = "Jibu kwa aina ya mkopo \n";
//            $msg .= " KA - MOTORBIKE TVS100CC MAGWHEELS\n";
//            $msg .= " KB - MOTORBIKE BOXER100CC\n";
//            $msg .= " KC - MOTORBIKE BOXER125CC \n";
//            $msg .= " KD - COW \n";
//            $msg .= " KE - MOTORBIKE TVS125CC \n";
//            $msg .= " KF - MOTORBIKE TVS150CC \n";
//            $msg .= " KG - MOTORBIKE BOXER150CC \n";
//            $msg .= " KH - TUKTUK \n";
//            $msg .= " KI - HONDA 125cc-Electric \n";
//            $msg .= " KJ - HONDA 125CC KICKSTART \n";
//            $msg .= " KK - BAJAJ 100CC \n";
//            $msg .= " KL - MOTORBIKE BOXER X150CC \n";
           
            echo $msg;
            sendmessage($msg);
        }
        break;


    case "K4":
        if (verifyUserK() == true) {
            if (isset($txt) && $txt != "") {
                $sql = "SELECT * FROM m_client where external_id=$txt ";
                $result = mysqli_query(getDbConnection(), $sql);
                if (mysqli_num_rows($result) > 0) {
                    echo $msg = "Nambari ya Kitambulisho ambayo umetoa tayari imesajiliwa";
                    // sendmessage($msg);
                } else {
                    $sql = "UPDATE m_client SET external_id=$txt where mobile_no= " . $_SESSION['phone'];
                    $result = mysqli_query(getDbConnection(), $sql);
                    echo $msg = "Nambari yako ya kitambulisho imesasishwa \n";
                    sendmessage($msg);
                }
            } else {
                echo $msg = "Jibu batili, jaribu tena baadae \n";
                sendmessage($msg);
            }
        }
        break;
    case "E4":
        if (verifyUser() == true) {
            if (isset($txt) && $txt != "") {
                $sql = "SELECT * FROM m_client where external_id=$txt ";
                $result = mysqli_query(getDbConnection(), $sql);
                if (mysqli_num_rows($result) > 0) {
                    echo $msg = "The ID number you provided is already registered";
                    // sendmessage($msg);
                } else {
                    $sql = "UPDATE m_client SET external_id=$txt where mobile_no= " . $_SESSION['phone'];
                    $result = mysqli_query(getDbConnection(), $sql);
                    echo $msg = "Your ID number has been updated \n";
                    sendmessage($msg);
                }
            } else {
                echo $msg = "Invalid response kindly try again \n";
                sendmessage($msg);
            }
        }
        break;
/***********loan applications**********************/

        
        
   

    case "E6":

        if (isset($txt) && $txt != "") {
            $data = explode('*', $txt);

            $sql = "SELECT * FROM m_client where mobile_no= " . $_SESSION['phone'];

            $result = mysqli_query(getDbConnection(), $sql);

            $sq2 = "SELECT * FROM m_client where external_id= " . $data[2];

            $result2 = mysqli_query(getDbConnection(), $sq2);


            if (mysqli_num_rows($result) > 0) {
                $msg = "Mobile number provided is already registered \n";
                $msg .= "Kindly reply with: \n";
                $msg .= " E1 - To appy for a loan \n ";
                $msg .= " E2- For member statements \n ";
                $msg .= " E3- For Mini-Ledger ";
                echo $msg;
                sendmessage($msg);
            } else if (mysqli_num_rows($result2) > 0) {
                $msg = "ID number provided is already registered \n";
                $msg .= "Kindly enter the correct ID: \n";
                echo $msg;
                sendmessage($msg);
            } else {
                $details = Array();
                $details['firstname'] = $data[0];
                $details['lastname'] =$data[1];
                $details['officeId'] = 1;
                $details['active'] = false;
                $details['externalId'] = $data[2];
                $details['dateFormat'] = "dd MMMM yyyy";
                $details['locale'] = "en";
                $details['mobileNo'] = $_SESSION['phone'];
              // $details['savingsProductId'] =1;
                $url = "https://192.168.1.243:8443/fineract-provider/api/v1/clients?tenantIdentifier=default";
                $response = get_details_to_mifos($url, $details);

                //create deposit account for registration
                $res = json_decode($response, TRUE);


                if (isset($res['clientId']) && $res['clientId'] != "") {
                    $arr = Array();
                    $arr['amount'] =200;
                    $arr['phone'] = $_SESSION['phone'];
                    $arr['accountReference'] = "deposit";
                    $arr['transactionDesc'] = "deposit";
                    $url = "https://localhost:3000/mpesa/api/mpesa/push";
                    regPayment($url, $arr);

                    // $msg = "Your account has been created successfully, \n to activate your account \n ensure you have at least KES 220 in your mpesa then reply with ' p' to pay KSH 200 and get your account activated ";
                    sendmessage($msg);
                } else {
                    echo $msg = "U have already been registered or have an existing loan.Send E1 to 40945 to apply for loan or contact our office on 0724379003.Your ID is ".$res['clientId'];
                    sendmessage($msg);
                }
            }
        } else {
            echo $msg = "Invalid response kindly try again ";
            sendmessage($msg);
        }

        break;

    case "K6":

        if (isset($txt) && $txt != "") {
            $data = explode('*', $txt);

            $sql = "SELECT * FROM m_client where mobile_no= " . $_SESSION['phone'];

            $result = mysqli_query(getDbConnection(), $sql);

            if (mysqli_num_rows($result) > 0) {
                $msg = "Nambari ya simu ya mkononi imeorodheshwa tayari \n";
                $msg .= "Jibu kwa: \n";
                $msg .= " K1 - Kuomba mkopo \n ";
                $msg .= " K2- Kwa taarifa \n ";
                $msg .= " K3- Kwa Mini-Ledger ";
//       echo $msg;
                sendmessage($msg);
            } else {
                $details = Array();
                $details['firstname'] = $data[0];
              $details['lastname'] =".";
                $details['officeId'] = 1;
                $details['active'] = false;
                $details['externalId'] = $data[2];
                $details['dateFormat'] = "dd MMMM yyyy";
                $details['locale'] = "en";
				//$details['savingsProductId'] =1;
                $details['mobileNo'] = $_SESSION['phone'];
                
                
                $url = "https://192.168.1.243:8443/fineract-provider/api/v1/clients?tenantIdentifier=default";
				$file = fopen('error.log', 'a');
            fwrite($file,"*".$data[0]."*".$data[0]."*".$data[2].$_SESSION['phone'] );
                echo $response = get_details_to_mifos($url, $details);
                $res = json_decode($response, TRUE);
                //create deposit account for registration

                if (!empty($res['clientId']) && $res['clientId'] != "") {
                    $arr = Array();
                    $arr['amount'] = 200;
                    $arr['phone'] = $_SESSION['phone'];
                    $arr['accountReference'] = "deposit";
                    $arr['transactionDesc'] = "Member Registration";
                    $url = "https://192.168.1.243:3000/mpesa/api/mpesa/push";
                    regPayment($url, $arr);
				
                    //$msg = "Akaunti yako imeundwa kwa ufanisi, \ n kuamsha akaunti yako \ n hakikisha una angalau KES 220 katika mpesa wako kisha jibu na ' kp' kulipa KSH 200 na akaunti yako kuanzishwa ";
                    sendmessage($msg);
                } else {
						$file = fopen('error.log', 'a');
            fwrite($file, print_r($res));
                    echo $msg = "Tatizo dogo limetokea. Tafadhali jaribu baadaye tena ";
                    sendmessage($msg);
                }
            }
        } else {
            $msg = "Jibu batili, jaribu tena";
            sendmessage($msg);
        }

        break;
        
        
    case "K2":
        if (verifyUser() == true) {
       $client_id=getMemberByPhone($_SESSION['phone']);
             $loans=getMemberLoans($client_id);
             $deposits=getMemberSavings($client_id);
         $msg = "Mikopo yako: \n";
         foreach ($loans as $value) {
            $msg.= "A/c ".$value["account_no"]." ".$value["name"]." ".$value["currency_code"]." ".number_format($value["principal_disbursed_derived"])." disbursed on ".$value["disbursedon_date"].".Balance ".number_format($value["principal_disbursed_derived"]-$value["total_repayment_derived"])." \n";  //get member loans 
         }
         $msg.= "Pesa amabazo umetuma: \n";
       //  $sql = "SELECT m_savings_account.account_no,m_savings_account.total_deposits_derived,m_savings_account.total_withdrawals_derived,m_savings_account.total_withdrawal_fees_derived,m_savings_account.client_id=$clientid and m_savings_account.status_enum=200" ;
        foreach ($deposits as $value) {
            $msg.= "A/c ".$value["account_no"]." ".$value["currency_code"]." ".number_format($value["total_deposits_derived"]-$value["total_withdrawals_derived"])." active from ".$value["activatedon_date"]." \n";  //get member loans 
         }
            echo $msg;
            sendmessage($msg);    
         } 
        
        
        break;

    case "EP":
        if (verifyUser() == true) {
            $arr = Array();
            $arr['amount'] =200;
            $arr['phone'] = $_SESSION['phone'];
            $arr['accountReference'] = "deposit";
            $arr['transactionDesc'] = "Member Registration";
            $url = "https://127.0.0.1:3000/mpesa/api/mpesa/push";
            regPayment($url, $arr);
        }
        break;

    default:
         $sql = "SELECT id,short_name,name,currency_code,principal_amount,nominal_interest_rate_per_period,number_of_repayments,fund_id,interest_method_enum,grace_on_principal_periods,amortization_method_enum,grace_on_interest_periods,is_equal_amortization FROM m_product_loan";
                $result = mysqli_query(getDbConnection(), $sql);
               
                    while ($row = mysqli_fetch_array($result)) {
                                           // add loans to array
                      $loansarray["short_name"]=$row["short_name"];
                      $loansarray["details"]=$row;
                      array_push($loansarrays,$loansarray);
                      
                    }
                 //   die(print_r($loansarrays));
                    $applied=0;
        foreach ($loansarrays as $loandetails) {
        //apply loan here
    if(strtoupper($pref)==strtoupper(trim($loandetails["short_name"]))){
       $applied=1;
       if (verifyUser() == true) {
           //{"clientId":"3","productId":36,"disbursementData":[],"principal":95939,"loanTermFrequency":365,"loanTermFrequencyType":0,
            //"numberOfRepayments":365,"repaymentEvery":1,"repaymentFrequencyType":0,"interestRatePerPeriod":10,"amortizationType":1,
            //"isEqualAmortization":false,"interestType":0,"interestCalculationPeriodType":1,"allowPartialPeriodInterestCalcualtion":false,
            //"transactionProcessingStrategyId":1,"locale":"en","dateFormat":"dd MMMM yyyy","loanType":"individual","expectedDisbursementDate":"20 November 2018","submittedOnDate":"10 December 2018"}     
           $charges=array();
             appyLoan($loandetails["details"]["principal_amount"], $loandetails["details"]["number_of_repayments"],$loandetails["details"]["nominal_interest_rate_per_period"], $loandetails["details"]["id"], $loandetails["details"]["fund_id"], $loandetails["details"]["interest_method_enum"], $loandetails["details"]["grace_on_principal_periods"],$loandetails["details"]["amortization_method_enum"],$loandetails["details"]["grace_on_interest_periods"],$loandetails["details"]["is_equal_amortization"]);
        }  
        //($principal, $loanTermFreq, $interestRate, $productId, $fundID, $interestType, $gracePrincipal, $amotizaType, $graceOnInt, $isEqualAmortization)
        
    }
    
   
    
}
if(!$applied){
        $msg = "Didnt understand command,please retry \n";
        $msg .= "To continue in English reply with: \n";
        $msg .= "1 \n ";
        $msg .= "Kuendelea Kwa kiswahili jibu kwa: \n";
        $msg .= "2 ";
//       $msg .= "  3- For Mini-Ledger ";
        sendmessage($msg);
        echo $msg;
}

    //sendmessage($msg);
}



function getDbConnection() {
    $DB_HOST = '127.0.0.1:3306';
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

function regPayment($url, $arr) {
    $out = array_values($arr);
    $js = json_encode($arr);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        //curl_setopt ($curl, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem"),
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false),
        //CURLOPT_CAINFO=> dirname(__FILE__)."/cacert.pem",
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_PORT => "3000",
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $js,
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "content-type: application/json"
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

function verifyUser() {

    $sql = "SELECT mobile_no FROM m_client where mobile_no=" . $_SESSION['phone'];
   // die($sql);
    $result = mysqli_query(getDbConnection(), $sql);

    if (mysqli_num_rows($result) == 0) {
        $msg = "Kindly ensure you have at least KES. 220 on mpesa for registration then reply with: \n";
        $msg .= " E6*firstname lastname*ID e.g\n";
        $msg .= " E6*John Doe*123456";
        echo $msg;
        sendmessage($msg);
        return false;
    } else {
        $sql = "SELECT external_id,status_enum FROM m_client where mobile_no=" . $_SESSION['phone'];
        $result = mysqli_query(getDbConnection(), $sql);

        if (mysqli_num_rows($result) == 0) {
            $msg = "Please reply with your National ID number as: \n";
            $msg .= " E4*ID Number e.g \n";
            $msg .= " E4*98765433 ";
            echo $msg;
            sendmessage($msg);
            return false;
        }
        else if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['status_enum'] == 100) {
                    echo $msg = "To activate your account kindly pay KES 200 to Paybill 715423 Account No Register \n";
                    
                    sendmessage($msg);
                     $arr = Array();
                    $arr['amount'] = 200;
                    $arr['phone'] = $_SESSION['phone'];
                    $arr['accountReference'] = "Member Registration";
                    $arr['transactionDesc'] = "deposit";
                    $url = "https://192.168.1.243:3000/mpesa/api/mpesa/push";
                    regPayment($url, $arr);                   
                    return false;
                } else if ($row['status_enum'] == 300) {
                    return TRUE;
                }
            }
        } 
        else {
            return true;
        }
    }
}

function verifyUserK() {

    $sql = "SELECT mobile_no FROM m_client where mobile_no=" . $_SESSION['phone'];
    $result = mysqli_query(getDbConnection(), $sql);

    if (mysqli_num_rows($result) == 0) {
        $msg = "Kujiandikisha jibu na: \n";
        $msg .= " K6*jina la kwanza jina la pili*Nambari ya kitambulisho mfano\n";
        $msg .= " K6*John Doe*123456";
        echo $msg;

        sendmessage($msg);
        return false;
    } else {
        $sql = "SELECT external_id,status_enum FROM m_client where mobile_no=".$_SESSION['phone'];
        $result = mysqli_query(getDbConnection(), $sql);

        if (mysqli_num_rows($result) == 0) {
            $msg = "Tafadhali jibu kwa nambari yako ya kitambulisho kama\n";
            $msg .= " E4*ID Number mfano:\n";
            $msg .= " E4*98765433 ";
            echo $msg;
            sendmessage($msg);
            return false;
        } else if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {
                if ($row['status_enum'] == 100) {
                    echo $msg = "Ili kuamsha akaunti yako jibu na ' EP' kulipa KES 200 \n";
                    
                    $arr = Array();
                    $arr['amount'] = 200;
                    $arr['phone'] = $_SESSION['phone'];
                    $arr['accountReference'] = "Member Registration";
                    $arr['transactionDesc'] = "deposit";
                    $url = "https://192.168.1.243:3000/mpesa/api/mpesa/push";
                    regPayment($url, $arr);
					sendmessage($msg);
                    return false;
                } else if ($row['status_enum'] == 300) {
                    return TRUE;
                }
            }
        } else {
            return true;
        }
    }
}

function appyLoan($principal, $loanTermFreq, $interestRate, $productId, $fundID, $interestType, $gracePrincipal, $amotizaType, $graceOnInt, $isEqualAmortization) {

    $phoneNumber = $_SESSION['phone'];
    $sql = "SELECT id, account_no, external_id FROM m_client where mobile_no=$phoneNumber";
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
            $filee = fopen('error.log', 'a');
            fwrite($filee, json_encode($details));

            $res = json_decode(get_details_to_mifos($url, $details), TRUE);
            $file = fopen('error.log', 'a');
            fwrite($file, print_r($res));
            if (!isset($res['clientId']) && $res['clientId'] != $row['id']) {
                $msg = "We were unable to process your loan application.Please contact our office on 0724379003 for instructions";
                SendCustomerSms(array("message"=>$msg,"phone"=>$phoneNumber));
            }
            else{
                $msg = "City Corporation Ltd,Dear customer your loan application has been received and is being processed,ensure you present copies of your ID and a filled loan form to our office or any of our agents";
                 SendCustomerSms(array("message"=>$msg,"phone"=>$phoneNumber));
            }
        }
    } else {
        echo $msg = "Invalid phone number!";
        sendmessage($msg);
    }


    mysqli_close(getDbConnection());
}

function appyLoanK($principal, $loanTermFreq, $interestRate, $charges, $productId, $fundID, $interestType, $gracePrincipal, $amotizaType, $graceOnInt, $isEqualAmortization) {

    $phoneNumber = $_SESSION['phone'];
    $sql = "SELECT id, account_no, external_id FROM m_client where mobile_no=$phoneNumber";
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
            $details['charges'] = $charges;

            $url = "https://192.168.1.243:8443/fineract-provider/api/v1/loans?tenantIdentifier=default";

            $res = json_decode(get_details_to_mifos($url, $details), TRUE);
            $file = fopen('error.log', 'a');
            fwrite($file, print_r($res));

            if (!isset($res['clientId']) && $res['clientId'] != $row['id']) {
                $msg = "Tatizo dogo limetokea. Tafadhali jaribu tena baadaye";
                sendmessage($msg);
            }
        }
    } else {
        echo $msg = "Nambari ya simu isiyo sahihi!";
        sendmessage($msg);
    }


    mysqli_close(getDbConnection());
}

function sendmessage($msg) {
    $shortCode = $_SESSION["shortcode"];
    $phoneNumber = $_SESSION["phone"];
    $keyword = "Citycorp";
    $retryDurationInHours = "2";
    $linkId = $_SESSION['linkId'];
$username = "krufed";
$apiKey  = "dca4182bf9bde4c0b0b9952259089c3cb10f2334f0099ac61c1730180f55f190";
    //$username = "city";
    //$apiKey = "265ac602db29596b17a03b87d27dcf564efa1dffa61019ae0a3c0b03da4a928f";

    $AT = new AfricasTalking($username, $apiKey);

// Get one of the services
    $sms = $AT->sms();

// Use the service
//try{
    $params = [
	'username'=>"CITYCORP",
        'to' => "+".$phoneNumber,
        'message' => $msg,
        'from' => $shortCode,
        //'keyword' =>Null,
        'bulkSMSMode' => 0,
        'retryDurationInHours' => $retryDurationInHours,
        'linkId' => $linkId
    ];
    $result = $sms->sendPremium($params);
    print_r($result);

    $h = fopen('process.txt', 'r+');
    fwrite($h, var_export($result, true));

    $h2 = fopen('process.json', 'r+');
    fwrite($h2, var_export($params, true));


    /* } catch (Exception $e) {
      $file = fopen('process.log', 'r+');
      fwrite($file,$e->getMessage()."<br>");
      echo "Error: ".$e->getMessage();
      } */
    //$application = $AT->application();
    // session_destroy();
}


function sendmessagekeyword($msg) {
    $shortCode ="CITYCORP";
    $phoneNumber = $_SESSION["phone"];
    $keyword = "Citycorp";
    $retryDurationInHours = "2";
    $linkId = $_SESSION['linkId'];
//$username = "krufed";
//$apiKey  = "dca4182bf9bde4c0b0b9952259089c3cb10f2334f0099ac61c1730180f55f190";
    $username = "city";
    $apiKey = "265ac602db29596b17a03b87d27dcf564efa1dffa61019ae0a3c0b03da4a928f";

    $AT = new AfricasTalking($username, $apiKey);

// Get one of the services
    $sms = $AT->sms();

// Use the service
//try{
    $params = [
	//'username'=>"CITYCORP",
        'to' => "+".$phoneNumber,
        'message' => $msg,
        'from' => $shortCode,
        //'keyword' =>Null,
        'bulkSMSMode' => 0,
        'retryDurationInHours' => $retryDurationInHours,
        'linkId' => $linkId
    ];
    $result = $sms->sendPremium($params);
    print_r($result);

    $h = fopen('process.txt', 'r+');
    fwrite($h, var_export($result, true));

    $h2 = fopen('process.json', 'r+');
    fwrite($h2, var_export($params, true));


    /* } catch (Exception $e) {
      $file = fopen('process.log', 'r+');
      fwrite($file,$e->getMessage()."<br>");
      echo "Error: ".$e->getMessage();
      } */
    //$application = $AT->application();
    // session_destroy();
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


function SendCustomerSms($smsdetails){
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
	'from'=>"CITYCORP",
        'to'      => $smsdetails["phone"],
        'message' => $smsdetails["message"]
    ]);

    print_r($result);
} catch (Exception $e) {
    echo "Error: ".$e->getMessage();
}
    
}


function getMemberByPhone($phone){
    $id=0;
     $sql = "SELECT id, account_no, external_id FROM m_client where mobile_no =$phone";
    $result = mysqli_query(getDbConnection(), $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $id=$row["id"];
    }
    return $id;
    
}

function getMemberLoans($clientid){
    $details=array();
  $sql = "SELECT m_loan.id, m_loan.account_no,m_loan.currency_code,m_loan.approved_principal,m_loan.disbursedon_date,m_loan.principal_disbursed_derived,m_loan.total_repayment_derived,m_product_loan.name FROM m_loan join m_product_loan on m_loan.product_id=m_product_loan.id where m_loan.client_id=$clientid " ;//and m_loan.loan_status_id=200
  //die($sql); 
  $result = mysqli_query(getDbConnection(), $sql);
    while ($row = mysqli_fetch_assoc($result)) {
       array_push($details,$row);
    }   
    return $details;
}

function getMemberSavings($clientid){
    $details=array();
   $sql = "SELECT m_savings_account.currency_code,m_savings_account.account_no,m_savings_account.total_deposits_derived,m_savings_account.total_withdrawals_derived,m_savings_account.total_withdrawal_fees_derived from m_savings_account WHERE m_savings_account.client_id=$clientid " ; //and m_savings_account.status_enum=200
   //die($sql);
    $result = mysqli_query(getDbConnection(), $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($details,$row);
    }   
    return $details; 
    
}