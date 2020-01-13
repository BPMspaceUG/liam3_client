<?php
require_once(__DIR__ . '/../inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
/*$excluded_ports = array(80, 443);
if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
    $server_port = '';
} else {
    $server_port = ':' . $_SERVER['SERVER_PORT'];
}
$liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;*/
$liam3_url = LIAM3_URL;
if (!isset($_POST['token'])) {
    ?>
    <script>
        window.location.href = "<?php echo $liam3_url . '/liam3_Client_login.php'; ?>";
    </script>
    <?php
} else {
    $token = isset(apache_request_headers()['Authorization']) ? apache_request_headers()['Authorization'] : '';
    if (!$token || $token == 'null') {
        ?>
        <script>
            sessionStorage.removeItem('token');
            window.location.href = "<?php echo $liam3_url . '/liam3_Client_login.php'; ?>";
        </script>
        <?php
        exit();
    }
    JWT::$leeway = 60; // $leeway in seconds
    try {
        $decoded = JWT::decodeWithoutKey($token, array('HS256'));
    } catch (Exception $e) {
        $error = $e->getMessage();
        ?>
        <script>
            sessionStorage.removeItem('token');
            window.location.href = "<?php echo $liam3_url . '/liam3_Client_login.php?error=' . $error; ?>";
        </script>
        <?php
        exit();
    }
    $user_id = $decoded->uid;
    if ($_POST['liam3_change_password']) {
        $change_password = api(json_encode(array(
            "cmd" => "changePassword",
            "param" => array(
                "user_id" => $user_id,
                "password_old" => $_POST['liam3_User_password_old'],
                "password_new" => $_POST['liam3_User_password_new'],
                "password_new_confirm" => $_POST['liam3_User_password_new_confirm']
            )
        )));
        try {
            $change_password = json_decode($change_password, true);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if (isset($change_password['message'])) {
            $success = $change_password['message'];
        } else {
            $error = $change_password['error']['msg'];
        }
    }
    require_once(__DIR__ . '/../inc/templates/liam3_Client_change_password.inc.php');
}
