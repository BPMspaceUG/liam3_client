<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/captcha/captcha.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_translate.inc.php');
$show_form = true;

//Brute force prevention
$apc_captcha_key = "{$_SERVER['SERVER_NAME']}~captcha:{$_SERVER['REMOTE_ADDR']}";
$apc_captcha_blocked_key = "{$_SERVER['SERVER_NAME']}~captcha-blocked:{$_SERVER['REMOTE_ADDR']}";
$captcha_tries = (int)apcu_fetch($apc_captcha_key);
if ($captcha_tries >= liam3_failed_captcha_max) {
    header("HTTP/1.1 429 Too Many Requests");
    echo "You've exceeded the number of captcha attempts. We've blocked IP address {$_SERVER['REMOTE_ADDR']} for a few minutes.";
    exit();
}
$apc_login_key = "{$_SERVER['SERVER_NAME']}~login:{$_SERVER['REMOTE_ADDR']}";
$apc_login_blocked_key = "{$_SERVER['SERVER_NAME']}~login-blocked:{$_SERVER['REMOTE_ADDR']}";
$login_tries = (int)apcu_fetch($apc_login_key);
if ($login_tries >= liam3_failed_login_max) {
    header("HTTP/1.1 429 Too Many Requests");
    echo "You've exceeded the number of login attempts. We've blocked IP address {$_SERVER['REMOTE_ADDR']} for a few minutes.";
    exit();
}

if (isset($_POST['forgot_password']) || isset($_GET['email'])) {
    if (!isset($_GET['email'])) {
        if (file_exists($_POST['captcha-image'])) unlink($_POST['captcha-image']);
        $sentCode = htmlspecialchars($_POST['code']);
        $result = (int)$_POST['result'];
        $captchaResult = getExpressionResult($sentCode) === $result;
    } else {
        $captchaResult = true;
    }
    if (!$captchaResult) {
        $error = 'Wrong Captcha.';
        $captcha_blocked = (int)apcu_fetch($apc_captcha_blocked_key);
        apcu_store($apc_captcha_key, $captcha_tries+1, pow(2, $captcha_blocked+1)*60);  # store tries for 2^(x+1) minutes: 2, 4, 8, 16, ...
        apcu_store($apc_captcha_blocked_key, $captcha_blocked+1, 86400);  # store number of times blocked for 24 hours
    } else {
        apcu_delete($apc_captcha_key);
        apcu_delete($apc_captcha_blocked_key);
        $email_input = htmlspecialchars($_REQUEST['email']);
        /*$excluded_ports = array(80, 443);
        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
            $server_port = '';
        } else {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;*/
        $liam3_url = LIAM3_URL;
        $forgot_password = api(json_encode(array("cmd" => "forgotPassword", "param" => array(
            "liam3_url" => $liam3_url,
            "email" => $email_input,
        ))));
        $forgot_password = json_decode($forgot_password, true);
        if (isset($forgot_password['message'])) {
            $success = $forgot_password['message'];
            $show_form = false;
        } else {
            $error = $forgot_password['error']['msg'];
        }
    }
}
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);
require_once(__DIR__ . '/inc/templates/liam3_Client_forgot_password.inc.php');