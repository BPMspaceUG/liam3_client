<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
if (!isset($_SESSION['token'])) {
    if (!isset($_GET['origin'])) {
        header("Location: LIAM3_Client_login.php");
        exit();
    } else {
        header("Location: LIAM3_Client_login.php?origin=" . $_GET['origin']);
        exit();
    }
} else {
    $token = $_SESSION['token'];
    /*$tks = explode('.', $jwt);
    list($headb64, $bodyb64, $cryptob64) = $tks;
    $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));*/

    /**
     * You can add a leeway to account for when there is a clock skew times between
     * the signing and verifying servers. It is recommended that this leeway should
     * not be bigger than a few minutes.
     *
     * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
     */
    JWT::$leeway = 60; // $leeway in seconds
    try {
        $decoded = JWT::decodeWithoutKey($token, array('HS256'));
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    $user_id = $decoded->uid;
    $user = json_decode(api(json_encode(array(
        "cmd" => "read",
        "param" => array(
            "table" => "liam3_user",
            "filter" => '{"=":["liam3_User_id", '.$user_id.']}'
        ))
    )), true);
    $username = $user["records"][0]['liam3_User_firstname'] . ' ' . $user["records"][0]['liam3_User_lastname'];
    require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM3_Client_main.inc.php');
}
