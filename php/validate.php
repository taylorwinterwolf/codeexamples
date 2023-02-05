<?php
//THIS SCRIPT VALIDATES EMAIL ADDRESSES USING XVERIFY.COM, THIS SCRIPT SUPPORTS subscribe.php
class validate {

    function email($email){
        /*Response Code 1 – Valid Email Address
        Response Code 2 – Email Address Does Not Exist
        Response Code 3 – Unknown
        Response Code 4 – Fraud List
        Response Code 5 – High Risk Email Address
        Response Code 6 – Affiliate Is Blocked By Client
        Response Code 7 – Complainer Email Address
        Response Code 8 – Top Level Domain Blocked By Client
        Response Code 9 – Temporary/Disposable Email
        Response Code 10 – Keyword is Blocked By Client
        Response Code 11 – IP address – Country Not Allowed
        Response Code 12 – Block list from Client Settings
        Response Code 400 – Missing required fields
        Response Code 503 – Invalid API Key/Service Not Active
        Response Code 504 – User reach the API Limit*/

        $url = 'https://xverify.com/services/emails/verify/?email='.$email.'&type=json&apikey=KEYGOESHERE&domain=URLGOESHERE';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        $output = json_decode($output,TRUE);

        //printNeat($output);

        $responseCode = $output['email']['responsecode'];

        //echo"Response Code is: ".$responseCode;

        $safeCodes = array(1,3);

        if(is_array($output)){
            if(in_array($responseCode, $safeCodes)){
                return "valid";
            }else{
                return "invalid";
            }
        }else{
            return "unknown";
        }

    }

}

$validate = new validate();
?>
