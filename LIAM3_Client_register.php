<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
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
        $email = $decoded->aud;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
    if (isset($_POST['register'])) {
        $password = trim(htmlspecialchars($_POST['password']));
        $firstname = htmlspecialchars($_POST['firstname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $register = api(json_encode(array("cmd" => "register", "param" => array(
            "email" => $email,
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
    }
    if (isset($_GET['firstname']) || isset($_GET['lastname'])) $show_form = true;
    if (!isset($_POST['register'])) {
        $register = api(json_encode(array("cmd" => "register", "param" => array(
            "check_email" => true,
            "email" => $email
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
require_once(__DIR__ . '/inc/templates/LIAM3_Client_register.inc.php');