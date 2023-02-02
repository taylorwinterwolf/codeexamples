<?php
ob_start();
$testing = 'false';

echo "<link rel='stylesheet' type='text/css' href='../style.css'>";
require('assets/functions.php');
require('class/functions.php');
require('class/validate.php');
require('class/maropostAPI.php');

if($testing == 'true'){
    printNeat($_REQUEST, "Request Info");
}

$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : "https://digitalromanceinc.com/404";

//CHECK TO SEE IF THE REDIRECT IS TO CLICKBANK OR TO ANY OF THE OTHERS LISTED BELOW
$checkString = array(
    '.pay.clickbank.net',
    'doesheloveyouquiz',
    '3magic',
    'review-your-cart',
    'review-cart',
    'getbacktogetherquiz'
);
foreach($checkString as $string){
    if(strpos($redirect, $string)){
        $appendEmail = true;
        break;
    }
}

$email = filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL);

if(strpos($email, "hotmail")){
    $isHotmail = true;
}

if($isHotmail != true){
    $checkEmail = $validate->email($email);
}

//If we don't have an email, send them to the redirect
if($isHotmail == true){
    //echo "You've got a bad email";
    header("Location: " . $redirect);
    exit();
}elseif($checkEmail == "invalid"){
    header("Location: " . $redirect);
    exit();
}else{
    if(isset($appendEmail)){
        $redirect .= "&email=".$email;
    }else{
        $redirect .= "?email=".$email;
    }
}

//SET CUSTOM FIELD PRODUCT
if(isset($_REQUEST['product'])){
    $product = $_REQUEST['product'];
}else{
    $product = "";
}

if(isset($_REQUEST['listname'])){
    $listName = $_REQUEST['listname'];

    if($listName == "DRF" || $listName == "DRM"){
        $listName = 'CUST';
    }

    $needID = ['DRWN','DRMN','AFF','WNEWS','MNEWS'];
    if(in_array($listName, $needID)){
        $listID = $functions->getListID($listName);
    }
}

//SET CUSTOM FIELD GENDER
$customFields = array();
if(isset($_REQUEST['gender'])){
    $gender = $_REQUEST['gender'];
    $customFields['gender'] = $gender;
}else{
    $gender = 'f';
}

//SET VARIABLE FOR ZONE, NOTE THAT ZONES ARE ONLY FOR QUIZES
if(isset($_REQUEST['zone'])){
    $zone = $_REQUEST['zone'];
}else{
    $zone = "";
}

//SET CUSTOM FIELD FOR CUSTOMER, LEAD, NEWSLETTER OR AC
if(isset($listName)){
    $customFieldName = $functions->getCustomFieldName($product, $listName);
    //printNeat($customFieldName, "Custom Field Name");
    $customFields[$customFieldName] = 1;
}

//SET CUSTOM TAG FOR AFFILIATE ID
if(isset($_REQUEST['aff_id'])){
    $affID = $_REQUEST['aff_id'];
    $customFields['affiliate_id'] = $affID;
}

//SET CUSTOM TAG FOR AFFILIATE TYPE
if(isset($_REQUEST['affiliate_type'])){
    $affType = $_REQUEST['affiliate_type'];
    $customFields['affiliate_type'] = $affType;
}

//SET CUSTOM BOOLEAN TAG WHICH IS A VARIABLE CUSTOM FIELD * NOTE CUSTOM FIELD HAS TO EXIST IN MAROPOST
if(isset($_REQUEST['custom-tag'])){
    $fieldName = $_REQUEST['custom-tag'];
    $customFields[$fieldName] = 1;
}

if(empty($customFields)){
    $customFields = "";
}

/*
 * THIS SECTION ADDS A SUBSCRIBER EITHER TO A SPECIFIC LIST OR JUST TO THE SYSTEM
 * IF THE SUBSCRIBER IS ALREADY IN THE SYSTEM THIS UPDATES THEM WITH ANY NEW INFORMATION BEING PASSED
 * THE ONLY LISTS WE HAVE ARE MENS AND WOMENS NEWSLETTER AND AFFILIATES
 * EVERY THING ELSE GETS SENT TO A JOURNEY/AR SEQUENCE, SEE BELOW TO ADD TO JOURNEY
*/

//IF THE SUBSCRIBER IS ALREADY IN THE SYSTEM THIS WILL GRAB ALL OF THEIR INFORMATION
$contactInfo = $maropostapi->getContactInfo($email);
//printNeat($contactInfo, "Contact Info");

if(isset($contactInfo['first_name']) && !empty($contactInfo['first_name'])){
    $fname = $contactInfo['first_name'];
}elseif(isset($_REQUEST['fname'])){
    $fname = $_REQUEST['fname'];
}else{
    $fname = "";
}

if(isset($contactInfo['last_name']) && !empty($contactInfo['last_name'])){
    $lname = $contactInfo['last_name'];
}elseif(isset($_REQUEST['lname'])){
    $lname = $_REQUEST['lname'];
}else{
    $lname = "";
}

if(isset($listName) && isset($listID)){
    $maropostapi->addContactToList($listID, $email, $customFields, $fname, $lname);
    //printNeat($addToList, "CONTACT ADDED TO LIST");
}else{
    //ONLY ADD CONTACT TO MAROPOST IF THEY ARE NOT ALREADY IN IT
    if(!isset($contactInfo['id'])){
        $maropostapi->addContact($email, $customFields, $fname, $lname);
    }else{
        //IF CONTACT IS ALREADY IN THE SYSTEM UPDATE WITH ANY NEW INFORMATION
        if(!empty($customFields)){
            $contactID = $contactInfo['id'];
            $maropostapi->updateContact($contactID, $customFields, $fname, $lname);
        }
    }
}

if(!empty($product)){
    if(isset($contactInfo['id'])){
        if($listName == "LEAD" || $listName == "AC" || $listName == "CBAC"){
            $customerJourney = $functions->getJourneyIDs($product, 'CUST', $gender);
            $customerFlowID = $customerJourney['flowID'];
            //printNeat($customerJourneyID, "Customer Journey ID");
            $workFlows = array();
            foreach($contactInfo['workflows'] as $journey){
                $workFlows[] = $journey['id'];
            }
            //printNeat($workFlows);
            if(!in_array($customerFlowID, $workFlows)){
                addToJourney($email, $product, $listName, $gender, $zone);
            }
        }else{
            addToJourney($email, $product, $listName, $gender, $zone);
        }
    }else{
        addToJourney($email, $product, $listName, $gender, $zone);
        //echo "Contact not in system, add".$email." to ".$product." ".$listName;
    }
}

/*
 * CHECK IF LIST NAME IS CUSTOMER
 * AND PAUSE THEM IN ANY LEAD OR ABANDONED CART JOURNEYS
 * AND ADD THEM TO THEIR RESPECTIVE NEWSLETTER
 */
if(isset($listName) && $listName == "CUST"){
    //printNeat($contactInfo);
    if($gender == 'm'){
        $newsletterid = $functions->getListID('MNEWS');
    }else{
        $newsletterid = $functions->getListID('WNEWS');
    }

    $subscriptions = array();
    if(isset($contactInfo['list_subscriptions'])){
        foreach($contactInfo['list_subscriptions'] as $subscription){
            $subscriptions[] = $subscription['list_id'];
        }
    }
    if(!in_array($newsletterid, $subscriptions)){
        //THIS ADDS TO NEWSLETTER LIST IF SOMEONE IS GETTING ADDED A CUSTOMER JOURNEY AND IS NOT ALREADY ON THE LIST
        $addToNewsletter = $maropostapi->addContactToList($newsletterid, $email, $customFields, $fname, $lname);
    }

    //printNeat($contactInfo);
    if(isset($contactInfo['id'])){
        $contactID = $contactInfo['id'];

        //STOP JOURNEYS IF APPLICABLE
        if(!empty($product)) {
            $checkLists = array('LEAD','DIGIBLOG','AC','CBAC');

            foreach ($checkLists as $cList){
                $maropostapi->checkStopJourney($cList, $contactID, $contactInfo, $product, $gender, $zone, $fname, $lname);

                if ($product == "SS") {
                    $checkProducts = ['DHLYQ_Z1','DHLYQ_Z2','DHLYQ_Z3'];
                    foreach ($checkProducts as $cProduct){
                        $maropostapi->checkStopJourney($cList, $contactID, $contactInfo, $cProduct, $gender, $zone, $fname, $lname);
                    }
                }
            }

            if($product == 'TXB' || $product == 'TRB' || $product == 'HTK'){
                $zones = ['a','b','c'];
                foreach ($zones as $zone){
                    $maropostapi->checkStopJourney('LEAD', $contactID, $contactInfo, 'GBTQ', $gender, $zone, $fname, $lname);
                }
            }
        }
    }
}

if(isset($_REQUEST['remove']) && isset($contactInfo['id'])){
    $contactID = $contactInfo['id'];
    $info = urldecode($_REQUEST['remove']);
    //printNeat($info, "Remove Information");
    $infoArray = unserialize($info);
    //printNeat($infoArray, "Array of items to be removed");
    $removeList = $infoArray['listname'];
    $removeProduct = $infoArray['product'];
    $maropostapi->checkStopJourney($removeList, $contactID, $contactInfo, $removeProduct, $gender);
}

//THIS ADDS TO JOURNEY IF JOURNEY EXISTS
function addToJourney($email, $product, $listName, $gender="", $zone=""){
    //printNeat(func_get_args());
    global $functions;
    global $maropostapi;
    $journey = $functions->getJourneyIDs($product, $listName, $gender, $zone);
    $flowID = $journey['flowID'];
    $triggerID = $journey['triggerID'];
    if(!empty($flowID) && !empty($triggerID)){
        $maropostapi->triggerAPIevent($flowID, $triggerID, $email);
    }
}

if($testing == 'false'){
    header("Location: " . $redirect);
    exit();
}
?>
