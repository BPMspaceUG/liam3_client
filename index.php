<?php require_once(__DIR__ . '/inc/liam3_Client_header.inc.php');
$origin = isset($_GET['origin']) ? $_GET['origin'] : '';
?>
<script>
    var token = sessionStorage.getItem('token');
    $.ajax({
        type: 'POST',
        url: 'controller/liam3_Client_main.php',
        beforeSend: function (xhr) {
            xhr.setRequestHeader ("Authorization", token);
        },
        data: {
            'token': true,
            'origin': '<?php echo $origin; ?>'
        },
        success: function (data){
            $('body').append(data);
        }
    });
</script>