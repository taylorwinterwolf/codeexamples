<?php
//THIS IS THE CLASS THAT EXICUTES ALL RELEVANT MAROPOST API REQUESTS AND SUPPORTS subscribe.php
class maropost {

    function checkForRemoval($listName, $product, $contactInfo){
        global $functions;

        $listID = $functions->getListID($listName);

        $contactID = $contactInfo['id'];

        //Remove from lead or abandoned cart list if applicable
        if(is_array($contactInfo['list_subscriptions'])){
            foreach($contactInfo['list_subscriptions'] as $listInfo){
                //printNeat($listInfo, "listInfo");
                if($listInfo['list_id'] == $listID){
                    $this->deleteContactFromList($contactID, $listID);
                    break;
                }
            }
        }
    }

    function checkStopJourney($listName, $contactID, $contactInfo, $product, $gender="", $zone="", $fname, $lname){
        global $functions;

        //Remove Custom Field if Abandoned Cart
        if($listName == 'AC'){
            $customFieldName = $functions->getCustomFieldName($product, $listName);
            if(isset($contactInfo[$customFieldName]) && $contactInfo[$customFieldName] == 1){
                $customFields[$customFieldName] = 0;
                $this->updateContact($contactID, $customFields, $fname, $lname);
            }
        }

        $Journey = $functions->getJourneyIDs($product, $listName, $gender, $zone);
        $flowID = $Journey['flowID'];
        if(is_array($contactInfo['workflows'])){
            foreach($contactInfo['workflows'] as $journey){
                if(in_array($flowID, $journey)){
                    //echo"pauseJourney($flowID, $contactID)";
                    $this->pauseJourney($flowID, $contactID, $fname, $lname);
                }
            }
        }
    }

    function getContactInfo($email){
        $info = $this->request('GET',"contacts/email", "getContactInfo()", array('email' => $email));

        return $info;
    }

    function getCustomFields(){
        $maroCustomFields = $this->request('GET',"custom_fields","getCustomFields()");

        return $maroCustomFields;
    }

    function getContactLists($email){
        $contactInfo = $this->getContactInfo($email);

        $contactLists = $contactInfo['list_subscriptions'];

        return $contactLists;
    }

    function getContactID($email){
        $contactInfo = $this->getContactInfo($email);

        if(isset($contactInfo['id'])){
            $contactID = $contactInfo['id'];
        }else{
            $contactID = "";
        }

        return $contactID;
    }

    //Removes from specific list
    function deleteContactFromList($contactID, $listID){
        $this->request("DELETE","lists/".$listID."/contacts/".$contactID, "deleteContactFromList()");

        return "$contactID deleted from $listID";
    }

    //Removes from all lists
    function deleteContact($email){
        $this->request("DELETE","contacts/delete_all", "deleteContact()", array('email' => $email));

        return "$email deleted from all lists";
    }

    function getJourneys(){
        $journeys = $this->request('GET','journeys', "getJourneys()");

        return $journeys;
    }

    function addToJourney($flowID, $contactID){
        $resuts = $this->request("PUT","journeys/$flowID/start/$contactID", "addToJourney()");

        return $resuts;
    }

    function killEmail($email){
        $info = $this->request("POST", "global_unsubscribes", "killEmail()", array('global_unsubscribe' => array(
            'email'   => $email
        )));
        return $info;
    }

    function pauseJourney($flowID, $contactID){
        $resuts = $this->request("PUT","journeys/$flowID/stop/$contactID", "pauseJourney()");

        return $resuts;
    }

    function triggerAPIevent($flowID, $triggerID, $email){
        $resuts = $this->request("POST","journeys/$flowID/trigger/$triggerID", "triggerAPIevent()", array('email' => $email));

        return $resuts;
    }

    function updateContact($contactID, $customFields, $fname, $lname){
        $info = $this->request("PUT", "contacts/".$contactID, "updateContact()", array('contact' => array(
            'first_name'    => $fname,
            'last_name'     => $lname,
            'custom_field'  => $customFields,
        )));

        return $info;
    }

    function addContactToList($listid, $email, $customFields, $fname="", $lname=""){
        $addContact = $this->request('POST', "lists/".$listid."/contacts", "addContactToList()", array('contact' => array(
            'email' 	    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'custom_field'  => $customFields,
            'subscribe'     => 'true'
        )));

        return $addContact;
    }

    function addContact($email, $customFields, $fname="", $lname=""){
        $addContact = $this->request('POST', "contacts", "addContact()", array('contact' => array(
            'email' 	    => $email,
            'first_name'    => $fname,
            'last_name'     => $lname,
            'custom_field'  => $customFields,
            'subscribe'     => 'true'
        )));

        return $addContact;
    }

    function request($action, $endpoint, $requestName, $dataArray=array()) {

        global $testing;

        $url = "https://api.maropost.com/accounts/ACCOUNT#HERE/" . $endpoint . ".json";
        $ch = curl_init();

        $dataArray['auth_token'] = "TOKENHERE";
        $json = json_encode($dataArray);

        if($testing == 'true') {
            echo "<div class='request-wrap'>
                    <span class='request-info'>Request Info</span></br></br>
                    <span class='key'>Request Name:</span> 
                    <span class='value'>$requestName</span>
                    <span class='key'>Action:</span> <span class='value'>$action</span>
                    <span class='key'>URL:</span> <span class='value'>$url</span>
                    <span class='key'>JSON:</span> <span class='value'>$json</span>
                 ";
            printNeat($dataArray, "Data Array");
            echo "</div>";
        }

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_URL, $url);

        switch($action){
            case "POST":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "GET":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                break;
            default:
                break;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json','Accept: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $output = curl_exec($ch);
        $output = json_decode($output,TRUE);
        return $output;

        if($testing == 'true'){
            printNeat($output, "API Return");
        }
    }

}

$maropostapi = new maropost();
?>
