<div class="modal fade" id="liam3_manage_emails_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content manage-email">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)) : ?>
                <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
            <?php endif; ?>
            <h2>Manage E-Mails</h2>
            <form id="liam3_add_another_email_form" method="post" action="" class="needs-validation">
                <div class="form-group row">
                    <input type="text" name="liam3_add_another_email" class="form-control col-lg-6 col-10 manage-email-fields" required />
                    <a href="#" id="liam3_add_another_email" class="col-lg-6 col-2 manage-email-fields">
                        <i class="fas fa-plus-circle fa-3x"></i>
                    </a>
                </div>
            </form>
            <?php foreach ($user_emails as $user_email) :
                $email_id = $user_email['liam3_email_id_fk_396224']['liam3_email_id'];
                $email_text = $user_email['liam3_email_id_fk_396224']['liam3_email_text']; ?>
                <div class='row'>
                    <?php if (isset($user_email['not_verified'])) : ?>
                        <div class="col-lg-6 manage-email-fields"><?php echo $email_text; ?></div>
                        <div class="col-lg-3 manage-email-fields">
                            <form method="post" action="" class="needs-validation">
                                <input type="hidden" id="verify_email" name="email" value="<?php echo $email_id; ?>">
                                <input type="submit" class="form-submit btn btn-primary" value="Send" name="liam3_verify_email" />
                            </form>
                        </div>
                        <div class="col-lg-3">
                            <div class="manage-email-fields float-right manage-email-text">Not verified</div>
                        </div>

                    <?php elseif ($user_email['state_id']['state_id'] == 11) : ?>
                        <div class="col-lg-6 manage-email-fields"><?php echo $email_text; ?></div>
                            <div class="col-lg-3 manage-email-fields">
                                <form method="post" action="" class="needs-validation">
                                    <input type="hidden" name="email" value="<?php echo $user_email['liam3_User_email_id']; ?>" />
                                    <?php if (count($selected_user_emails) > 1) : ?>
                                        <input type="submit" class="form-submit btn btn-primary" value="Do not use" name="liam3_unselect_email" data-toggle="tooltip" data-placement="top" title="Notifications and messages will not be sent to this e-mail address" />
                                    <?php else : ?>
                                        <input type="submit" class="form-submit btn btn-primary" value="Do not use" name="liam3_dont_unselect_email" data-toggle="tooltip" data-placement="top" title="Notifications and messages will not be sent to this e-mail address" />
                                    <?php endif; ?>
                                </form>
                            </div>
                    <?php
                        /*if (count($selected_user_emails) > 1) : ?>
                            <div class="col-lg-3 manage-email-fields">
                                <form method="post" action="" class="needs-validation">
                                    <input type="hidden" name="email" value="<?php echo $email_id; ?>" />
                                    <input name="delete_user_email_id" type="hidden" value="<?php echo $user_email['liam3_User_email_id']; ?>" />
                                    <input type="submit" class="form-submit btn btn-primary" value="Delete email" name="liam3_delete_email" />
                                </form>
                            </div>
                        <?php endif;*/
                    else : ?>
                        <div class="col-lg-6 manage-email-fields"><?php echo $email_text; ?></div>
                        <div class="col-lg-3 manage-email-fields">
                            <form method="post" action="" class="needs-validation">
                                <input type="hidden" name="email" value="<?php echo $user_email['liam3_User_email_id']; ?>" />
                                <input type="submit" class="form-submit btn btn-primary" value="Use" name="liam3_select_email" data-toggle="tooltip" data-placement="top" title="Notifications and messages will be sent to this e-mail address" />
                            </form>
                        </div>
                        <?php if (count($selected_user_emails) > 1 || ($selected_user_emails && $unselected_user_emails)) : ?>
                            <div class="col-lg-3 manage-email-fields">
                                <form method="post" action="" class="needs-validation">
                                    <input type="hidden" name="email" value="<?php echo $email_id; ?>">
                                    <input name="delete_user_email_id" type="hidden" value="<?php echo $user_email['liam3_User_email_id']; ?>" />
                                    <input type="submit" class="form-submit btn btn-primary liam3-delete-email" value="Delete email" name="liam3_delete_email" data-toggle="tooltip" data-placement="top" title="This e-mail address will be removed" />
                                </form>
                            </div>
                        <?php endif;
                    endif; ?>
                </div>
            <?php endforeach; ?>
            <div class="row go-back">
                <a class="form-submit btn btn-primary" href="./">Go Back</a>
            </div>
        </div>
    </div>
</div>