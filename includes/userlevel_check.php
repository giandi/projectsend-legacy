<?php
/**
 * Contains all the functions used to validate the current logged in
 * client or user.
 *
 * @package ProjectSend
 *
 */

/**
 * Used on header.php to check if there is an active session or valid
 * cookie before generating the content.
 * If none is found, redirect to the log in form.
 */
function check_for_session( $redirect = true )
{
	$is_logged_now = false;
	if (isset($_SESSION['loggedin'])) {
		$is_logged_now = true;
	}
	elseif (isset($_SESSION['access']) && $_SESSION['access'] == 'admin') {
		$is_logged_now = true;
	}
	if ( !$is_logged_now && $redirect == true ) {
		header("location:" . BASE_URI . "index.php");
	}
	return $is_logged_now;
}

/**
 * Used on header.php to check if the current logged in account is either
 * a system user or a client.
 *
 * Clients are then redirected to the index page, where another check is
 * performed and then a second redirection takes the client to the
 * correspondent file list.
 *
 * @see check_for_client
 */
function check_for_admin() {
	$is_logged_admin = false;
	if (isset($_SESSION['access']) && $_SESSION['access'] == 'admin') {
		$is_logged_admin = true;
	}
	if (!$is_logged_admin) {
	    ob_clean();
		header("location:" . BASE_URI . "index.php");
	}
    return $is_logged_admin;
}

/**
 * Used on the log in form page (index.php) to take the clients directly to their
 * files list.
 * Also used on the self-registration form (register.php).
 */
function check_for_client() {
	if (isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == '0') {
		header("location:my_files/");
		exit;
	}
}

/**
 * Used on header.php to check if the current logged in system user has the
 * permission to view this page.
 */
function can_see_content($allowed_levels) {
	$permission = false;
	if(isset($allowed_levels)) {
		/**
		 * Check for a session, and if found see if the user
		 * level is among those defined by the page.
		 *
		 * $allowed_levels in defined on each page before the inclusion of header.php
		*/
		if (isset($_SESSION['userlevel']) && in_array($_SESSION['userlevel'],$allowed_levels)) {
			$permission = true;
		}
		/**
		 * After the checks, if the user is allowed, continue.
		 * If not, show the "Not allowed message", then the footer, then die(); so the
		 * actual page content is not generated.
		*/
	}
	if (!$permission) {
        permission_denied_page('role');
    }
}

function permission_denied_page($error_type) {
    ob_end_clean();
    $page_title = __('Access denied','cftp_admin');
?>
        <!doctype html>
        <html lang="<?php echo SITE_LANG; ?>">
            <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1">
            
                <title><?php echo html_output( $page_title . ' &raquo; ' . THIS_INSTALL_TITLE ); ?></title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <?php meta_favicon(); ?>
                <script type="text/javascript" src="<?php echo ASSETS_LIB_URL; ?>/jquery/jquery.1.12.4.min.js"></script>
            
                <!--[if lt IE 9]>
                    <script src="<?php echo BASE_URI; ?>includes/js/html5shiv.min.js"></script>
                    <script src="<?php echo BASE_URI; ?>includes/js/respond.min.js"></script>
                <![endif]-->
                
                <?php
                    require_once( 'assets.php' );
            
                    load_css_files();
                ?>
            </head>
            <body class="backend forbidden">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <h2><?php echo $page_title; ?></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="whiteform whitebox">
                                <?php
                                    switch ($error_type) {
                                        case 'role':
                                            $msg = __("Your account type doesn't allow you to view this page. Please contact a system administrator if you need to access this function.",'cftp_admin');
                                        break;
                                        case 'csrf':
                                            $msg = __("The security token could not be validated.",'cftp_admin');
                                            break;
                                    }
                                    echo $msg;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
        </html>
<?php
    exit;
}