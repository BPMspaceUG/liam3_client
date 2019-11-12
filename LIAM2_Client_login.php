<?php
require_once(__DIR__ . '/inc/liam2_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/captcha/captcha.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
require_once(__DIR__ . '/inc/liam2_Client_header.inc.php');
use \Firebase\JWT\JWT;
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);
if (isset($_POST['liam2_login'])) {
    if (file_exists($_POST['captcha-image'])) unlink($_POST['captcha-image']);
    if (!$_POST['email'] || !$_POST['password']) {
        $error = 'Please fill all the fields.';
    } else {
        $email_input = htmlspecialchars($_POST['email']);
        $password_input = $_POST['password'];
        $sentCode = htmlspecialchars($_POST['code']);
        $captcha_result = (int)$_POST['result'];
        if (getExpressionResult($sentCode) !== $captcha_result) {
            $error = 'Wrong Captcha.';
            $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
            $result = api(json_encode(array(
                    "cmd" => "create",
                    "param" => array(
                        "table" => "liam3_LoginAttempts",
                        "row" => array(
                            "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                            "liam3_LoginAttempts_info" => $login_attempt_info
                        )
                    )
                )
            ));
        } else {
            $error = false;
            $email = json_decode(api(json_encode(array("cmd" => "read", "param" => array("table" => "liam3_email",
                "filter" => '{"=":["liam3_email_text","'.$email_input.'"]}')))), true);
                $email = $email['records'];
            if (!$email) {
                $error = 'E-mail address is not recognized by the system, please enter the correct e-mail address or register.';
                $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                $result = api(json_encode(array(
                        "cmd" => "create",
                        "paramparam" => array(
                            "table" => "liam3_LoginAttempts",
                            "row" => array(
                                "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                "liam3_LoginAttempts_info" => $login_attempt_info
                            )
                        )
                    )
                ));
            } elseif ($email[0]['state_id'] == EMAIL_STATE_VERIFIED) {
                $email_id = $email[0]['liam3_email_id'];
            } else {
                $error = 'Email is not verified';
                $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                $result = api(json_encode(array(
                        "cmd" => "create",
                        "param" => array(
                            "table" => "liam3_LoginAttempts",
                            "row" => array(
                                "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                "liam3_LoginAttempts_info" => $login_attempt_info
                            )
                        )
                    )
                ));
            }
            if (!$error) {
                $user_email = json_decode(api(json_encode(array("cmd" => "read", "param" => array("table" => "liam3_user_email",
                    "filter" => '{"=":["liam3_email_id",'.$email_id.']}')))), true);
                    $user_email = $user_email['records'];
                if ($user_email && ($user_email[0]['state_id'] == USER_EMAIL_STATE_USE)) {
                    $user_id = $user_email[0]['liam3_User_id_fk_164887']['liam3_User_id'];
                } else {
                    $error = 'This email is unselected.';
                    $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                    $result = api(json_encode(array(
                            "cmd" => "create",
                            "param" => array(
                                "table" => "liam3_LoginAttempts",
                                "row" => array(
                                    "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                    "liam3_LoginAttempts_info" => $login_attempt_info
                                )
                            )
                        )
                    ));
                }
            }
            if (!$error) {
                $user = json_decode(api(json_encode(array("cmd" => "read", "param" => array("table" => "liam3_user",
                    "filter" => '{"=":["liam3_User_id",'.$user_id.']}')))), true);
                $user = $user['records'];
                if (!$user) {
                    $error = 'This email is not linked to any user.';
                    $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                    $result = api(json_encode(array(
                            "cmd" => "create",
                            "param" => array(
                                "table" => "liam3_LoginAttempts",
                                "row" => array(
                                    "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                    "liam3_LoginAttempts_info" => $login_attempt_info
                                )
                            )
                        )
                    ));
                }
                if (!$error && ($user[0]['state_id'] != USER_STATE_COMPLETE)) {
                    $error = 'The state of this user is not complete';
                    $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                    $result = api(json_encode(array(
                            "cmd" => "create",
                            "param" => array(
                                "table" => "liam3_LoginAttempts",
                                "row" => array(
                                    "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                    "liam3_LoginAttempts_info" => $login_attempt_info
                                )
                            )
                        )
                    ));
                }
            }
            if (!$error) {
                $salt = $user[0]['liam3_User_salt'];
                $hashedPassword = hash('sha512', $password_input . $salt);
                if ($hashedPassword != $user[0]['liam3_User_password']) {
                    $error = 'Wrong password.';
                    $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                    $result = api(json_encode(array(
                            "cmd" => "create",
                            "param" => array(
                                "table" => "liam3_LoginAttempts",
                                "row" => array(
                                    "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                    "liam3_LoginAttempts_info" => $login_attempt_info
                                )
                            )
                        )
                    ));
                } else {
                    //$_SESSION['user_id'] = $user_id;
                    $login_attempt_info = 'Successful - ' . $email_input;
                    $result = api(json_encode(array(
                            "cmd" => "create",
                            "param" => array(
                                "table" => "liam3_LoginAttempts",
                                "row" => array(
                                    "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                                    "liam3_LoginAttempts_info" => $login_attempt_info
                                )
                            )
                        )
                    ));

                    if (isset($_GET['origin'])) {
                        $origin = $_GET['origin'];
                        $jwt_token = array(
                            "iss" => "liam3",
                            "uid" => $user_id,
                            "iat" => time(),
                            "exp" => time() + 86400
                        );
                        $token = JWT::encode($jwt_token, AUTH_KEY);
                        header("Location: " . $origin . "?token=" . $token);
                        exit();
                    } else {
                        $_SESSION['user_id'] = $user_id;
                    }
                }
            }
        }
    }
}
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
} else {
    require_once(__DIR__ . '/inc/templates/liam2_Client_login.inc.php');
}