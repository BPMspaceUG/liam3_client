<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
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

        $excluded_ports = array(80, 443);
        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
            $server_port = '';
        } else {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;

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
        } else {
            $error = $selfRegister['error']['msg'];
        }

        /*if ($email_id) {
            $result = [1,2];
        } else {
            $result = api(json_encode(array(
                    "cmd" => "create",
                    "param" => array(
                        "table" => "liam3_email",
                        "row" => array(
                            "liam3_email_text" => $email
                        )
                    )
                )
            ));
            $result = json_decode($result, true);
        }
        if (count($result) > 1) {
            if ($email_id) {
                $check_email = json_decode(api(json_encode(array("cmd" => "read", "param" => array("table" => "liam3_email",
                    "where" => "liam3_email_id = $email_id && a.state_id = 13")))), true);
                if (!$check_email) $error = 'This email is already verified or blocked.';
            }
            if (!isset($error)) {
                if (!$email_id) $email_id = $result[1]['element_id'];
                $jwt_key = AUTH_KEY;
                $jwt_token = array(
                    "iss" => "liam3",
                    "aud" => $email_id,
                    "iat" => time(),
                    "exp" => time() + 10800
                );

                $jwt = JWT::encode($jwt_token, $jwt_key);

                // Mail Content
                $subject = "Please confirm your Mail Adress";
                $user_info = '&firstname=' . $firstname . '&lastname=' . $lastname;
                $excluded_ports = array(80, 443);
                if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
                    $server_port = '';
                } else {
                    $server_port = ':' . $_SERVER['SERVER_PORT'];
                }
                $link = "http://" . $_SERVER['SERVER_NAME'] . $server_port . "/LIAM3_Client_register.php?token=" . $jwt . "&origin=" . $origin . $user_info;
                $msg = translate('LIAM3 CLIENT Self registration email', 'en');
                $msg = str_replace('$link', $link, $msg);
                // Format and Send Mail
                $msg = wordwrap($msg, 70);
                //if (mail($email, $subject, $msg)) {
                //    $success = 'A verification link has been sent to your email address.';
                //} else {
                //    $error = "The email can't be send";
                //}
                mail($email, $subject, $msg);
                $success = 'A verification link has been sent to your email address.';
            }
        } else {
            $error = $result[0]['message'];
        }*/
    }
}
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM3_Client_self_register.inc.php');
