<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: LIAM2_Client_login.php");
    exit();
} else {
    $user = json_decode(api(json_encode(array(
        "cmd" => "read",
        "paramJS" => array(
            "table" => "liam2_User",
            "filter" => '{"=":["liam2_User_id", '.$_SESSION['user_id'].']}'
        ))
    )), true);
    $username = $user[0]['liam2_User_firstname'] . ' ' . $user[0]['liam2_User_lastname'];
    require_once(__DIR__ . '/inc/LIAM2_Client_header.inc.php');
    require_once(__DIR__ . '/inc/templates/LIAM2_Client_main.inc.php');
}
