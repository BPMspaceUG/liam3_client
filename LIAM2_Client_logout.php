<?php
require_once(__DIR__ . '/inc/LIAM2_Client_header_session.inc.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: LIAM2_Client_login.php");
    exit();
} else {
    session_destroy();
    header("Location: LIAM2_Client_login.php");
    exit();
}