<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
$show_form = true;
if (!isset($_GET['token'])) {
    $error = 'No token.';
    $show_form = false;
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
    if (isset($error)) {
        $show_form = false;
    } else {
        $user_id = $decoded->aud;
        $reset_password = api(json_encode(array("cmd" => "resetPassword", "param" => array(
            "user_id" => $user_id,
        ))));
        $reset_password = json_decode($reset_password, true);
        if (isset($reset_password['error']['msg'])) {
            $error = $reset_password['error']['msg'];
            $show_form = false;
            $show_login_button = true;
        }
        if (isset($_POST['liam3_reset_password'])) {
            $reset_password = api(json_encode(array("cmd" => "resetPassword", "param" => array(
                "user_id" => $user_id,
                "password_new" => htmlspecialchars($_POST['liam3_User_password_new']),
                "password_new_confirm" => htmlspecialchars($_POST['liam3_User_password_new_confirm'])
            ))));
            $reset_password = json_decode($reset_password, true);
            if (isset($reset_password['error']['msg'])) {
                $error = $reset_password['error']['msg'];
            } else {
                $success = $reset_password['message'];
                $show_form = false;
                $show_login_button = true;
            }
        }
    }
}
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM3_Client_reset_password.inc.php');