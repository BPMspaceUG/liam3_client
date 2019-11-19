<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
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
        $email_id = $decoded->aud;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    if (isset($_POST['register'])) {
        $password = trim(htmlspecialchars($_POST['password']));
        $firstname = htmlspecialchars($_POST['firstname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $register = api(json_encode(array("cmd" => "register", "param" => array(
            "email_id" => $email_id,
            "password" => $password,
            "firstname" => $firstname,
            "lastname" => $lastname
        ))));
        $register = json_decode($register, true);
        if (isset($register['error']['msg'])) {
            $error = $register['error']['msg'];
        } else if (isset($register['showForm'])) {
            $show_form = true;
            if (isset($register['message'])) {
                $error = $register['message'];
            } else {
                $success = 'Success.';
            }
        } else {
            $success = 'Success.';
        }

        /*$result = api(json_encode(array(
                "cmd" => "create",
                "param" => array(
                    "table" => "liam3_user",
                    "row" => array(
                        "liam3_User_firstname" => htmlspecialchars($_POST['firstname']),
                        "liam3_User_lastname" => htmlspecialchars($_POST['lastname']),
                        "liam3_User_password" => $password,
                        "liam3_User_email_id" => $email_id
                    )
                )
            )
        ));
        $result = json_decode($result, true);
        if (count($result) > 1) {
            $success = 'Success.';

            $result = api(json_encode(array(
                    "cmd" => "makeTransition",
                    "param" => array(
                        "table" => "liam3_email",
                        "row" => array(
                            "liam3_email_id" => $email_id,
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
        }*/
    /*} else {
        $result = api(json_encode(array(
                "cmd" => "makeTransition",
                "param" => array(
                    "table" => "liam3_email",
                    "row" => array(
                        "liam3_email_id" => $email_id,
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
        $register = api(json_encode(array("cmd" => "register", "param" => array(
            "check_email" => true,
            "email_id" => $email_id
        ))));
        $register = json_decode($register, true);
        if (isset($register['error']['msg'])) {
            $show_form = false;
            $error = $register['error']['msg'];
        } else {
            $show_form = true;
        }
    }
}
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM3_Client_register.inc.php');