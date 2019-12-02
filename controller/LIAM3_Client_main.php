<?php
require_once(__DIR__ . '/../inc/LIAM3_Client_header_session.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/BeforeValidException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/ExpiredException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/SignatureInvalidException.inc.php');
require_once(__DIR__ . '/../inc/php-jwt-master/src/JWT.inc.php');
use \Firebase\JWT\JWT;
$excluded_ports = array(80, 443);
if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
    $server_port = '';
} else {
    $server_port = ':' . $_SERVER['SERVER_PORT'];
}
$liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;
if (!isset($_POST['token'])) {
    ?>
    <script>
        window.location.href = "<?php echo $liam3_url . '/LIAM3_Client_login.php'; ?>";
    </script>
    <?php
    exit();
} elseif ($_POST['origin']) {
    ?>
    <script>
        window.location.href = "<?php echo $liam3_url . '/LIAM3_Client_login.php?origin=' . $_POST['origin']; ?>";
    </script>
    <?php
    exit();
} else {
    //$token = $_SESSION['token'];
    /*$tks = explode('.', $jwt);
    list($headb64, $bodyb64, $cryptob64) = $tks;
    $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));*/

    /**
     * You can add a leeway to account for when there is a clock skew times between
     * the signing and verifying servers. It is recommended that this leeway should
     * not be bigger than a few minutes.
     *
     * Source: http://self-issued.info/docs/draft-ietf-oauth-json-web-token.html#nbfDef
     */
    $token = isset(apache_request_headers()['Authorization']) ? apache_request_headers()['Authorization'] : '';
    if (!$token || $token == 'null') {
        ?>
        <script>
            sessionStorage.removeItem('token');
            window.location.href = "<?php echo $liam3_url . '/LIAM3_Client_login.php'; ?>";
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
            window.location.href = "<?php echo $liam3_url . '/LIAM3_Client_login.php?error=' . $error; ?>";
        </script>
        <?php
        exit();
    }
    $user_id = $decoded->uid;
    $user = json_decode(api(json_encode(array(
            "cmd" => "read",
            "param" => array(
                "table" => "liam3_user",
                "filter" => '{"=":["liam3_User_id", ' . $user_id . ']}'
            ))
    )), true);
    $username = $user["records"][0]['liam3_User_firstname'] . ' ' . $user["records"][0]['liam3_User_lastname'];
    require_once(__DIR__ . '/../inc/templates/LIAM3_Client_main.inc.php');
}