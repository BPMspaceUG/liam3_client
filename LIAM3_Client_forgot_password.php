<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/LIAM3_Client_translate.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
$show_form = true;

if (isset($_POST['forgot_password']) || isset($_GET['email'])) {
    $email_input = htmlspecialchars($_REQUEST['email']);
    $user_email = json_decode(api(json_encode(array(
        "cmd" => "read",
        "param" => array(
            "table" => "liam3_User_email",
            "where" => "liam3_email_text = '$email_input' && a.state_id = 11"
        )
    ))), true);
    if (!$user_email) {
        $error = 'There is no registered user with this email address';
    } else {
        $result = json_decode(api(json_encode(array(
            "cmd" => "read",
            "param" => array(
                "table" => "liam3_email",
                "where" => "liam3_email_text = '$email_input' && a.state_id = 14"
            )
        ))), true);
        if (!$result) {
            $error = 'This email address is not verified';
        }
    }
    if (!isset($error)) {
        $jwt_key = AUTH_KEY;
        $jwt_token = array(
            "iss" => "liam3",
            "aud" => $user_email[0]['liam3_User_id_fk_164887']['liam3_User_id'],
            "iat" => time(),
            "exp" => time() + 10800
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($jwt_token, $jwt_key);

        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "param" => array(
                "table" => "liam3_User",
                "row" => array(
                    "liam3_User_id" => $user_email[0]['liam3_User_id_fk_164887']['liam3_User_id'],
                    "liam3_client_passwd_reset" => true,
                    "liam3_User_email" => $email_input,
                    "state_id" => 9
                )
            )
        )));
        $result = json_decode($result, true);
        if ($result > 2) {
            // Mail Content
            $subject = "Password Reset";
            $excluded_ports = array(80, 443);
            if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
                $server_port = '';
            } else {
                $server_port = ':' . $_SERVER['SERVER_PORT'];
            }
            $link = "http://" . $_SERVER['SERVER_NAME'] . $server_port . "/LIAM3_Client_reset_password.php?token=" . $jwt;
            $msg = translate('LIAM3 CLIENT password reset email', 'en');
            $msg = str_replace('$link', $link, $msg);
            // Format and Send Mail
            $msg = wordwrap($msg, 70);
            mail($email_input, $subject, $msg);
            $success = 'Password reset link is sent to this e-mail address.';
            $show_form = false;
            /*if (mail($email_input, $subject, $msg)) {
                $success = 'Password reset link sent to email.';
            } else {
                $error = "The email can't be send";
            }*/
        }
    }
}
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
require_once(__DIR__ . '/inc/templates/LIAM3_Client_forgot_password.inc.php');