<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
if (!isset($_SESSION['token'])) {
    header("Location: LIAM3_Client_login.php");
    exit();
} else {
    $token = $_SESSION['token'];
    if (isset($_POST['liam3_change_password'])) {
        $change_password = api(json_encode(array(
            "cmd" => "changePassword",
            "param" => array(
                "user_id" => $_SESSION['user_id'],
                "password_old" => htmlspecialchars($_POST['liam3_User_password_old']),
                "password_new" => htmlspecialchars($_POST['liam3_User_password_new']),
                "password_new_confirm" => htmlspecialchars($_POST['liam3_User_password_new_confirm'])
            )
        )));
        try {
            $change_password = json_decode($change_password, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (!isset($error)) {
            if (isset($change_password['message'])) {
                $success = $change_password['message'];
            } else {
                $error = $change_password['error']['msg'];
            }
        }
    }
    require_once(__DIR__ . '/inc/templates/LIAM3_Client_change_password.inc.php');
}
