<?php

function convertToDate($epochTime){
    $epoch = $epochTime-25200;
    $dt = new DateTime("@$epoch");
    return $dt->format('m-d-Y g:ia');
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

function formatEmailQuery($emailsArray){
    $emails = array();
    foreach ($emailsArray as $email) {
        $emails[] = "'".addslashes($email)."'";
    }

    $emails = implode(",", $emails);

    return $emails;
}

function groupByEmail($subscriberArray){
    $newArray = array();
    foreach ($subscriberArray as $info) {
        $emailaddres = $info['emailaddress'];
        $key = $info['listid'];
        $newArray[$emailaddres][$key] = $info;
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

?>
