<?php
require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
require_once(__DIR__ . '/inc/liam3_Client_header_session.inc.php');
require_once(__DIR__ . '/inc/captcha/captcha.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
generateImage($expression->n1.' + '.$expression->n2.' =', $captchaImage);

//Brute force prevention
$apc_login_key = "{$_SERVER['SERVER_NAME']}~login:{$_SERVER['REMOTE_ADDR']}";
$apc_login_blocked_key = "{$_SERVER['SERVER_NAME']}~login-blocked:{$_SERVER['REMOTE_ADDR']}";
$apc_captcha_key = "{$_SERVER['SERVER_NAME']}~captcha:{$_SERVER['REMOTE_ADDR']}";
$apc_captcha_blocked_key = "{$_SERVER['SERVER_NAME']}~captcha-blocked:{$_SERVER['REMOTE_ADDR']}";
$login_tries = (int)apcu_fetch($apc_login_key);
if ($login_tries >= liam3_failed_login_max) {
    header("HTTP/1.1 429 Too Many Requests");
    echo "You've exceeded the number of login attempts. We've blocked IP address {$_SERVER['REMOTE_ADDR']} for a few minutes.";
    exit();
}
$captcha_tries = (int)apcu_fetch($apc_captcha_key);
if ($captcha_tries >= liam3_failed_captcha_max) {
    header("HTTP/1.1 429 Too Many Requests");
    echo "You've exceeded the number of captcha attempts. We've blocked IP address {$_SERVER['REMOTE_ADDR']} for a few minutes.";
    exit();
}

if (isset($_POST['liam3_login'])) {
    if (file_exists($_POST['captcha-image'])) unlink($_POST['captcha-image']);
    if (!$_POST['email'] || !$_POST['password']) {
        $error = 'Please fill all the fields.';
    } else {
        $email_input = htmlspecialchars($_POST['email']);
        if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
            $error = "Email address '$email_input' is not valid.\n";
        } else {
            $password_input = $_POST['password'];
            $sentCode = htmlspecialchars($_POST['code']);
            $captcha_result = (int)$_POST['result'];
            if (getExpressionResult($sentCode) !== $captcha_result) {
                $error = 'Wrong Captcha.';
                $login_attempt_info = 'Not Successful - ' . $email_input . ' - ' . $error;
                api(json_encode(array(
                    "cmd" => "create",
                    "param" => array(
                        "table" => "liam3_loginattempts",
                        "row" => array(
                            "liam3_LoginAttempts_time" => date('Y-m-d H:i'),
                            "liam3_LoginAttempts_info" => $login_attempt_info
                        )
                    )
                )));
                $captcha_blocked = (int)apcu_fetch($apc_captcha_blocked_key);
                apcu_store($apc_captcha_key, $captcha_tries+1, pow(2, $captcha_blocked+1)*60);  # store tries for 2^(x+1) minutes: 2, 4, 8, 16, ...
                apcu_store($apc_captcha_blocked_key, $captcha_blocked+1, 86400);  # store number of times blocked for 24 hours
            } else {
                $error = false;
                apcu_delete($apc_captcha_key);
                apcu_delete($apc_captcha_blocked_key);
                $login = json_decode(api(json_encode(array("cmd" => "login", "param" => array("email" => $email_input, "password" => $password_input)))), true);
                if (isset($login['login_valid']) && $login['login_valid']) {
                    $token = $login['token'];
                    /**
                     * You can add a leeway to account for when there is a clock skew times between
                     * the signing and verifying servers. It is recommended that this leeway should
                     * not be bigger than a few minutes.
                     *
                     * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
                     */
                    JWT::$leeway = 60; // $leeway in seconds
                    try {
                        $decoded = JWT::decodeWithoutKey($token, array('HS256'));
                    } catch (Exception $e) {
                        $error = $e->getMessage();
                    }
                    $user_id = $decoded->liam3_user_id;
                    apcu_delete($apc_login_key);
                    apcu_delete($apc_login_blocked_key);
                    if (isset($_GET['origin'])) {
                        $origin = $_GET['origin'];
                        header("Location: " . $origin . "?token=" . $token);
                        exit();
                    } else {
                        /*$excluded_ports = array(80, 443);
                        if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
                            $server_port = '';
                        } else {
                            $server_port = ':' . $_SERVER['SERVER_PORT'];
                        }
                        $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;*/
                        $liam3_url = LIAM3_URL;
                        ?>
                        <script>
                            sessionStorage.setItem("token", "<?php echo $token; ?>");
                            window.location.href = "<?php echo $liam3_url . '/index.php'; ?>";
                        </script>
                        <?php
                        exit();
                    }
                } else {
                    $error = $login['error']['msg'];
                    $login_blocked = (int)apcu_fetch($apc_login_blocked_key);
                    apcu_store($apc_login_key, $login_tries+1, pow(2, $login_blocked+1)*60);  # store tries for 2^(x+1) minutes: 2, 4, 8, 16, ...
                    apcu_store($apc_login_blocked_key, $login_blocked+1, 86400);  # store number of times blocked for 24 hours
                }
            }
        }
    }
}
/*if (isset($_SESSION['token'])) {
    $excluded_ports = array(80, 443);
    if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
        $server_port = '';
    } else {
        $server_port = ':' . $_SERVER['SERVER_PORT'];
    }
    $liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;
    ?>
    <script>
        window.location.href = "<?php echo $liam3_url . '/index.php'; ?>";
    </script>
    <?php
} else {
    require_once(__DIR__ . '/inc/templates/liam3_Client_login.inc.php');
}*/
if (isset($_GET['error'])) $error = "Token error: " . $_GET['error'];
require_once(__DIR__ . '/inc/templates/liam3_Client_login.inc.php');