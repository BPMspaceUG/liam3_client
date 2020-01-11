<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
if (!isset($_GET['token'])) {
    $error = 'No token.';
} else {
    $jwt = $_GET['token'];

    /**
     * You can add a leeway to account for when there is a clock skew times between
     * the signing and verifying servers. It is recommended that this leeway should
     * not be bigger than a few minutes.
     *
     * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
     */
    JWT::$leeway = 60; // $leeway in seconds
    try {
        $decoded = JWT::decodeWithoutKey($jwt, array('HS256'));
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    $email = $decoded->aud;
    $set_email_to_verified = api(json_encode(array(
        "cmd" => "setEmailToVerified",
        "param" => array(
            "email" => $email,
        )
    )));
    try {
        $set_email_to_verified = json_decode($set_email_to_verified, true);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    if (!isset($error)) {
        if (isset($set_email_to_verified['message'])) {
            $success = $set_email_to_verified['message'];
        } else {
            $error = $set_email_to_verified['error']['msg'];
        }
    }
}
require_once(__DIR__ . '/inc/templates/LIAM3_Client_verify.inc.php');