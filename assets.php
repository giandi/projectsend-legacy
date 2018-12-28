<?php
/**
 * Generates the list of CSS and JS files to load
 * base on the $load_scripts array defined on each
 * page.
 *
 * @package ProjectSend
 */
global $load_css_files;
global $load_js_files;
global $load_compat_js_files;

$load_css_files			= array();
$load_js_files			= array();
$load_compat_js_files	= array();

/** Add the base files that every page will need, regardless of type */

/** JS */
$load_js_files[]	= ASSETS_LIB_URL . '/bootstrap/js/bootstrap.min.js';
$load_js_files[]	= ASSETS_LIB_URL . '/jen/jen.js';
$load_js_files[]	= ASSETS_LIB_URL . '/js-cookie/js.cookie-2.2.0.min.js';
$load_js_files[]	= ASSETS_JS_URL . '/jquery.validations.js';
$load_js_files[]	= ASSETS_JS_URL . '/jquery.psendmodal.js';
$load_js_files[]	= ASSETS_JS_URL . '/main.js';
$load_js_files[]	= ASSETS_JS_URL . '/js.functions.php';

/** CSS */

/** Fonts*/
$load_css_files[]	= 'https://fonts.googleapis.com/css?family=Open+Sans:400,700,300';
$load_css_files[]	= ASSETS_LIB_URL . '/font-awesome/css/font-awesome.min.css';

/**
 * Optional scripts
 */
if ( !empty( $load_scripts ) ) {
	foreach ( $load_scripts as $script ) {
		switch ( $script ) {
			case 'recaptcha':
				$load_js_files[]		= 'https://www.google.com/recaptcha/api.js';
				break;
			case 'social_login':
				$load_css_files[]		= ASSETS_CSS_URL . '/social-login.css';
				break;
			case 'datepicker':
				$load_css_files[]		= ASSETS_LIB_URL . '/bootstrap-datepicker/css/datepicker.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/bootstrap-datepicker/js/bootstrap-datepicker.js';
				break;
			case 'spinedit':
				$load_css_files[]		= ASSETS_LIB_URL . '/bootstrap-spinedit/css/bootstrap-spinedit.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/bootstrap-spinedit/js/bootstrap-spinedit.js';
				break;
			case 'footable':
				$footable_js_file		= ( !empty( $footable_min ) ) ? 'footable.min.js' : 'footable.all.min.js';
				$load_css_files[]		= ASSETS_LIB_URL . '/footable/css/footable.core.css';
				$load_css_files[]		= ASSETS_CSS_URL . '/footable.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/footable/' . $footable_js_file;
				break;
			case 'jquery_tags_input':
				$load_css_files[]		= ASSETS_LIB_URL . '/jquery-tags-input/jquery.tagsinput.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/jquery-tags-input/jquery.tagsinput.min.js';
				break;
			case 'chosen':
				$load_css_files[]		= ASSETS_LIB_URL . '/chosen/chosen.min.css';
				$load_css_files[]		= ASSETS_LIB_URL . '/chosen/chosen.bootstrap.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/chosen/chosen.jquery.min.js';
				break;
			case 'toggle':
				$load_css_files[]		= ASSETS_LIB_URL . '/bootstrap-toggle/css/bootstrap-toggle.min.css';
				$load_js_files[]		= ASSETS_LIB_URL . '/bootstrap-toggle/js/bootstrap-toggle.min.js';
				break;
			case 'plupload':
				$load_css_files[]		= ASSETS_LIB_URL . '/plupload/js/jquery.plupload.queue/css/jquery.plupload.queue.css';
				$load_js_files[]		= ASSETS_JS_URL . '/browserplus-min.js';
				$load_js_files[]		= ASSETS_LIB_URL . '/plupload/js/plupload.full.js';
				$load_js_files[]		= ASSETS_LIB_URL . '/plupload/js/jquery.plupload.queue/jquery.plupload.queue.js';
				/**
				 * Load a plupload translation file, if the ProjectSend language
				 * on sys.config.php is set to anything other than "en", and the
				 * corresponding plupload file exists.
				 */
				if ( LOADED_LANG != 'en' ) {
					$plupload_lang_file = 'plupload/js/i18n/'.LOADED_LANG.'.js';
					if ( file_exists( ASSETS_LIB_DIR . $plupload_lang_file ) ) {
						$load_js_files[] = ASSETS_LIB_URL . '/' . $plupload_lang_file;
					}
				}

				break;
			case 'flot':
				$load_js_files[]		= ASSETS_LIB_URL . '/flot/jquery.flot.min.js';
				$load_js_files[]		= ASSETS_LIB_URL . '/flot/jquery.flot.resize.min.js';
				$load_js_files[]		= ASSETS_LIB_URL . '/flot/jquery.flot.time.min.js';
				$load_compat_js_files[]	= array(
												'file'	=> ASSETS_LIB_URL . '/flot/excanvas.js',
												'cond'	=> 'lt IE 9',
											);
				break;
			case 'ckeditor':
				$load_js_files[]		= ASSETS_LIB_URL . '/ckeditor/ckeditor.js';
				break;
		}
	}
}

$load_css_files[]	= ASSETS_LIB_URL . '/bootstrap/css/bootstrap.min.css';
$load_css_files[]	= ASSETS_CSS_URL . '/main.min.css';
$load_css_files[]	= ASSETS_CSS_URL . '/mobile.min.css';

/**
 * Load a different css file when called from the default template.
 */
if ( isset( $this_template_css ) ) {
	$load_css_files[]	= $this_template_css;
}

/**
 * Custom CSS styles.
 * Possible locations: css/custom.css | assets/custom/custom.css
 */
$custom_css_locations = [ 'css/custom.css', 'assets/custom/custom.css' ];
foreach ( $custom_css_locations as $css_file ) {
	if ( file_exists ( ROOT_DIR . DS . $css_file ) ) {
		$load_css_files[]	= BASE_URI . $css_file;
	}
}


/**
 * Used on header to print the CSS files
 */
function load_css_files() {
	global $load_css_files;

	if ( !empty( $load_css_files ) ) {
		foreach ( $load_css_files as $file ) {
?>
			<link rel="stylesheet" media="all" type="text/css" href="<?php echo $file; ?>" />
<?php
		}
	}
}

/**
 * Custom JS files.
 * Possible locations: includes/js/custom.js | assets/custom/custom.js
 */
$custom_js_locations = [ 'includes/js/custom.js', 'assets/custom/custom.js' ];
foreach ( $custom_js_locations as $js_file ) {
	if ( file_exists ( ROOT_DIR . DS . $js_file ) ) {
		$load_js_files[]	= BASE_URI . $js_file;
	}
}

/**
 * Used before the </body> tag to print the JS files
 */
function load_js_files() {
	global $load_compat_js_files;
	global $load_js_files;

	if ( !empty( $load_compat_js_files ) ) {
		foreach ( $load_compat_js_files as $index => $info ) {
?>
			<!--[if <?php echo $info['cond']; ?>]><script language="javascript" type="text/javascript" src="<?php echo $info['file']; ?>"></script><![endif]-->
<?php
		}
	}

	if ( !empty( $load_js_files ) ) {
		foreach ( $load_js_files as $file ) {
?>
			<script src="<?php echo $file; ?>"></script>
<?php
		}
	}
}
