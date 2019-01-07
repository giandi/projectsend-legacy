<script type="text/javascript">
    /**
        * Quick hack to ignore the col-sm-* classes
        * when adding the errors to the form
        */
    var ignore_columns = true;

    $(document).ready(function() {
        $("form").submit(function() {

            clean_form(this);

            is_complete(this.reset_password_new, json_strings.validation.no_pass);
            is_length(this.reset_password_new, json_strings.validation.password_min, json_strings.validation.password_max, json_strings.validation.length_pass);
            is_password(this.reset_password_new, json_strings.validation.valid_pass + " " + json_strings.validation.valid_chars);

            // show the errors or continue if everything is ok
            if (show_form_errors() == false) { return false; }
        });
    });
</script>

<form action="reset-password.php?token=<?php echo html_output($got_token); ?>&user=<?php echo html_output($got_user); ?>" name="newpassword" method="post" role="form">
    <fieldset>
        <input type="hidden" name="form_type" id="form_type" value="new_password" />

        <div class="form-group">
            <label for="reset_password_new"><?php _e('New password','cftp_admin'); ?></label>
            <div class="input-group">
                <input type="password" name="reset_password_new" id="reset_password_new" class="form-control password_toggle" />
                <div class="input-group-btn password_toggler">
                    <button type="button" class="btn pass_toggler_show"><i class="glyphicon glyphicon-eye-open"></i></button>
                </div>
            </div>
            <button type="button" name="generate_password" id="generate_password" class="btn btn-default btn-sm btn_generate_password" data-ref="reset_password_new" data-min="<?php echo MAX_GENERATE_PASS_CHARS; ?>" data-max="<?php echo MAX_GENERATE_PASS_CHARS; ?>"><?php _e('Generate','cftp_admin'); ?></button>
        </div>
        <?php echo password_notes(); ?>
        
        <p><?php _e("Please enter your desired new password. After that, you will be able to log in normally.",'cftp_admin'); ?></p>

        <div class="inside_form_buttons">
            <button type="submit" name="submit" class="btn btn-wide btn-primary"><?php _e('Set new password','cftp_admin'); ?></button>
        </div>
    </fieldset>
</form>
