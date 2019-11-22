<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
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
    $result = api(json_encode(array(
        "cmd" => "getEmailId",
        "param" => array(
            "email" => $email,
        )
    )));
    $result = json_decode($result, true);
    if (isset($result["error"])) {
        $error = 'Email not found.';
    } else {
        $email_id = $result['email_id'];
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "param" => array(
                "table" => "liam3_email",
                "row" => array(
                    "liam3_email_id" => $email_id,
                    "state_id" => EMAIL_STATE_VERIFIED
                )
            )
        )));
        try {
            $result = json_decode($result, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            if ($result && count($result) > 2) {
                $success = 'Success.';
            } else {
                $error = 'This email is already verified or blocked.';
            }
        }
    }
}
require_once(__DIR__ . '/inc/templates/LIAM3_Client_verify.inc.php');