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
    $jwt_key = AUTH_KEY;

    /**
     * You can add a leeway to account for when there is a clock skew times between
     * the signing and verifying servers. It is recommended that this leeway should
     * not be bigger than a few minutes.
     *
     * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
     */
    JWT::$leeway = 60; // $leeway in seconds
    try {
        $decoded = JWT::decode($jwt, $jwt_key, array('HS256'));
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    if (isset($error)) {
        $show_form = false;
    } else {
        $user_id = $decoded->aud;
        $result = json_decode(api(json_encode(array(
            "cmd" => "read",
            "param" => array(
                "table" => "liam3_User",
                "where" => "liam3_User_id = $user_id && a.state_id = 8"
            )
        ))), true);
        if ($result) {
            $error = "This link was already used. If you need to reset your passwort again, please click on the button \"Forgot passwort\" in the LogIn form.";
            $show_form = false;
            $show_login_button = true;
        }
        if (isset($_POST['liam3_reset_password'])) {
            $result = api(json_encode(array(
                "cmd" => "makeTransition",
                "param" => array(
                    "table" => "liam3_User",
                    "row" => array(
                        "liam3_User_id" => $user_id,
                        "liam3_User_password_new" => htmlspecialchars($_POST['liam3_User_password_new']),
                        "liam3_User_password_new_confirm" => htmlspecialchars($_POST['liam3_User_password_new_confirm']),
                        "liam3_client_passwd_reset_form" => true,
                        "state_id" => 8
                    )
                )
            )));
            $result = json_decode($result, true);
            if (count($result) > 2) {
                $success = $result[0]['message'];
                $show_form = false;
                $show_login_button = true;
            } else {
                $error = $result[0]['message'];
            }
        }
    }
}
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM3_Client_reset_password.inc.php');