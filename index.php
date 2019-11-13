<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
if (!isset($_SESSION['token'])) {
    if (!isset($_GET['origin'])) {
        header("Location: LIAM2_Client_login.php");
        exit();
    } else {
        header("Location: LIAM2_Client_login.php?origin=" . $_GET['origin']);
        exit();
    }
} else {
    /*
    $user = json_decode(api(json_encode(array(
        "cmd" => "read",
        "param" => array(
            "table" => "liam3_user",
            "filter" => '{"=":["liam3_User_id", '.$_SESSION['token'].']}'
        ))
    )), true);
    */
    //$user = JWT::decode()

    $jwt = $_SESSION['token'];
    $tks = explode('.', $jwt);
    list($headb64, $bodyb64, $cryptob64) = $tks;
    $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
    $token = $_SESSION['token'];
    $user = json_decode(api(json_encode(array(
        "cmd" => "read",
        "param" => array(
            "table" => "liam3_user",
            "filter" => '{"=":["liam3_User_id", '.$payload->uid.']}'
        ))
    )), true);


    $username = $user["records"][0]['liam3_User_firstname'] . ' ' . $user["records"][0]['liam3_User_lastname'];
   
    require_once(__DIR__ . '/inc/LIAM2_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM2_Client_main.inc.php');
}
