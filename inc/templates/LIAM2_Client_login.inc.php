<div class="modal fade" id="liam2_login_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <?php if (isset($error) && $error) { ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php } ?>
            <h2>Login</h2>
            <form method="post" action="" class="needs-validation">
                <div class="form-group row">
                    <label for="email" class="col-lg-4 col-sm-6">E-Mail *</label>
                    <input type="text" name="email" class="form-control col-lg-8" required />
                </div>
                <div class="form-group row">
                    <label for="password" class="col-lg-4 col-sm-6">Password *</label>
                    <div class="show-hide-password col-lg-8">
                        <input type="password" name="password" class="form-control" required />
                        <div class="input-group-addon">
                            <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <input type="hidden" name="code" value="<?php echo $code; ?>" />
                    <input type="hidden" name="captcha-image" value="<?php echo $captchaImage; ?>" />
                    <label for="result" class="col-lg-4">Captcha *</label>
                    <img src="<?php echo $captchaImage; ?>" class="captcha-image col-lg-4 col-sm-4" />
                    <input type="text" name="result" class="form-control col-lg-4 col-sm-8" autocomplete="off" required />
                </div>
                <a class="form-submit btn btn-primary" href="LIAM2_Client_forgot_password.php">Forgot password</a>
                <a class="form-submit btn btn-primary" href="LIAM2_Client_self_register.php">Register</a>
                <input type="submit" class="form-submit btn btn-primary" value="Login" name="liam2_login" />
            </form>
        </div>
    </div>
</div>