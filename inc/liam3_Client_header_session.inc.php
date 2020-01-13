<script type="text/javascript" src="js/liam3_Client_main.js"></script>
<?php
//session_start();
require_once(__DIR__ . '/liam3_Client_api.secret.inc.php');
require_once(__DIR__ . '/liam3_Client_api.inc.php');
define('EMAIL_STATE_NOT_VERIFIED', 1);
define('EMAIL_STATE_VERIFIED', 2);
define('EMAIL_STATE_DELETED', 4);
define('USER_STATE_COMPLETE', 10);
define('USER_STATE_UPDATE', 11);
define('USER_EMAIL_STATE_USE', 13);
define('USER_EMAIL_STATE_UNSELECTED', 14);
