<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
$show_form = false;
if (!isset($_GET['token'])) {
    $error = 'No token.';
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
        $email_id = $decoded->aud;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    if (isset($_POST['register'])) {
        $password = trim(htmlspecialchars($_POST['password']));
        $result = api(json_encode(array(
                "cmd" => "create",
                "paramJS" => array(
                    "table" => "liam2_User",
                    "row" => array(
                        "liam2_User_firstname" => htmlspecialchars($_POST['firstname']),
                        "liam2_User_lastname" => htmlspecialchars($_POST['lastname']),
                        "liam2_User_password" => $password,
                        "liam2_User_email_id" => $email_id
                    )
                )
            )
        ));
        $result = json_decode($result, true);
        if (count($result) > 1) {
            $success = 'Success.';

            $result = api(json_encode(array(
                    "cmd" => "makeTransition",
                    "paramJS" => array(
                        "table" => "liam2_email",
                        "row" => array(
                            "liam2_email_id" => $email_id,
                            "state_id" => 14
                        )
                    )
                )
            ));
            try {
                $result = json_decode($result, true);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if (!isset($error)) {
                if ($result && count($result) > 2) {
                    $show_form = true;
                    if (isset($_GET['origin']) && $_GET['origin']) {
                        header('Location: http:' . $_GET['origin']);
                        exit();
                    }
                } else {
                    $error = 'This email is already verified or blocked.';
                }
            }

        } else {
            $error = $result[0]['message'];
            $show_form = true;
        }
    /*} else {
        $result = api(json_encode(array(
                "cmd" => "makeTransition",
                "paramJS" => array(
                    "table" => "liam2_email",
                    "row" => array(
                        "liam2_email_id" => $email_id,
                        "state_id" => 14
                    )
                )
            )
        ));
        try {
            $result = json_decode($result, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            if ($result && count($result) > 2) {
                $show_form = true;
            } else {
                $error = 'This email is already verified or blocked.';
            }
        }*/
    }
    if (isset($_GET['firstname']) || isset($_GET['lastname'])) $show_form = true;
    if (!isset($_POST['register'])) {
        $check_email = json_decode(api(json_encode(array("cmd" => "read", "paramJS" => array("table" => "liam2_email",
            "where" => "liam2_email_id = $email_id && a.state_id != 13")))), true);
        if ($check_email) {
            $show_form = false;
            $error = 'This email is already verified or blocked.';
        }
    }
}
require_once(__DIR__ . '/inc/LIAM2_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM2_Client_register.inc.php');