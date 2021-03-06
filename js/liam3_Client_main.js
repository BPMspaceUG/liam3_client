$(document).ready( function () {
    $('.modal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('.modal').on('shown.bs.modal', function() {
        $('.autofocus').trigger('focus');
    });

    $('#liam3_add_another_email').click(function(){
        $('#liam3_add_another_email_form').submit();
    });

    $('.liam3-delete-email').click(function(){
        if(!confirm("Please confirm that you want to remove this e-mail address.")){
            return false;
        }
    });

    $(".show-hide-password a").on('click', function(e) {
        e.preventDefault();
        var input = $(this).closest('.show-hide-password').find('input');
        var icon = $(this).find('i');
        if (input.attr("type") == "text") {
            input.attr('type', 'password');
            icon.addClass( "fa-eye-slash" );
            icon.removeClass( "fa-eye" );
        } else if (input.attr("type") == "password") {
            input.attr('type', 'text');
            icon.removeClass( "fa-eye-slash" );
            icon.addClass( "fa-eye" );
        }
    });
});