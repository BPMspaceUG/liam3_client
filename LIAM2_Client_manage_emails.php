<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/LIAM2_Client_translate.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
if (!isset($_SESSION['user_id']) && !isset($_GET['liam2_add_another_email'])) {
    header("Location: LIAM2_Client_login.php");
    exit();
} else {
    if (isset($_REQUEST['liam2_add_another_email'])) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_GET['user_id'];
        $email = htmlspecialchars($_REQUEST['liam2_add_another_email']);
        $result = api(json_encode(array(
                "cmd" => "create",
                "paramJS" => array(
                    "table" => "liam2_email",
                    "row" => array(
                        "liam2_email_text" => $email,
                        "only_verify_mail" => true
                    )
                )
            )
        ));
        $result = json_decode($result, true);
        if (count($result) > 1) {
            $email_id = $result[1]['element_id'];
            $jwt_key = AUTH_KEY;
            $jwt_token = array(
                "iss" => "liam2",
                "aud" => $email_id,
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
            $subject = "Verification";
            $excluded_ports = array(80, 443);
            if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
                $server_port = '';
            } else {
                $server_port = ':' . $_SERVER['SERVER_PORT'];
            }
            $link = "http://" . $_SERVER['SERVER_NAME'] . $server_port . "/LIAM2_Client_verify.php?token=" . $jwt;
            $msg = translate('LIAM2 CLIENT verify email', 'en');
            $msg = str_replace('$link', $link, $msg);
            // Format and Send Mail
            $msg = wordwrap($msg, 70);
            /*if (mail($email, $subject, $msg)) {
                $success = 'A verification link has been sent to your email address.';
            } else {
                $error = "The email can't be send";
            }*/
            mail($email, $subject, $msg);
            $success = 'A verification link has been sent to your email address.';
        } else {
            $error = $result[0]['message'];
        }
        if (isset($success)) {
            $email_id = $result[1]["element_id"];
            $result = api(json_encode(array(
                "cmd" => "create",
                "paramJS" => array(
                    "table" => "liam2_User_email",
                    "row" => [
                        "liam2_User_id_fk_164887" => $user_id,
                        "liam2_email_id_fk_396224" => $email_id
                    ]
                )
            )));
        }
        if (isset($_GET['liam2_add_another_email'])) {
            header("Location: http:" . $_GET['origin']);
            exit();
        }
    }
    if (isset($_POST['liam2_verify_email'])) {
        $email_id = htmlspecialchars($_POST['email']);
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_email",
                "row" => [
                    "liam2_email_id" => $email_id,
                    "only_verify_mail" => true,
                    "state_id" => 13
                ]
            )
        )));
        $result = json_decode($result, true);
        if (count($result) > 2) {
            $result2 = api(json_encode(array(
                "cmd" => "read",
                "paramJS" => array(
                    "table" => "liam2_email",
                    "where" => "liam2_email_id = $email_id"
                )
            )));
            $result2 = json_decode($result2, true);
            $email = $result2[0]['liam2_email_text'];
            $jwt_key = AUTH_KEY;
            $jwt_token = array(
                "iss" => "liam2",
                "aud" => $email_id,
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

            $subject = "Verification";
            $excluded_ports = array(80, 443);
            if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
                $server_port = '';
            } else {
                $server_port = ':' . $_SERVER['SERVER_PORT'];
            }
            $link = "http://" . $_SERVER['SERVER_NAME'] . $server_port . "/LIAM2_Client_verify.php?token=" . $jwt;
            $msg = translate('LIAM2 CLIENT verify email', 'en');
            $msg = str_replace('$link', $link, $msg);
            // Format and Send Mail
            $msg = wordwrap($msg, 70);
            mail($email, $subject, $msg);
            $success = 'A verification link has been sent to your email address.';
            /*if (mail($email, $subject, $msg)) {
                $success = 'A verification link has been sent to your email address.';
            } else {
                $error = "The email can't be send";
            }*/
        } else {
            $error = $result[0]['message'];
        }
    }
    if (isset($_POST['liam2_select_email'])) {
        $user_email_id = htmlspecialchars($_POST['email']);
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_User_email",
                "row" => [
                    "liam2_User_email_id" => $user_email_id,
                    "state_id" => 11
                ]
            )
        )));
        $success = 'Email successfully selected.';
    }
    if (isset($_POST['liam2_unselect_email'])) {
        $user_email_id = htmlspecialchars($_POST['email']);
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_User_email",
                "row" => [
                    "liam2_User_email_id" => $user_email_id,
                    "state_id" => 12
                ]
            )
        )));
        $success = 'Email successfully unselected.';
    }
    if (isset($_POST['liam2_dont_unselect_email'])) {
        $error = "Minimum one e-email address must be used.";
    }
    if (isset($_POST['liam2_delete_email'])) {
        $email_id = htmlspecialchars($_POST['email']);
        $user_email_id = htmlspecialchars($_POST['delete_user_email_id']);
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_User_email",
                "row" => [
                    "liam2_User_email_id" => $user_email_id,
                    "state_id" => 12
                ]
            )
        )));
        $result = json_decode($result, true);
        if (!$result) {
            $result = api(json_encode(array(
                "cmd" => "makeTransition",
                "paramJS" => array(
                    "table" => "liam2_User_email",
                    "row" => [
                        "liam2_User_email_id" => $user_email_id,
                        "state_id" => 11
                    ]
                )
            )));
        }
        if (!$result) $error = 'Wrong email';
        if (!isset($error)) {
            $result = api(json_encode(array(
                "cmd" => "read",
                "paramJS" => array(
                    "table" => "liam2_email",
                    "where" => "liam2_email_id = $email_id"
                )
            )));
            $result = json_decode($result, true);
            if (!$result) $error = 'Wrong email';
            if (!isset($error)) {
                $result = api(json_encode(array(
                    "cmd" => "makeTransition",
                    "paramJS" => array(
                        "table" => "liam2_email",
                        "row" => [
                            "liam2_email_id" => $email_id,
                            "liam2_email_text" => $result[0]['liam2_email_text'],
                            "state_id" => 16
                        ]
                    )
                )));
                $result = json_decode($result, true);
                if (!$result) {
                    $error = 'Something went wrong.';
                } else {
                    $success = $result[0]['message'];
                }
            }
        }
    }

    $user_emails = json_decode(api(json_encode(array(
        "cmd" => "read",
        "paramJS" => array(
            "table" => "liam2_User_email",
            "where" => "liam2_User_id = $_SESSION[user_id]"
        )
    ))), true);
    $selected_user_emails = array();
    $unselected_user_emails = array();
    foreach ($user_emails as $key => $user_email) {
        $email_id = $user_email['liam2_email_id_fk_396224']['liam2_email_id'];
        $email = json_decode(api(json_encode(array(
            "cmd" => "read",
            "paramJS" => array(
                "table" => "liam2_email",
                "where" => "liam2_email_id = $email_id && a.state_id != 16"
            )
        ))), true);
        if (!$email) {
            unset($user_emails[$key]);
            continue;
        }
        if ($email[0]['state_id']['state_id'] == 13) {
            $user_emails[$key]['not_verified'] = true;
            continue;
        }
        if ($user_email['state_id']['state_id'] == 11) {
            array_push($selected_user_emails, $user_email);
        } elseif ($user_email['state_id']['state_id'] == 12) {
            array_push($unselected_user_emails, $user_email);
        }
    }
    require_once(__DIR__ . '/inc/LIAM2_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM2_Client_manage_emails.inc.php');
}