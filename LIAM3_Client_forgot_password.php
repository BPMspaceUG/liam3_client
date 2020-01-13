<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_translate.inc.php');
$show_form = true;
if (isset($_POST['forgot_password']) || isset($_GET['email'])) {
    $email_input = htmlspecialchars($_REQUEST['email']);
    /*$excluded_ports = array(80, 443);
    if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
        $server_port = '';
    } else {
        $server_port = ':' . $_SERVER['SERVER_PORT'];
    }
    $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;*/
    $liam3_url = LIAM3_URL;
    $forgot_password = api(json_encode(array("cmd" => "forgotPassword", "param" => array(
        "liam3_url" => $liam3_url,
        "email" => $email_input,
    ))));
    $forgot_password = json_decode($forgot_password, true);
    if (isset($forgot_password['message'])) {
        $success = $forgot_password['message'];
        $show_form = false;
    } else {
        $error = $forgot_password['error']['msg'];
    }
}
require_once(__DIR__ . '/inc/templates/liam3_Client_forgot_password.inc.php');