<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: LIAM2_Client_login.php");
    exit();
} else {
    if (isset($_POST['liam2_change_password'])) {
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_User",
                "row" => array(
                    "liam2_User_id" => $_SESSION['user_id'],
                    "state_id" => 9
                )
            )
        )));
        $result = api(json_encode(array(
            "cmd" => "makeTransition",
            "paramJS" => array(
                "table" => "liam2_User",
                "row" => array(
                    "liam2_User_id" => $_SESSION['user_id'],
                    "liam2_User_password_old" => htmlspecialchars($_POST['liam2_User_password_old']),
                    "liam2_User_password_new" => htmlspecialchars($_POST['liam2_User_password_new']),
                    "liam2_User_password_new_confirm" => htmlspecialchars($_POST['liam2_User_password_new_confirm']),
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
    require_once(__DIR__ . '/inc/LIAM2_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM2_Client_change_password.inc.php');
}
