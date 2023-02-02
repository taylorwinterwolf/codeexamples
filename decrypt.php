<?php
//THIS DECRYPTS ENCODED DATA THAT IS SENT FROM CLICKBANK, THIS FILE SUPPORTS addCustomer.php
class decrypt {

    public function ipn(){
        global $testing;

        //Decode IPN
        $secretKey = "SECRETKEYGOESHERE"; // secret key from your ClickBank account

        $message = json_decode(file_get_contents('php://input'));

        $encrypted = $message->{'notification'};
        $iv = $message->{'iv'};
        error_log("IV: $iv");

        $decrypted = trim(
            openssl_decrypt(base64_decode($encrypted),
                'AES-256-CBC',
                substr(sha1($secretKey), 0, 32),
                OPENSSL_RAW_DATA,
                base64_decode($iv)), "\0..\32");

        error_log("Decrypted: $decrypted");
      
        $sanitizedData = utf8_encode(stripslashes($decrypted));
        $order = json_decode($decrypted);

        return $order;
    }

}
?>
