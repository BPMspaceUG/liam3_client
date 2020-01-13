<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/captcha/captcha.inc.php');
if (isset($_POST['self_register']) || isset($_GET['origin']) || isset($_GET['email_id'])) {
    if (!isset($_GET['origin']) && !isset($_GET['email_id'])) {
        if (file_exists($_POST['captcha-image'])) unlink($_POST['captcha-image']);
        $sentCode = htmlspecialchars($_POST['code']);
        $result = (int)$_POST['result'];
        $captchaResult = getExpressionResult($sentCode) === $result;
    } else {
        $captchaResult = true;
    }
    if (!$captchaResult) {
        $error = 'Wrong Captcha.';
    } else {
        $email = htmlspecialchars($_REQUEST['email']);
        /*$excluded_ports = array(80, 443);
        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
            $server_port = '';
        } else {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;*/
        $liam3_url = LIAM3_URL;
        $origin = isset($_GET['origin']) ? htmlspecialchars($_GET['origin']) : '';
        $firstname = isset($_GET['firstname']) ? htmlspecialchars($_GET['firstname']) : '';
        $lastname = isset($_GET['lastname']) ? htmlspecialchars($_GET['lastname']) : '';
        $email_id = isset($_GET['email_id']) ? htmlspecialchars($_GET['email_id']) : '';
        $selfRegister = api(json_encode(array("cmd" => "selfRegister", "param" => array(
            "liam3_url" => $liam3_url,
            "email" => $email,
            "origin" => $origin,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "email_id" => $email_id
        ))));
        $selfRegister = json_decode($selfRegister, true);
        if (isset($selfRegister['message'])) {
            $success = $selfRegister['message'];
            $email_id = $selfRegister['email_id'];
        } else {
            $error = $selfRegister['error']['msg'];
        }
    }
}
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);
require_once(__DIR__ . '/inc/templates/liam3_Client_self_register.inc.php');
