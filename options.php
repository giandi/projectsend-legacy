<?php
/**
 * Options page and form.
 *
 * @package ProjectSend
 * @subpackage Options
 */
$allowed_levels = array(9);
require_once('bootstrap.php');

$section = ( !empty( $_GET['section'] ) ) ? $_GET['section'] : $_POST['section'];

switch ( $section ) {
	case 'general':
		$section_title	= __('General options','cftp_admin');
		$checkboxes		= array(
								'footer_custom_enable',
								'files_descriptions_use_ckeditor',
								'use_browser_lang',
							);
		break;
	case 'clients':
		$section_title	= __('Clients','cftp_admin');
		$checkboxes		= array(
								'clients_can_register',
								'clients_auto_approve',
								'clients_can_upload',
								'clients_can_delete_own_files',
								'clients_can_set_expiration_date',
							);
		break;
	case 'privacy':
		$section_title	= __('Privacy','cftp_admin');
		$checkboxes		= array(
								'privacy_noindex_site',
								'enable_landing_for_all_files',
								'public_listing_page_enable',
								'public_listing_logged_only',
								'public_listing_show_all_files',
								'public_listing_use_download_link',
							);
		break;
	case 'email':
		$section_title	= __('E-mail notifications','cftp_admin');
		$checkboxes		= array(
								'mail_copy_user_upload',
								'mail_copy_client_upload',
								'mail_copy_main_user',
							);
		break;
	case 'security':
		$section_title	= __('Security','cftp_admin');
		$checkboxes		= array(
								'pass_require_upper',
								'pass_require_lower',
								'pass_require_number',
								'pass_require_special',
								'recaptcha_enabled',
							);
		break;
	case 'branding':
		$section_title	= __('Branding','cftp_admin');
		$checkboxes		= array(
							);
		break;
	case 'social_login':
		$section_title	= __('Social Login','cftp_admin');
		$checkboxes		= array(
							);
		break;
	default:
		$location = BASE_URI . 'options.php?section=general';
		header("Location: $location");
		exit;
		break;
}

$page_title = $section_title;

$active_nav = 'options';
include_once ADMIN_VIEWS_DIR . DS . 'header.php';

/* Logo */
$logo_file_info = generate_logo_url();

/** Form sent */
if ($_POST) {
	/**
	 * Escape all the posted values on a single function.
	 * Defined on functions.php
	 */
	/** Values that can be empty */
	$allowed_empty_values	= array(
								'mail_copy_addresses',
								'mail_smtp_host',
								'mail_smtp_port',
								'mail_smtp_user',
								'mail_smtp_pass',
							);

	if ( empty( $_POST['google_signin_enabled'] ) ) {
		$allowed_empty_values[] = 'google_client_id';
		$allowed_empty_values[] = 'google_client_secret';
	}
	if ( empty( $_POST['recaptcha_enabled'] ) ) {
		$allowed_empty_values[] = 'recaptcha_site_key';
		$allowed_empty_values[] = 'recaptcha_secret_key';
	}

	foreach ($checkboxes as $checkbox) {
		$_POST[$checkbox] = (empty($_POST[$checkbox]) || !isset($_POST[$checkbox])) ? 0 : 1;
	}

	$keys = array_keys($_POST);

	$options_total = count($keys);
	$options_filled = 0;
	$query_state = '0';

	/**
	 * Check if all the options are filled.
	 */
	for ($i = 0; $i < $options_total; $i++) {
		if (!in_array($keys[$i], $allowed_empty_values)) {
			if (empty($_POST[$keys[$i]]) && $_POST[$keys[$i]] != '0') {
				$query_state = '3';
			}
			else {
				$options_filled++;
			}
		}
	}

	/** If every option is completed, continue */
	if ($query_state == '0') {
		$updated = 0;
		for ($j = 0; $j < $options_total; $j++) {
			$save = $dbh->prepare( "UPDATE " . TABLE_OPTIONS . " SET value=:value WHERE name=:name" );
			$save->bindParam(':value', $_POST[$keys[$j]]);
			$save->bindParam(':name', $keys[$j]);
			$save->execute();

			if ($save) {
				$updated++;
			}
		}
		if ($updated > 0){
			$query_state = '1';
		}
		else {
			$query_state = '2';
		}
	}

	/** If uploading a logo on the branding page */
	$file_logo = $_FILES['select_logo'];
	if ( !empty( $file_logo ) ) {
		$logo = option_file_upload( $file_logo, 'image', 'logo_filename', 29 );
		$file_status = $logo['status'];
	}

	/** Redirect so the options are reflected immediatly */
	while (ob_get_level()) ob_end_clean();
	$section_redirect = html_output($_POST['section']);
	$location = BASE_URI . 'options.php?section=' . $section_redirect;

	if ( !empty( $query_state ) ) {
		$location .= '&status=' . $query_state;
	}

	if ( !empty( $file_status ) ) {
		$location .= '&file_status=' . $file_status;
	}
	header("Location: $location");
	die();
}

/**
 * Replace | with , to use the tags system when showing
 * the allowed filetypes on the form. This value comes from
 * site.options.php
*/
/** Explode, sort, and implode the values to list them alphabetically */
$allowed_file_types = explode('|',ALLOWED_FILE_TYPES);
sort($allowed_file_types);

/** If .php files are allowed, set the flag for the warning message */
if ( in_array( 'php', $allowed_file_types ) ) {
	$php_allowed_warning = true;
}

$allowed_file_types = implode(',',$allowed_file_types);

?>

<div class="col-xs-12 col-sm-12 col-lg-6">
	<?php
		if (isset($_GET['status'])) {
			switch ($_GET['status']) {
				case '1':
					$msg = __('Options updated succesfuly.','cftp_admin');
					echo system_message('success',$msg);
					break;
				case '2':
					$msg = __('There was an error. Please try again.','cftp_admin');
					echo system_message('danger',$msg);
					break;
				case '3':
					$msg = __('Some fields were not completed. Options could not be saved.','cftp_admin');
					echo system_message('danger',$msg);
					$show_options_form = 1;
					break;
			}
		}

		/** Logo uploading status */
		if (isset($_GET['file_status'])) {
			switch ($_GET['file_status']) {
				case '1':
					break;
				case '2':
					$msg = __('The file could not be moved to the corresponding folder.','cftp_admin');
					$msg .= __("This is most likely a permissions issue. If that's the case, it can be corrected via FTP by setting the chmod value of the",'cftp_admin');
					$msg .= ' '.ADMIN_UPLOADS_DIR.' ';
					$msg .= __('directory to 755, or 777 as a last resource.','cftp_admin');
					$msg .= __("If this doesn't solve the issue, try giving the same values to the directories above that one until it works.",'cftp_admin');
					echo system_message('danger',$msg);
					break;
				case '3':
					$msg = __('The file you selected is not an allowed format.','cftp_admin');
					echo system_message('danger',$msg);
					break;
				case '4':
					$msg = __('There was an error uploading the file. Please try again.','cftp_admin');
					echo system_message('danger',$msg);
					break;
			}
		}
	?>

	<div class="white-box">
		<div class="white-box-interior">

			<script type="text/javascript">
				$(document).ready(function() {
                    $('#allowed_file_types')
                    .tagify()
                    .on('add', function(e, tagName){
                        console.log('added', tagName)
                    });

					$("form").submit(function() {
						clean_form(this);

						is_complete_all_options(this,'<?php _e('Please complete all the fields.','cftp_admin'); ?>');

						// show the errors or continue if everything is ok
						if (show_form_errors() == false) { alert('<?php _e('Please complete all the fields.','cftp_admin'); ?>'); return false; }
					});
				});
			</script>

			<form action="options.php" name="optionsform" method="post" enctype="multipart/form-data" class="form-horizontal">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>" />
				<input type="hidden" name="section" value="<?php echo $section; ?>">

                <?php
                    $form_file = FORMS_DIR . DS . 'options' . DS . $section . '.php';
                    if (file_exists($form_file)) {
                        include_once $form_file;
                    }
                ?>

				<div class="options_divide"></div>

				<div class="after_form_buttons">
					<button type="submit" class="btn btn-wide btn-primary empty"><?php _e('Save options','cftp_admin'); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
	include_once ADMIN_VIEWS_DIR . DS . 'footer.php';
