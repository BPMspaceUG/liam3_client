<div class="modal fade" id="liam2_forgot_password_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)) : ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
                <a class="form-submit btn btn-primary" href="LIAM2_Client_forgot_password.php?email=<?php echo $email_input; ?>">Resend Email</a>
            <?php endif; ?>
            <?php if ($show_form) : ?>
            <h2>Reset password</h2>
            <form method="post" action="" class="needs-validation">
                <div class="form-group row">
                    <label for="email" class="col-lg-4 col-sm-6">E-Mail</label>
                    <input type="text" id="email" name="email" class="form-control col-lg-8" required />
                </div>
                <input type="submit" value="OK" class="form-submit btn btn-primary" name="forgot_password" />
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>