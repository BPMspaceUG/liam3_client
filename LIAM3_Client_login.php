<?php
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/captcha/captcha.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
use \Firebase\JWT\JWT;
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);
if (isset($_POST['liam3_login'])) {
    if (file_exists($_POST['captcha-image'])) unlink($_POST['captcha-image']);
    if (!$_POST['email'] || !$_POST['password']) {
        $error = 'Please fill all the fields.';
    } else {
        $email_input = htmlspecialchars($_POST['email']);
        if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
            $error = "Email address '$email_input' is not valid.\n";
        } else {
            $password_input = $_POST['password'];
            $sentCode = htmlspecialchars($_POST['code']);
            $captcha_result = (int)$_POST['result'];
            if (getExpressionResult($sentCode) !== $captcha_result) {
                $error = 'Wrong Captcha.';
                $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                $result = api(json_encode(array(
                    "cmd" => "create",
                    "param" => array(
                        "table" => "liam3_loginattempts",
                        "row" => array(
                            "liam3_loginattempts_time" => date('Y-m-d H:i'),
                            "liam3_loginattempts_info" => $login_attempt_info
                        )
                    )
                )));
            } else {
                $error = false;
                $login = json_decode(api(json_encode(array("cmd" => "login", "param" => array("email" => $email_input, "password" => $password_input)))), true);
                if (isset($login['login_valid']) && $login['login_valid']) {
                    $token = $login['token'];
                    if (isset($_GET['origin'])) {
                        $origin = $_GET['origin'];
                        header("Location: " . $origin . "?token=" . $token);
                        exit();
                    } else {
                        $_SESSION['token'] = $token;
                    }
                } else {
                    $error = $login['error']['msg'];
                }
            }
        }
    }
}
if (isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
} else {
    require_once(__DIR__ . '/inc/templates/LIAM3_Client_login.inc.php');
}