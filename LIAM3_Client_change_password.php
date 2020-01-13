<?php require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
$change_password = isset($_POST['liam3_change_password']) ? $_POST['liam3_change_password'] : false;
$password_old = isset($_POST['liam3_User_password_old']) ? htmlspecialchars($_POST['liam3_User_password_old']) : '';
$password_new = isset($_POST['liam3_User_password_new']) ? htmlspecialchars($_POST['liam3_User_password_new']) : '';
$password_confirm = isset($_POST['liam3_User_password_new_confirm']) ? htmlspecialchars($_POST['liam3_User_password_new_confirm']) : '';
?>
<script>
    var token = sessionStorage.getItem('token');
    $.ajax({
        type: 'POST',
        url: 'controller/liam3_Client_change_password.php',
        beforeSend: function (xhr) {
            xhr.setRequestHeader ("Authorization", token);
        },
        data: {'token': true,
            'liam3_change_password': '<?php echo $change_password; ?>',
            'liam3_User_password_old': '<?php echo $password_old; ?>',
            'liam3_User_password_new': '<?php echo $password_new; ?>',
            'liam3_User_password_new_confirm': '<?php echo $password_confirm; ?>'
        },
        success: function (data){
            $('body').append(data);
        }
    });
</script>