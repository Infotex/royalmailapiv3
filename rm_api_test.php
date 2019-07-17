<?php
$clientid = '';
$clientsecret = '';
$securitypassword = '';
$securityusername= '';
date_default_timezone_set("Europe/London");

function rm_generate_token() {
    global $clientid;
    global $clientsecret;
    global $securitypassword;
    global $securityusername;

    $ch = curl_init();

    curl_setopt_array($ch, array(
    CURLOPT_URL => "https://api.royalmail.net/shipping/v3/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "x-ibm-client-id: " . $clientid,
        "x-ibm-client-secret: " . $clientsecret,
        "x-rmg-security-password: " . $securitypassword,
        "x-rmg-security-username: " . $securityusername
        ),
    ));

    $response = curl_exec($ch);

    curl_close($ch);
    unset($ch);

    $tokenary = json_decode($response,true);
    $token = $tokenary["token"];
    return $token;
}

function rm_check_address($token,$addressid) {
    global $clientid;
    $haserror = false;

    echo "\n" . date("d/m/Y H:i:s") . " Calling https://api.royalmail.net/shipping/v3/addresses/A" . $addressid;

    $ch = curl_init();

    curl_setopt_array($ch, array(
    CURLOPT_URL => "https://api.royalmail.net/shipping/v3/addresses/A".$addressid,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "x-ibm-client-id: " . $clientid,
        "x-rmg-auth-token: " . $token
        ),
    ));

    $response = curl_exec($ch);
    unset($response);
    $err = curl_error($ch);

    if (!empty($err)) {
        echo " - Error: " . $err;
        $haserror = true;
    } else {
        echo " - OK";
    }

    curl_close($ch);
    return $haserror;
}

if (php_sapi_name()!=='cli') {  // via apache so output pre tag
    echo '<html><body><pre>';
};

$i = 0;
while ($i<50) {	//test 50 random orders within the range
    $addressid = rand(174330,175846);
    $token = rm_generate_token();
    $addresscheck = rm_check_address($token,$addressid);
    if ($addresscheck==false) {
        $addresscheck = rm_check_address($token,$addressid);    //retry same one
    }
    $i++;
}