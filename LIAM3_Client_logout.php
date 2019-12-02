<?php
$excluded_ports = array(80, 443);
if (in_array($_SERVER['SERVER_PORT'], $excluded_ports)) {
$server_port = '';
} else {
$server_port = ':' . $_SERVER['SERVER_PORT'];
}
$liam3_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port;
?>
<script>
    sessionStorage.removeItem('token');
    window.location.href = "<?php echo $liam3_url . '/LIAM3_Client_login.php'; ?>";
</script>
<?php
exit();