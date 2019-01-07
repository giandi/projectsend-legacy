<script type="text/javascript">
    $(document).ready(function() {
        $("form").submit(function() {
            clean_form(this);

            is_complete(this.reset_password_email, json_strings.validation.no_email);
            is_email(this.reset_password_email, json_strings.validation.invalid_email);

            // show the errors or continue if everything is ok
            if (show_form_errors() == false) { return false; }
        });
    });
</script>

<form action="reset-password.php" name="resetpassword" method="post" role="form">
    <fieldset>
        <input type="hidden" name="form_type" id="form_type" value="new_request" />

        <div class="form-group">
            <label for="reset_password_email"><?php _e('E-mail','cftp_admin'); ?></label>
            <input type="text" name="reset_password_email" id="reset_password_email" class="form-control" />
        </div>

        <p><?php _e("Please enter your account's e-mail address. You will receive a link to continue the process.",'cftp_admin'); ?></p>

        <div class="inside_form_buttons">
            <button type="submit" name="submit" class="btn btn-wide btn-primary"><?php _e('Get a new password','cftp_admin'); ?></button>
        </div>
    </fieldset>
</form>
