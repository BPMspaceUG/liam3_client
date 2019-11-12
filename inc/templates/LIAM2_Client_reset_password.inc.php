<div class="modal fade" id="liam2_reset_password_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)) : ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($show_login_button)) : ?>
                <a class="form-submit btn btn-primary" href="LIAM2_Client_login.php">Login</a>
            <?php endif; ?>
            <?php if ($show_form) : ?>
                <h2>Enter new password</h2>
                <form method="post" action="" class="needs-validation">
                    <div class="form-group row">
                        <label for="liam2_User_password_new" class="col-lg-4 col-sm-6">New password *</label>
                        <div class="show-hide-password col-lg-8">
                            <input type="password" name="liam2_User_password_new" class="form-control" minlength="10" required />
                            <div class="input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="liam2_User_password_new_confirm" class="col-lg-4 col-sm-6">Confirm new password *</label>
                        <div class="show-hide-password col-lg-8">
                            <input type="password" name="liam2_User_password_new_confirm" class="form-control" minlength="10" required />
                            <div class="input-group-addon">
                                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <input type="submit" class="form-submit btn btn-primary" value="Reset Password" name="liam2_reset_password" />
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>