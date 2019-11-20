<?php
require_once(__DIR__ . '/inc/LIAM3_Client_header_session.inc.php');
if (!isset($_SESSION['token'])) {
    header("Location: LIAM3_Client_login.php");
    exit();
} else {
    if (isset($_POST['liam3_change_password'])) {
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "param" => array(
                "table" => "liam3_user",
                "row" => array(
                    "liam3_User_id" => $_SESSION['user_id'],
                    "state_id" => 9
                )
            )
        )));
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "param" => array(
                "table" => "liam3_user",
                "row" => array(
                    "liam3_User_id" => $_SESSION['user_id'],
                    "liam3_User_password_old" => htmlspecialchars($_POST['liam3_User_password_old']),
                    "liam3_User_password_new" => htmlspecialchars($_POST['liam3_User_password_new']),
                    "liam3_User_password_new_confirm" => htmlspecialchars($_POST['liam3_User_password_new_confirm']),
                    "state_id" => 8
                )
            )
        )));
        $result = json_decode($result, true);
        if (count($result) > 2 && $result[1]['change_password']) {
            $success = 'Password changed succesfully';
        } else {
            $error = $result[1]['message'];
        }
    }
    require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM3_Client_change_password.inc.php');
}
