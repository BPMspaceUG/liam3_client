<?php require_once(__DIR__ . '/inc/LIAM3_Client_header.inc.php');
$add_another_email = isset($_REQUEST['liam3_add_another_email']) ? htmlspecialchars($_REQUEST['liam3_add_another_email']) : '';
$get_request_add_another_email = isset($_GET['liam3_add_another_email']) ? htmlspecialchars($_GET['liam3_add_another_email']) : '';
$get_request_user_id = isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : '';
$verify_email = isset($_POST['liam3_verify_email']) ? $_POST['liam3_verify_email'] : false;
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$select_email = isset($_POST['liam3_select_email']) ? $_POST['liam3_select_email'] : false;
$unselect_email = isset($_POST['liam3_unselect_email'])? $_POST['liam3_unselect_email'] : false;
$dont_unselect_email = isset($_POST['liam3_dont_unselect_email']) ? $_POST['liam3_dont_unselect_email'] : false;
$delete_email = isset($_POST['liam3_delete_email']) ? $_POST['liam3_delete_email'] : false;
$origin = isset($_GET['origin']) ? $_GET['origin'] : '';
?>
<script>
    var token = sessionStorage.getItem('token');
    $.ajax({
        type: 'POST',
        url: 'controller/LIAM3_Client_manage_emails.php',
        beforeSend: function (xhr) {
            xhr.setRequestHeader ("Authorization", token);
        },
        data: {'token': true,
            'liam3_add_another_email': '<?php echo $add_another_email; ?>',
            'liam3_get_request_add_another_email': '<?php echo $get_request_add_another_email; ?>',
            'liam3_get_request_user_id': '<?php echo $get_request_user_id; ?>',
            'liam3_verify_email': '<?php echo $verify_email; ?>',
            'email': '<?php echo $email; ?>',
            'liam3_select_email': '<?php echo $select_email; ?>',
            'liam3_unselect_email': '<?php echo $unselect_email; ?>',
            'liam3_dont_unselect_email': '<?php echo $dont_unselect_email; ?>',
            'liam3_delete_email': '<?php echo $delete_email; ?>',
            'origin': '<?php echo $origin; ?>'
        },
        success: function (data){
            $('body').append(data);
        }
    });
</script>