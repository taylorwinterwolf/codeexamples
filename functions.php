<?php
class functions {

    function getListID($listname){
        $lists = array(
            'DRWN' => 39359, // Digital Romance Women's Newsletter
            'DRMN' => 39358, // Digital Romance Men's Newsletter
            //DUPLICATING THE NEWSLETTER LISTS SO I CAN USE A DIFFERENT LIST NAME TO SET THE PROPER CUSTOM FIELDS
            'WNEWS' => 39359, // Digital Romance Women's Newsletter
            'MNEWS' => 39358, // Digital Romance Men's Newsletter
            'AFF'  => 44691, // Digital Romance Affiliate
        );

        return $lists[$listname];
    }

    function getProducts(){
        $products = array('DRI', 'TDS', 'TDS37', 'TDSDIGI', 'TXB', 'TRB', 'TYWIB', 'CHH', 'SS', 'DTD', 'CGC', 'OF', 'OA', 'SGG', 'MHB', 'CMC', 'H2L', 'DHLYQ_Z1', 'DHLYQ_Z2', 'DHLYQ_Z3', 'AA', 'LOD', 'LOL', 'PSY', '3MT', '3MT_FB', 'CONCOLD', 'SOG', 'HTK','MHWY');
        return $products;
    }

    function getCustomFieldName($product="", $listName){

        if(!empty($product)){
            $productLower = strtolower($product);
        }

        switch ($listName) {
            case "DRWN":
                $customFieldName = "dri_signup";
                break;
            case "DRMN":
                $customFieldName = "dri_signup";
                break;
            case "DIGIBLOG":
                $customFieldName = "digiblog";
                break;
            case "CUST":
                $customFieldName = "product_".$productLower;
                break;
            case "LEAD":
                $customFieldName = "promo_".$productLower;
                break;
            case "AC":
                $customFieldName = "ac_".$productLower;
                break;
            case "CBAC":
                $customFieldName = $productLower."_cb_abandoned_cart";
                break;
            case "AFF":
                $customFieldName = "dri_affiliate";
                break;
        }

        if(isset($customFieldName)){
            return $customFieldName;
        }else{
            return;
        }
    }

    function getJourneyIDs($product, $listName, $gender="", $zone=""){
        $key = array(
            'YCHYT' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "35285", 'triggerID' => "98912981266512"),
                'CUST' => array('flowID' => "35060", 'triggerID' => "58100503787838")
            ),
            'MHWY' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "29836", 'triggerID' => "94923617897078"),
                'CUST' => array('flowID' => "29709", 'triggerID' => "30242913707309")
            ),
            'MHWYDIG24' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "29836", 'triggerID' => "94923617897078"),
                'CUST' => array('flowID' => "30248", 'triggerID' => "19437461939425")
            ),
            'TXB' => array(
                'LEAD' => array(
                    'f' => array('flowID' => "9639", 'triggerID' => "81566026900451"),
                    'm' => array('flowID' => "9403", 'triggerID' => "57678127610329")
                ),
                'AC' => array(
                    'f' => array('flowID' => "9399", 'triggerID' => "15749439805800"),
                    'm' => array('flowID' => "9349", 'triggerID' => "76422054271328")
                ),
                'CUST' => array(
                    'f' => array('flowID' => "", 'triggerID' => ""),
                    'm' => array('flowID' => "", 'triggerID' => "")
                )
            ),
            'TRB'   => array(
                'LEAD' => array(
                    'f' => array('flowID' => "9614", 'triggerID' => "55685656253922"),
                    'm' => array('flowID' => "9649", 'triggerID' => "65933067949353")
                ),
                'AC' => array(
                    'f' => array('flowID' => "9397", 'triggerID' => "46075190072413"),
                    'm' => array('flowID' => "9402", 'triggerID' => "93033257948938")
                ),
                'CUST' => array(
                    'f' => array('flowID' => "9355", 'triggerID' => "54894861046537"),
                    'm' => array('flowID' => "9617", 'triggerID' => "28614150998970")
                )
            ),
            'LOD' => array(
                'LEAD' => array('flowID' => "9929", 'triggerID' => "12865793227272"),
                'AC'   => array('flowID' => "8545", 'triggerID' => "50548136094540"),
                'CBAC' => array('flowID' => "20756", 'triggerID' => "92691888009763"),
                'CUST' => array('flowID' => "33667", 'triggerID' => "91895846586015")
            ),
            'TDSDIGI' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
                'CUST' => array('flowID' => "31127", 'triggerID' => "42424426334248")
            ),
            'TDS37' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
                'CUST' => array('flowID' => "27940", 'triggerID' => "53848164382992")
            ),
            'LOL' => array(
                'LEAD' => array('flowID' => "9956", 'triggerID' => "76711193634432"),
                'AC'   => array('flowID' => "8566", 'triggerID' => "78021371237830"),
                'CUST'   => array('flowID' => "8645", 'triggerID' => "40022730763394")
            ),
            'HTK' => array(
                'LEAD' => array('flowID' => "16245", 'triggerID' => "33335887259820"),
                'AC'   => array('flowID' => "16243", 'triggerID' => "87793998411005"),
                'CUST' => array('flowID' => "23150", 'triggerID' => "87914849635171")
            ),
            'CHH' => array(
                'LEAD' => array('flowID' => "9957", 'triggerID' => "66439818531035"),
                'AC'   => array('flowID' => "8549", 'triggerID' => "21126732177654"),
                'CBAC' => array('flowID' => "20763", 'triggerID' => "26133954548258"),
                'CUST' => array('flowID' => "8473", 'triggerID' => "94982815320012")
            ),
            'SS' => array(
                'LEAD' => array('flowID' => "9797", 'triggerID'  => "75289784685755"),
                'CBAC' => array('flowID' => "20500", 'triggerID' => "72306262851337"),
                'AC'   => array('flowID' => "8550", 'triggerID'  => "12294955368292"),
                'CUST' => array('flowID' => "32529", 'triggerID' => "57481888552626")
            ),
            'SOG' => array(
                'LEAD' => array('flowID' => "13315", 'triggerID' => "90787769589206"),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
                'CUST'   => array('flowID' => "13436", 'triggerID' => "92764723002571")
            ),
            '3MT'   => array(
                'LEAD' => array(
                    'f' => array('flowID' => "14836", 'triggerID' => "64427298945469"),
                    'm' => array('flowID' => "14835", 'triggerID' => "63199133827368")
                )
            ),
            '3MT_FB'   => array(
                'LEAD' => array('flowID' => "16947", 'triggerID' => "18979244662508")
            ),
            'GBTQ' => array(
                'LEAD' => array(
                    'f' => array(
                        'a' => array('flowID' => "19105", 'triggerID' => "26215566123234"),
                        'b' => array('flowID' => "19112", 'triggerID' => "37276454180127"),
                        'c' => array('flowID' => "19106", 'triggerID' => "71191249308977")
                    ),
                    'm' => array(
                        'a' => array('flowID' => "19102", 'triggerID' => "99302911937506"),
                        'b' => array('flowID' => "19109", 'triggerID' => "45081507893279"),
                        'c' => array('flowID' => "19103", 'triggerID' => "50306237477347")
                    )
                ),
            ),
            'DHLYQ_Z1' => array(
                'LEAD' => array('flowID' => "33549", 'triggerID' => "96683282651986"),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
            ),
            'DHLYQ_Z2' => array(
                'LEAD' => array('flowID' => "33549", 'triggerID' => "65415212737271"),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
            ),
            'DHLYQ_Z3' => array(
                'LEAD' => array('flowID' => "33549", 'triggerID' => "21081650057324"),
                'AC'   => array('flowID' => "", 'triggerID' => ""),
            ),
            'TYWIB' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'DTD' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'CGC' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "8565", 'triggerID' => "35097637288606"),
                'CUST' => array('flowID' => "8574", 'triggerID' => "34260806943054")
            ),
            'OF' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'OA' => array(
                'LEAD' => array('flowID' => "17176", 'triggerID' => "14254528386308"),
                'AC'   => array('flowID' => "8564", 'triggerID' => "42638323518928"),
                'CUST' => array('flowID' => "8576", 'triggerID' => "4273453403747")
            ),
            'SGG' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'MHB' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'CMC' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'H2L' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'AA' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "", 'triggerID' => "")
            ),
            'PSY' => array(
                'LEAD' => array('flowID' => "", 'triggerID' => ""),
                'AC'   => array('flowID' => "8554", 'triggerID' => "38871441040410")
            ),
            'DRI' => array(
                'DRWN' => array('flowID' => "12925", 'triggerID' => "87643685289458"),
                'DRMN' => array('flowID' => "14792", 'triggerID' => "25215758170386"),
                'AFF'  => array('flowID' => "9276", 'triggerID' => "88636575169825")
            )
        );

        if (is_array($key[$product]) && array_key_exists($listName, $key[$product])) {
            if($product == 'TRB' || $product == 'TXB' || $product == '3MT') {
                return $key[$product][$listName][$gender];
            }elseif($product == 'GBTQ'){
                return $key[$product][$listName][$gender][$zone];
            }else{
                return $key[$product][$listName];
            }
        }

    }
}

$functions = new functions();
?>
