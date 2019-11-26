<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
if (!isset($_SESSION['token']) && !isset($_GET['liam3_add_another_email'])) {
    header("Location: LIAM3_Client_login.php");
    exit();
} else {
    $token = $_SESSION['token'];
    if (isset($_REQUEST['liam3_add_another_email'])) {
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_GET['user_id'];
        $email = htmlspecialchars($_REQUEST['liam3_add_another_email']);
        $excluded_ports = array(80, 443);
        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
            $server_port = '';
        } else {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;
        $add_another_email = api(json_encode(array("cmd" => "addAnotherEmail", "param" => array(
            "liam3_url" => $liam3_url,
            "user_id" => $user_id,
            "email" => $email
        ))));
        $add_another_email = json_decode($add_another_email, true);
        if (isset($add_another_email['error'])) {
            $error = $add_another_email['error']['msg'];
        } else {
            $success = $add_another_email['message'];
        }
        if (isset($_GET['liam3_add_another_email'])) {
            header("Location: http:" . $_GET['origin']);
            exit();
        }
    }
    if (isset($_POST['liam3_verify_email'])) {
        $email_id = htmlspecialchars($_POST['email']);
        $excluded_ports = array(80, 443);
        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
            $server_port = '';
        } else {
            $server_port = ':' . $_SERVER['SERVER_PORT'];
        }
        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;
        $verify_email = api(json_encode(array("cmd" => "verifyEmail", "param" => array(
            "liam3_url" => $liam3_url,
            "email_id" => $email_id
        ))));
        $verify_email = json_decode($verify_email, true);
        if (isset($verify_email['error'])) {
            $error = $verify_email['error']['msg'];
        } else {
            $success = $verify_email['message'];
        }
    }
    if (isset($_POST['liam3_select_email'])) {
        $user_email_id = htmlspecialchars($_POST['email']);
        $select_email = api(json_encode(array(
            "cmd" => "selectEmail",
            "param" => array(
                "user_email_id" => $user_email_id
            )
        )));
        try {
            $select_email = json_decode($select_email, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            if (isset($select_email['message'])) {
                $success = $select_email['message'];
            } else {
                $error = $select_email['error']['msg'];
            }
        }
    }
    if (isset($_POST['liam3_unselect_email'])) {
        $user_email_id = htmlspecialchars($_POST['email']);
        $unselect_email = api(json_encode(array(
            "cmd" => "unselectEmail",
            "param" => array(
                "user_email_id" => $user_email_id
            )
        )));
        try {
            $unselect_email = json_decode($unselect_email, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            if (isset($unselect_email['message'])) {
                $success = $unselect_email['message'];
            } else {
                $error = $unselect_email['error']['msg'];
            }
        }
    }
    if (isset($_POST['liam3_dont_unselect_email'])) {
        $error = "Minimum one e-email address must be used.";
    }
    if (isset($_POST['liam3_delete_email'])) {
        $email_id = htmlspecialchars($_POST['email']);
        if (!isset($error)) {
            $delete_email = api(json_encode(array(
                "cmd" => "deleteEmail",
                "param" => array(
                    "email_id" => $email_id
                )
            )));
            try {
                $delete_email = json_decode($delete_email, true);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
            if (!isset($error)) {
                if (isset($delete_email['message'])) {
                    $success = $delete_email['message'];
                } else {
                    $error = $delete_email['error']['msg'];
                }
            }
        }
    }

    $user_emails = json_decode(api(json_encode(array(
        "cmd" => "read",
        "param" => array(
            "table" => "liam3_user_email",
            "filter" => '{"=":["liam3_User_id", '.$_SESSION["user_id"].'}'
        )
    ))), true);
    $selected_user_emails = array();
    $unselected_user_emails = array();
    $user_emails = $user_emails['records'];
    foreach ($user_emails as $key => $user_email) {
        $email_id = $user_email['liam3_email_id_fk_396224']['liam3_email_id'];
        $email = json_decode(api(json_encode(array(
            "cmd" => "read",
            "param" => array(
                "table" => "liam3_email",
                "filter" => '{"and":[{"=":["liam3_email_id","' . $email_id . '"]},{"!=":["liam3_email.state_id",' . EMAIL_STATE_DELETED . ']}]}'
            )
        ))), true);
        $email = $email['records'];
        if (!$email) {
            unset($user_emails[$key]);
            continue;
        }
        if ($email[0]['state_id'] == EMAIL_STATE_NOT_VERIFIED) {
            $user_emails[$key]['not_verified'] = true;
            continue;
        }
        if ($user_email['state_id'] == USER_EMAIL_STATE_USE) {
            array_push($selected_user_emails, $user_email);
        } elseif ($user_email['state_id'] == USER_EMAIL_STATE_UNSELECTED) {
            array_push($unselected_user_emails, $user_email);
        }
    }
    require_once(__DIR__ . '/inc/templates/LIAM3_Client_manage_emails.inc.php');
}