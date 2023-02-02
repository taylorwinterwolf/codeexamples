<?php
//THIS SUPPORTS addCustomer.php 
function convertToDate($epochTime){
    $epoch = $epochTime-25200;
    $dt = new DateTime("@$epoch");
    return $dt->format('m-d-Y g:ia');
}

function objectToArray($d){
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
        * Return array converted to object
        * Using __FUNCTION__ (Magic constant)
        * for recursive call
        */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

function getGender($vendor, $productID){
    $female = array(
        'vendors' => array(
            'lodesire',
            'whyhelies',
            'oaformula',
            'strokeofg',
            'girlnow',
            'capturehim',
            'ldrforever',
            'psystrology'
        ),
        'productIDs' => array(
            '1w-trb',
            'w-1a',
            'w-2a',
            'w-2d',
            'w-3da',
            '17',
            '18w',
            '19w',
            '20w',
            '21w'
        )
    );

    $male = array(
        'vendors' => array(
            'mofingers',
            'girlnow',
            'oralfix',
            'lolust'
        ),
        'productIDs' => array(
            '1m-trb',
            'm-1a',
            'm-2a',
            'm-2d',
            'm-3da',
            '17m',
            '1ma',
            '2ma',
            '3ma',
            '4md',
            '2md',
            '3mda',
            '4mdd'
        )
    );

    if($vendor == 'txtyourex' || $vendor == 'txtromance'){
        if(in_array($productID, $male['productIDs'])){
            $gender = 'm';
        }else{
            $gender = 'f';
        }
    }elseif(in_array($vendor, $male['vendors'])){
        $gender = 'm';
    }else{
        $gender = 'f';
    }

    return $gender;

}

function printNeat($printThis='', $message='', $allow='yes'){
    if($allow == 'yes'){
        if (is_array($printThis)) {
            echo "<pre style='clear:both;'></br>".$message."</br>";
            print_r($printThis);
            echo "</pre>";
        }else{
            echo $message."</br>";
            echo $printThis."</br></br>";
        }
    }
}

function echoFunction($text, $allow='yes'){
    if($allow == 'yes'){
        echo $text."</br></br>";
    }
}

function printNeatLeft($printThis='', $message=''){

    if (is_array($printThis)) {
        echo "<div style='float:left;margin:20px;'>".$message."<pre>";
        print_r($printThis);
        echo "</pre></div>";
    }else{
        echo $message."</br>";
        echo $printThis."</br></br>";
    }

}

function printNeatVar($printThis='', $message=''){

    if (is_array($printThis)) {
        echo $message."<pre>";
        var_dump($printThis);
        echo "</pre>";
    }else{
        echo $message."</br>";
        echo $printThis."</br></br>";
    }

}

function printNeatExit($printThis='', $message=''){
    if (is_array($printThis)) {
        echo $message."<pre>";
        print_r($printThis);
        echo "</pre>";
    }else{
        echo $message."</br>";
        echo $printThis."</br></br>";
    }
    exit();
}

function xmlToArray($xml){
    $json = json_encode($xml);
    $array = json_decode($json,TRUE);
    return $array;
}

function printXML($xml, $message = ''){
    echo $message."</br>";
    echo '<pre>', htmlentities($xml), '</pre>';
}

function getEpochTimePassed($number, $time){

    $epoch = array('minutes' => '60', 'hours' => '3600', 'days' => '86400', 'weeks' => '604800', 'months' => '2629743', 'years' => '31556926'); //seconds

    $epochTimePassed = $number*$epoch[$time];

    return $epochTimePassed;
}

function getDateDiff($dt1, $dt2)
{
    $dt1 = new DateTime($dt1);
    $dt2 = new DateTime($dt2);
    //This returns amount of days passed
    return $dt1->diff($dt2)->format('%a');
}

function formatEmailQuery($emailsArray){
    $emails = array();
    foreach ($emailsArray as $email) {
        //printNeat($email);
        if($email != 'Email'){
            $emails[] = "'".addslashes($email)."'";
        }
    }

    //printNeat($emails, "Emails from formatEmailQuery");

    $emails = implode(",", $emails);

    return $emails;
}

function formatDataForQuery($dataArray){
    $items = array();
    foreach ($dataArray as $item) {
        //printNeat($item);
        if($item != 'Email'){
            $items[] = "'".addslashes($item)."'";
        }
    }

    //printNeat($emails, "Emails from formatEmailQuery");

    $items = implode(",", $items);

    return $items;
}

function groupByEmail($subscriberArray){
    $newArray = array();
    foreach ($subscriberArray as $info) {
        $emailaddres = $info['email'];
        $newArray[$emailaddres][] = $info;
    }

    return $newArray;
}

function formatSubscriberData($subscriberDataArray){
    $subscriberData = array();
    if (is_array($subscriberDataArray)) {
        foreach ($subscriberDataArray as $data) {
            $subscriberData[$data['fieldid']] = $data;
        }
    }
    return $subscriberData;
}

function getSandbox(){
    return 'yes';
}

function convertV4($order)
{
    $v4 = array();

    $productTitle = "";
    $productID = "";

    if (isset($order['lineItems'])) {
        foreach ($order['lineItems'] as $key => $item) {
            if ($key == 0) {
                $productTitle = $item['productTitle'];
                $productID = $item['itemNo'];
            } else {
                $productTitle .= " + " . $item['productTitle'];
                $productID .= " + " . $item['itemNo'];
            }
        }
    }

    $v4['corderamount'] = str_replace(".", "", $order['totalOrderAmount']);
    $v4['ccustshippingcountry'] = $order['customer']['shipping']['address']['country'];
    $v4['ctransaction'] = $order['transactionType'];
    $v4['ccustemail'] = $order['customer']['shipping']['email'];
    $v4['cproditem'] = $productID;
    $v4['cprodtitle'] = $productTitle;
    $v4['ctransreceipt'] = $order['receipt'];
    $v4['ccustfirstname'] = $order['customer']['shipping']['firstName'];
    $v4['ccustlastname'] = $order['customer']['shipping']['lastName'];
    $v4['ccustzip'] = $order['customer']['shipping']['address']['postalCode'];
    $v4['ccuststate'] = $order['customer']['shipping']['address']['state'];
    $v4['ccustcity'] = $order['customer']['shipping']['address']['city'];
    $v4['ctransvendor'] = $order['vendor'];

    return $v4;
}
?>
