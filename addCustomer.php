<?php
//THIS PAGES TAKES IN CUSTOMER AND PRODUCT DATA AFTER A PUCHASE ON CLICKBANK(Shopping Cart)
//THIS SENDS DATA TO THE RELEVANT APIs AND STORES THE DATA IN A DATABASE

echo "This is not the page you are looking for...";

require('ipnFunctions.php');
require('decrypt.php');
$ipnDecrypt = new decrypt();

$testing = 'false';

if($testing == 'false'){
    $orderObject = $ipnDecrypt->ipn();

    $order = objectToArray($orderObject);
}else{
    //A JSON TEST ORDER
    $order = json_decode('{"transactionTime":"2020-04-28T12:14:50-07:00","receipt":"KVXJ6AVR","transactionType":"SALE","vendor":"makehimw","affiliate":"annakovach","role":"VENDOR","totalAccountAmount":0,"paymentMethod":"MSTR","totalOrderAmount":0,"totalTaxAmount":0,"totalShippingAmount":0,"currency":"USD","orderLanguage":"EN","lineItems":[{"itemNo":"mhwy-tds37","productTitle":"Total Devotion System 2.0","shippable":false,"recurring":true,"accountAmount":0,"quantity":1,"paymentPlan":{"rebillStatus":"ACTIVE","rebillFrequency":"MONTHLY","rebillAmount":37,"paymentsProcessed":1,"paymentsRemaining":998,"nextPaymentDate":"2020-05-28T12:14:49-07:00"},"downloadUrl":"https:\/\/access.totaldevotionsystem.com\/dap\/dap-CB-autologin.php?dapprodid=3&corderamount=37.00&cprodid=ss-tds-37&redirect=https%3A\/\/access.totaldevotionsystem.com\/total-devotion-system\/&item=mhwy-tds37&cbreceipt=KVXJ6AVR&time=1588101288&cbpop=3BD2096D&cbaffi=ANNAKOVACH&cupsellreceipt=M9XJ6ANM&cname=Elizabeth+Galan&cemail=Lzgorg29%40gmail.com&ccountry=US&czip=78415&cbitems=mhwy-tds37&cbur=a&cbrblaccpt=true&_ga=2.55081707.1971169062.1588097671-987687737.1587880010","lineItemType":"UPSELL"}],"customer":{"shipping":{"firstName":"Elizabeth","lastName":"Galan","fullName":"Elizabeth Galan","email":"Lzgorg29@gmail.com","address":{"city":"CORPUS CHRISTI","county":"NUECES","state":"TX","postalCode":"78415","country":"US"}},"billing":{"firstName":"Elizabeth","lastName":"Galan","fullName":"Elizabeth Galan","email":"Lzgorg29@gmail.com","address":{"state":"TX","postalCode":"78415","country":"US"}}},"upsell":{"upsellOriginalReceipt":"M9XJ6ANM","upsellFlowId":40689,"upsellSession":"M9XJ6AMLKT","upsellPath":"daa"},"version":6,"attemptCount":1,"vendorVariables":{"dapprodid":"3","corderamount":"37.00","cprodid":"ss-tds-37","redirect":"https%3A\/\/access.totaldevotionsystem.com\/total-devotion-system\/","cupsellreceipt":"M9XJ6ANM","cbitems":"mhwy-tds37","cbur":"a","cbrblaccpt":"true","_ga":"2.55081707.1971169062.1588097671-987687737.1587880010"}}', true);
    //printNeat($order);
}

if(is_array($order)){

    //printNeat($order);

    $data = json_encode($order);
    //$data = print_r($order, true);
    $date = date('m-d-y h:i:s e');
    $file_data = "Date: ".$date." - ".$data. "\n";
    $file_data .= file_get_contents('orders.txt');
    file_put_contents('orders.txt', $file_data);

    //printNeat($order);

    $customerInfo = $order['customer']['shipping'];
    $customerAddress = $order['customer']['shipping']['address'];

    $date = date('Y-m-d H:i:s');
    $epochTime = time();
    $email = isset($customerInfo['email']) ? $customerInfo['email'] : "";
    $fName = isset($customerInfo['firstName']) ? $customerInfo['firstName'] : "";
    $lName = isset($customerInfo['lastName']) ? $customerInfo['lastName'] : "";
    $country = isset($customerAddress['country']) ? $customerAddress['country'] : "";
    $state = isset($customerAddress['state']) ? $customerAddress['state'] : "";
    $city = isset($customerAddress['city']) ? $customerAddress['city'] : "";
    $zip = isset($customerAddress['postalCode']) ? $customerAddress['postalCode'] : "";
    $affiliate = isset($order['affiliate']) ? $order['affiliate'] : "";
    $orderAmount = isset($order['totalOrderAmount']) ? $order['totalOrderAmount'] : "";
    $receipt = isset($order['receipt']) ? $order['receipt'] : "";
    $vendor = isset($order['vendor']) ? $order['vendor'] : "";
    $type = isset($order['transactionType']) ? $order['transactionType'] : "";
    $transactionTime = isset($order['transactionTime']) ? $order['transactionTime'] : "";

    $productTitle = "";
    $productID = "";

    if(isset($order['lineItems']) && $type != 'ABANDONED_ORDER'){
        foreach($order['lineItems'] as $key => $item){
            if($key == 0){
                $productTitle = $item['productTitle'];
                $productID = $item['itemNo'];
            }else{
                $productTitle .= " + ".$item['productTitle'];
                $productID .= " + ".$item['itemNo'];
            }
        }
    }

    $gender = getGender($vendor, $productID);

    //PREVENT DUPLICATES
    if($testing == 'false'){
        require('database.php');
        $database = new MySQLDatabase();

        $checkQuery = "SELECT * FROM `customers` WHERE `email`='$email' AND `product_id`='$productID' AND `order_amount`='$orderAmount' AND `transaction_time`='$transactionTime'";
        $checkEntry = $database->selectQuery($checkQuery);

        if($checkEntry == 'noData'){
            $query = "
            INSERT INTO `customers` 
            (`id`, `date`, `epoch_time`, `email`, `fname`, `lname`, `gender`, `product_title`, `product_id`, `country`, `state`, `city`, `zip`, `affiliate`, `order_amount`, `receipt`, `vendor`, `transaction_type`, `transaction_time`)
            VALUES 
            ('', '$date', '$epochTime', '$email', '$fName', '$lName', '$gender', '$productTitle', '$productID', '$country', '$state', '$city', '$zip', '$affiliate', '$orderAmount', '$receipt', '$vendor', '$type', '$transactionTime')
        ";

            $database->runQuery($query);

        }
    }

    if($type == 'ABANDONED_ORDER' && !empty($email)){

        $products = array('capturehim' => 'CHH', 'whyhelies' => 'SS', 'lodesire' => 'LOD', 'howtokiss' => 'HTK', 'oaformula' => 'OA');

        $productShortName = $products[$vendor];

        //SERIALIZE AND URL ENCRYPT REMOVAL INFO
        $info = ['listname' => 'AC', 'product' => $productShortName];
        $serialized = serialize($info);
        $urlEncode = urlencode($serialized);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://THEURLHERE/subscribe.php?listname=AC&email=".$email."&gender=".$gender."&product=".$productShortName);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($order));
        $data = curl_exec($ch);
        curl_close($ch);
        
        //STORE DATA FOR PERSONAL REVIEW
        $data = json_encode($order);        
        $date = date('m-d-y h:i:s e');
        $file_data = "Date: ".$date." - ".$data. "\n";
        $file_data .= file_get_contents('orders.txt');
        file_put_contents('orders.txt', $file_data);*/
    }
}
?>
