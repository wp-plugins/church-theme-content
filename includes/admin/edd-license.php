<?php/** * EDD License Handling * * Add-ons can register themselves in order to leverage Easy Digital Downloads Software Licensing features. * * See add-ons.php for the ctc_register_add_on() function. A registered add-on benefits from: * * 1. License Key field and activate/deactivate buttons in Church Theme Content Settings * 2. One-click updates via an Easy Digital Downloads store using Software Licensing add-on * 3. Admin notice when add-on license is inactive, expiring soon or expired * * Future: Possibly turn this into a class * * @package    Church_Theme_Content * @subpackage Admin * @copyright  Copyright (c) 2014 - 2015 churchthemes.com * @link       https://github.com/churchthemes/church-theme-content * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html * @since      1.2 */// No direct accessif ( ! defined( 'ABSPATH' ) ) exit;/******************************************* * SUPPORT *******************************************//** * Add-on supports EDD licensing * * @since 1.2 * @param string $add_on_dir Add-on to work with */function ctc_edd_license_supported( $add_on_dir ) {	$supported = false;	// Store URL is all that is required	// Everything else can be auto-detected	if ( ctc_get_add_on( $add_on_dir, 'store_url' ) ) {		$supported = true;	}	return apply_filters( 'ctc_edd_license_supported', $supported, $add_on_dir );}/************************************************* * SETTINGS *************************************************//** * Add license key fields to settings page * * A field for each registered add-on will be added to the settings page * * @since 1.2 * @param array $fields Settings fields in the section * @return array Modified setting fields with license key fields inserted */function ctc_edd_license_settings( $section ) {	// Get registered add-ons	$add_ons = ctc_get_add_ons();	// Any add-ons registered?	if ( $add_ons ) {		// Loop add-ons		$fields = array();		foreach ( $add_ons as $add_on ) {			// Add-on supports EDD licensing?			if ( ! ctc_edd_license_supported( $add_on['plugin_dir'] ) ) {				continue;			}			// Setting key			$key = $add_on['plugin_dir'] . '_license_key';			// License Key			$fields[$key] = array(									   /* translators: %1$s is add-on name */				'name'				=> sprintf( __( '%1$s License Key', 'church-theme-content' ), $add_on['name'] ),				'desc'				=> '',				'type'				=> 'text', // text, textarea, checkbox, radio, select, number				'checkbox_label'	=> '', //show text after checkbox //show text after checkbox				'options'			=> array(), // array of keys/values for radio or select				'default'			=> '', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value				'class'				=> '', // classes to add to input				'custom_content'	=> 'ctc_edd_license_key_field', // function for custom display of field input				'custom_sanitize'	=> 'ctc_edd_license_sanitize', // function to do additional sanitization			);		}		// Add fields to section		$section['fields'] = $fields;	}	// No add-ons, show a message	else {		// Create new line if have description already		if ( ! empty( $section['desc'] ) ) {			$section['desc'] .= "<br /><br />";		} else {			$section['desc'] = '';		}		// Append message saying no add-ons installed		$section['desc'] .= '<i>' . sprintf(								/* translators: %1$s is URL to Add-ons */								__( 'No add-ons for the Church Theme Content plugin have been installed.', 'church-theme-content' ),								'http://churchthemes.com/plugins/?utm_source=ctc&utm_medium=plugin&utm_campaign=add-ons&utm_content=settings'							) . '</i>';	}	return $section;}add_filter( 'ctps_section-licenses', 'ctc_edd_license_settings' );/** * Custom settings field for license key * * Show a custom settings field for license key entry, activation/renewal buttons, etc. * * This is a callback used in ctc_edd_license_settings() * * @since 1.2 * @param array $args Field arguments for the setting * @param array $data Field data prepared by field_content(), for use in field output * @return string HTML for field output */function ctc_edd_license_key_field( $args, $data ) {	// Get add-on	$add_on_dir = str_replace( '_license_key', '', $data['id'] );	// License key and status	$license = ctc_edd_license_key( $add_on_dir );	$status = ctc_edd_license_status( $add_on_dir ); // local status	// Renewal URL	$renewal_url = ctc_edd_license_renewal_url( $add_on_dir );	// Text Input	$html = '<input type="text" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" />';	// License info	$license_info = '';	// Licensed provided? Show button and information	if ( $license ) {		// Show Activate/Deactivate buttons unless expired (in that case show Renew alone)		if ( ! ctc_edd_license_expired( $add_on_dir ) ) {			// Activate Button (license is not active)			if ( ! ctc_edd_license_active( $add_on_dir ) ) {				$html .= '<input type="submit" class="button button-primary ctc-license-button ctc-license-activate-button" name="ctc_edd_license_activate[' . esc_attr( $add_on_dir ) . ']" value="' . esc_attr__( 'Activate License', 'church-theme-content' ). '" />';			}			// Deactivate Button (license is active)			else {				$html .= '<input type="submit" class="button button ctc-license-button ctc-license-deactivate-button" name="ctc_edd_license_deactivate[' . esc_attr( $add_on_dir ) . ']" value="' . esc_attr__( 'Deactivate License', 'church-theme-content' ) . '" />';			}		}		// Renew Button		// Show only if renewal URL provided and license is active or expired		if ( ctc_edd_license_renewal_url( $add_on_dir ) && ( ctc_edd_license_active( $add_on_dir ) || ctc_edd_license_expired( $add_on_dir ) ) ) {			$html .= '<a href="' . esc_url( $renewal_url ) . '" class="button button' . ( ctc_edd_license_expired( $add_on_dir ) || ctc_edd_license_expiring_soon( $add_on_dir ) ? '-primary' : '' ) . ' ctc-license-button ctc-license-renew-button" target="_blank" />' . __( 'Renew License', 'church-theme-content' ) . '</a>';	}		// Status label		//$license_info .= '<span class="ctc-license-info-label ctc-license-status-label">' . _x( 'Status:', 'license key', 'church-theme-content' ) . '</span> ';			// Status value			if ( ctc_edd_license_active( $add_on_dir ) ) {				// Active				$license_info .= '<span class="ctc-license-active">' . _x( 'Active', 'license key', 'church-theme-content' ) . '</span>';				// Expiring soon				if ( ctc_edd_license_expiring_soon( $add_on_dir ) ) {					$license_info .= ' / <span class="ctc-license-expiring-soon">' . _x( 'Expiring Soon', 'license status', 'church-theme-content' ) . '</span>';				}			} elseif ( ctc_edd_license_expired( $add_on_dir ) ) {				// Expired				$license_info .= '<span class="ctc-license-expired">' . _x( 'Expired', 'license key', 'church-theme-content' ) . '</span>';			} else {				// Inactive				$license_info .= '<span class="ctc-license-inactive">' . _x( 'Inactive', 'license key', 'church-theme-content' ) . '</span>';			}		// Expiration		// Show expiration only if license is active or expired, not just if have the data		if ( ctc_edd_license_expiration( $add_on_dir ) && ( ctc_edd_license_active( $add_on_dir ) || ctc_edd_license_expired( $add_on_dir ) ) ) {			// Expiration label			$license_info .= '<span class="ctc-license-info-label ctc-license-expiration-label">' . _x( 'Expiration:', 'license key', 'church-theme-content' ) . '</span> ';			// Expiration value			$license_info .= '<span class="ctc-license-expiration">' . esc_html( ctc_edd_license_expiration_formatted( $add_on_dir ) ) . '</span>';		}	}	// License key not provided	else {		$license_info .= '<p><span class="ctc-license-key-missing">' . __( 'Key Not Entered', 'church-theme-content' ) . '</span></p>';	}	// Append license info	if ( ! empty( $license_info ) ) {		$html .= '<p class="ctc-edd-license-info">' . $license_info . '</p>';	}	return $html;}/** * Sanitize license key setting * * Unset local status and expiration if changing key. * This is a callback used in ctc_edd_license_settings() * * @since 1.2 * @param string $new Key being saved * @param string $field Setting's field data * @return string Sanitized key */function ctc_edd_license_sanitize( $new, $field ) {	// Get add-on	$add_on_dir = str_replace( '_license_key', '', $field['id'] );	// Get old license key value	$old = ctc_edd_license_key( $add_on_dir );	// Unset local status as active and expiration date when changing key -- need to activate new key	if ( $old && $old != $new ) {		// Note: status and expiration stored in their own options		// Not in plugin settings array (easier management and these are not user settings)		delete_option( $add_on_dir . '_license_status' );		delete_option( $add_on_dir . '_license_expiration' );	}	return $new;}/******************************************* * LICENSE DATA (LOCAL) *******************************************//** * License key value * * License key is stored in plugin settings array * Note: license status and expiration are stored in options (they are not plugin settings) * * @since 1.2 * @param string $add_on_dir Add-on to work with * @param string $append Append string to base option name * @return string Option value */function ctc_edd_license_key( $add_on_dir ) {	// Get license key setting	$license_key = trim( ctc_setting( $add_on_dir . '_license_key' ) );	// Return filtered	return apply_filters( 'ctc_edd_license_key', $license_key, $add_on_dir );}/** * Get local license status * * Note if inactive, value is empty * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return string status active, expired or empty (inactive) */function ctc_edd_license_status( $add_on_dir ) {	$status = get_option( $add_on_dir . '_license_status' );	return apply_filters( 'ctc_edd_license_status', $status, $add_on_dir );}/** * License is locally active * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return bool True if active */function ctc_edd_license_active( $add_on_dir ) {	$active = false;	if ( 'active' == ctc_edd_license_status( $add_on_dir ) ) {		$active = true;	}	return apply_filters( 'ctc_edd_license_active', $active, $add_on_dir );}/** * License is locally inactive * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return bool True if inactive */function ctc_edd_license_inactive( $add_on_dir ) {	$inactive = false;	if ( ! ctc_edd_license_status( $add_on_dir ) ) {		$inactive = true;	}	return apply_filters( 'ctc_edd_license_inactive', $inactive, $add_on_dir );}/** * License is locally expired * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return bool True if expired */function ctc_edd_license_expired( $add_on_dir ) {	$expired = false;	if ( 'expired' == ctc_edd_license_status( $add_on_dir ) ) {		$expired = true;	}	return apply_filters( 'ctc_edd_license_expired', $expired, $add_on_dir );}/** * License is expiring soon * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return bool True if expiring within X days */function ctc_edd_license_expiring_soon( $add_on_dir ) {	$expiring_soon = false;	$expiration_data = ctc_edd_license_expiration_data( $add_on_dir );	if ( ! empty( $expiration_data['expiring_soon'] ) ) {		$expiring_soon = true;	}	return apply_filters( 'ctc_edd_license_expiring_soon', $expiring_soon, $add_on_dir );}/** * Set license expiration date locally * * Removes seconds so stored value is YYYY-MM-DD. * * @since 1.2 * @param string $add_on_dir Add-on to work with * @param string $expiration Remove expiration date value * @return string Expiration YYYY-MM-DD */function ctc_edd_license_update_expiration( $add_on_dir, $expiration ) {	// Only if have a value (old value better than no value)	if ( ! empty( $expiration ) ) {		// Remove seconds so stored value is YYYY-MM-DD		list( $expiration ) = explode( ' ', $expiration );		$expiration = trim( $expiration );		// Not an invalid key?		if ( $expiration != '1970-01-01' ) {			// Update local value			update_option( $add_on_dir . '_license_expiration', $expiration );		}	}}/** * Get license expiration date (local value) * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return string Expiration YYYY-MM-DD */function ctc_edd_license_expiration( $add_on_dir ) {	$expiration = get_option( $add_on_dir . '_license_expiration' );	return apply_filters( 'ctc_edd_license_expiration', $expiration, $add_on_dir );}/** * Show license expiration date (formatted) * * @since 1.2 * @param string $add_on_dir Add-on to work with * @param string Text to show if no date found * @return string Expiration date formatted */function ctc_edd_license_expiration_formatted( $add_on_dir, $none_text = false ) {	$expiration = ctc_edd_license_expiration( $add_on_dir );	$date = '';	if ( $expiration ) {		$date = date_i18n( get_option( 'date_format' ), strtotime( $expiration ) );	} elseif ( ! empty( $none_text ) ) {		$date = $none_text;	}	return apply_filters( 'ctc_edd_license_expiration_formatted', $date, $add_on_dir );}/** * Get expiration data * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return array date in various formats and whether it is expiring soon or not */function ctc_edd_license_expiration_data( $add_on_dir ) {	$data = array();	$data['expiration'] = ctc_edd_license_expiration( $add_on_dir );	$data['expiration_date'] = ctc_edd_license_expiration_formatted( $add_on_dir, _x( 'unknown date', 'license expiration', 'church-theme-content' ) );	$data['expiration_ts'] = ! empty( $data['expiration'] ) ? strtotime( $data['expiration'] ) : '';	$data['expiring_soon_days'] = ctc_get_add_on( $add_on_dir, 'expiring_soon_days' );	$data['expiring_soon_ts'] = time() + ( DAY_IN_SECONDS * $data['expiring_soon_days'] );	$data['expiring_soon'] = ( ! ctc_edd_license_expired( $add_on_dir ) && ! empty( $data['expiration_ts'] ) && $data['expiration_ts'] < $data['expiring_soon_ts'] ) ? true : false;	return apply_filters( 'ctc_edd_license_expiration_data', $data, $add_on_dir );}/******************************************* * ACTIVATION / DEACTIVATION *******************************************//** * Activate or deactivate a license key on-demand * * This function can be run anywhere; it is not hooked (see other functions below) * It is primarily for running via action after activate/deactivate button clicked. * * @since 1.2 * @param string $add_on_dir Add-on to work with when not executing via post * @param string $action Action when not executing via post (activate_license or deactivate_license) * @return string $result Result such as 'activate_success' or 'activate_fail' (not every action has result) */function ctc_edd_license_activation( $add_on_dir, $action ) {	$result = '';	// Add-on supports licensing?	if ( ! ctc_edd_license_supported( $add_on_dir ) ) {		return $result;	}	// Continue to activate or deactivate	if ( $action && $add_on_dir ) {		// Get license data		$license_data = ctc_edd_license_action( $add_on_dir, $action );		// Call action via API		if ( $license_data ) {			// If activated remotely, set local status; or set local status if was already active remotely -- keep in sync			if ( 'activate_license' == $action ) {				// Success				if ( 'valid' == $license_data->license || 'valid' == ctc_edd_license_check( $add_on_dir ) ) {					update_option( $add_on_dir . '_license_status', 'active' );					$result = 'activate_success';				}				// Failure - note error for next page load				else {					$result = 'activate_fail';				}			}			// If deactivated remotely, set local status; or set local status if was already inactive remotely -- keep in sync			elseif (				'deactivate_license' == $action				&& (					'deactivated' == $license_data->license					|| 'disabled' == $license_data->license // if disabled would return failed... (leaving this just in case)					|| 'failed' == $license_data->license // likely means deactivation failed because it's disabled					|| 'inactive' == ctc_edd_license_check( $add_on_dir )				)			) {				// Success or failure?				if ( 'deactivated' == $license_data->license ) {					$result = 'deactivate_success';				} else {					$result = 'deactivate_fail';				}				// Delete license status				delete_option( $add_on_dir . '_license_status' );			}			// Set current expiration locally			// Local will be synced to remote daily in case changes			if ( isset( $license_data->expires ) ) {				ctc_edd_license_update_expiration( $add_on_dir, $license_data->expires );			}		}	}	return $result;}/** * Detect activation/deactivation button click * * This is run on pre_update_option_* before settings are saved, but after sanitization. * update_option_* does not fire unless values have changed so it won't be useful. * * With this $_POST is available so it can be determoned which button clicked. * After that is determined, data is passed to ctc_edd_license_activation() * for actual activation (which happens after a redirect, so transient is used) * * @since 1.2 * @param array $settings New settings values to save * @param array $old_settings Settings values presently saved * @return array Settings to save */function ctc_edd_license_detect_user_action( $settings, $old_settings ) {	// Activate button clicked	if ( ! empty( $_POST['ctc_edd_license_activate'] ) ) {		$action = 'activate_license'; // for EDD Software Licensing API		$add_on_dir = key( $_POST['ctc_edd_license_activate'] );	}	// Deactivate button clicked	elseif ( ! empty( $_POST['ctc_edd_license_deactivate'] ) ) {		$action = 'deactivate_license'; // for EDD Software Licensing API		$add_on_dir = key( $_POST['ctc_edd_license_deactivate'] );	}	// Has action, add-on and license key?	if ( ! empty( $action ) && ! empty( $settings[$add_on_dir . '_license_key'] ) ) {		// Set transient with license key data		// ctc_edd_license_activation() will run this after redirect occurs		// It is done after everything is saved in case license key value changed		set_transient( 'ctc_edd_license_activation', array(			'action'		=> $action,			'add_on_dir'	=> $add_on_dir,		), 30 ); // expires when first run or after 30 seconds in case of timeout	}	// Always return value to be saved	// Otherwise plugin settings will be wiped	return $settings;}// Using pre_update_option instead of update_option action because update_option// will not fire unless values changed -- and we need $_POST to detect button actionadd_filter( 'pre_update_option_ctc_settings', 'ctc_edd_license_detect_user_action', 10, 2 );/** * Do activation/deactivation after user's button click * * This runs after settings have been saved. * It uses transient data set when ctc_edd_license_detect_user_action() is triggered. * This data is used to run the activation/deactivation routine. * * Not using update_option_* hook because it only runs when values changed. * * @since 1.2 * @global object $ctc_settings */function ctc_edd_license_activation_after_user_action() {	global $ctc_settings;	// Is this plugin settings page?	if ( ! $ctc_settings->is_settings_page() ) {		return;	}	// Get transient with add-on to activate license for	$activation_data = get_transient( 'ctc_edd_license_activation' );	// Is a an add-on's license to be activated/deactivated?	if ( ! empty( $activation_data ) ) {		// Delete transient so this is run once		delete_transient( 'ctc_edd_license_activation' );		// Prepare data		$add_on_dir = $activation_data['add_on_dir'];		$action = $activation_data['action'];		// Attempt activation/deactivation remotely and set the result locally		$result = ctc_edd_license_activation( $add_on_dir, $action );		// Activation result		if ( $result ) {			// Set transient so notice shows on next settings page load			set_transient( 'ctc_edd_license_activation_result', array(				'result'		=> $result,				'add_on_dir'	=> $add_on_dir,			), 15 ); // will be deleted after shown or in 15 seconds		}	}}add_action( 'ctps_after_save', 'ctc_edd_license_activation_after_user_action' );/** * Show notice on activation/deactivation success/failure * * @since 1.2 * @global object $ctc_settings */function ctc_edd_license_activation_result_notice() {	global $ctc_settings;	// Only on plugin settings	if ( ! $ctc_settings->is_settings_page() ) {		return;	}	// Have a result transient?	$result = get_transient( 'ctc_edd_license_activation_result' );	if ( $result ) {		// Get result data?		$add_on_dir = $result['add_on_dir'];		$result = $result['result'];		// Have result data?		if ( ! empty( $add_on_dir ) && ! empty( $result ) ) {			// Get notice message			$notice_message = ctc_get_add_on( $add_on_dir, $result . '_notice' );			// Output notice			if ( $notice_message ) {				// Show notice and hide "Settings saved." notice beneath				?>				<div id="ctc-license-notice-<?php echo esc_attr( $result ); ?>" class="<?php if ( preg_match( '/success/', $result ) ) : ?>updated<?php else : ?>error<?php endif; ?>">					<p>						<?php echo $notice_message; ?>					</p>				</div>				<style type="text/css">				#setting-error-settings_updated {					display: none;				}				</style>				<?php			}		}		// Delete transient after showing notice once		delete_transient( 'ctc_edd_license_activation_result' );	}}add_action( 'admin_notices', 'ctc_edd_license_activation_result_notice' );/******************************************* * STATUS NOTICES *******************************************//** * Show inactive, expiring soon and expired license notices * * @since 1.2 * @global object $ctc_settings */function ctc_edd_license_notices() {	global $ctc_settings;	// User can manage plugins?	// Keeps notices from showing to non-admin users	if ( ! current_user_can( 'install_plugins' ) ) {		return;	}	// Show only on relevant pages as not to overwhelm the admin	// Don't show on settings page (redundant on Licenses tab and irrelevants/distracting on others)	$screen = get_current_screen();	if ( ! in_array( $screen->base, array( 'dashboard', 'plugins', 'update-core' ) ) ) {		return;	}	// Get add-ons	$add_ons = ctc_get_add_ons();	// Have add-ons?	if ( $add_ons ) {		// Collect add-ons requiring a notice		$inactive_add_ons = array();		$expiring_soon_add_ons = array();		$expired_add_ons = array();		// Loop add-ons		foreach( $add_ons as $add_on_dir => $add_on ) {			// Add-on supports EDD licensing?			if ( ! ctc_edd_license_supported( $add_on_dir ) ) {				continue;			}			// Get expiration data			$expiration_data = ctc_edd_license_expiration_data( $add_on_dir );			// Active But Expiring Soon			// Show a reminder notice 30 days before expiration			if ( ctc_edd_license_active( $add_on_dir ) && $expiration_data['expiring_soon'] ) {				$expiring_soon_add_ons[$add_on_dir] = $add_on;			}			// Expired			// This shows as error not notice, since it has come to pass			elseif ( ctc_edd_license_expired( $add_on_dir ) ) {				$expired_add_ons[$add_on_dir] = $add_on;			}			// Inactive			elseif ( ! ctc_edd_license_active( $add_on_dir ) ) {				$inactive_add_ons[$add_on_dir] = $add_on;			}		}		// Output notices		echo ctc_edd_license_notice_content( 'inactive', $inactive_add_ons );		echo ctc_edd_license_notice_content( 'expired', $expired_add_ons );		echo ctc_edd_license_notice_content( 'expiring_soon', $expiring_soon_add_ons );	}}add_action( 'admin_notices', 'ctc_edd_license_notices', 8 ); // higher priority than regular notices (10) but lower than theme license notice (7)/** * Content for inactive, expiring soon and expired license notices * * @since 1.2 * @param string $notice Type of notice (inactive, expiring_soon, expired) * @param array $add_ons Add-ons to show notice for * @return string HTML output for admin_notice */function ctc_edd_license_notice_content( $notice, $add_ons ) {	$content = '';	// Count add-ons	$count = count( $add_ons );	// Have at least one?	if ( $count ) {		// Empty vars (only filled for single add-on)		$add_on_dir = '';		$expiration_date = '';		$renewal_url = '';		$renewal_info_url = '';		// One add-on		if ( 1 == $count ) {			// Message to use			$notice_key = $notice;			// Get first and only add-on in array			$first_add_on = array_shift( array_values( $add_ons ) );			// Add-on data			$add_on_dir = $first_add_on['plugin_dir'];			$add_on_names = $first_add_on['name']; // single name			$expiration_data = ctc_edd_license_expiration_data( $add_on_dir );			$expiration_date = $expiration_data['expiration_date'];			// URLs for add-on overriding default notices during registration			$renewal_url = ctc_edd_license_renewal_url( $add_on_dir );			$renewal_info_url = ctc_get_add_on( $add_on_dir, 'renewal_info_url' );		}		// Multiple add-ons		else {			// Message to use			$notice_key = $notice . '_multiple';			// Make list of add-on names			$i = 0;			$add_on_names = '';			foreach ( $add_ons as $add_on ) {				$i++;				// Separate with comma or "and"				if ( $i == $count ) {					/* translators: separator between last and second to last add-ons in inactive/expired admin notice (instead of comma) */					$add_on_names .= _x( ' and ', 'license notice', 'church-theme-content' );				} elseif ( 1 != $i ) {					/* translators: separator between add-on names in inactive/expired admin notice */					$add_on_names .= _x( ', ', 'license notice', 'church-theme-content' );				}				// Append name to list				$add_on_names .= '<strong>' . $add_on['name'] . '</strong>';			}		}		// Notice Messages		// These are generic for multiple add-ons from different providers		// They link to the Add-on Licenses settings page where the Renew buttons are highlighted		$notices =  array(			/* translators: %1$s is URL to add-on license settings, %2$s is name of add-on */			'inactive'					=> ctc_get_add_on( $add_on_dir, 'inactive_notice' ),			/* translators: %1$s is URL to add-on license settings, %2$s is names of add-ons */			'inactive_multiple'			=> __( '<strong>Add-on Licenses Inactive:</strong> <a href="%1$s">Activate Your Add-on Licenses</a> to enable updates for %2$s.', 'church-theme-content' ),			/* translators: %1$s is URL to add-on license settings, %2$s is name of add-on, %3$s is expiration date */			'expired'					=> ctc_get_add_on( $add_on_dir, 'expired_notice' ),			/* translators: %1$s is URL to add-on license settings, %2$s is names of add-ons */			'expired_multiple'			=> __( '<strong>Add-on Licenses Expired:</strong> <a href="%1$s">Renew Your Add-on Licenses</a> for %2$s to re-enable updates.', 'church-theme-content' ),			/* translators: %1$s is URL to add-on license settings, %2$s is name of add-on, %3$s is expiration date */			'expiring_soon'				=> ctc_get_add_on( $add_on_dir, 'expiring_soon_notice' ),			/* translators: %1$s is URL to add-on license settings, %2$s is names of add-ons */			'expiring_soon_multiple'	=> __( '<strong>Add-on Licenses Expiring Soon:</strong> <a href="%1$s">Renew Your Add-on Licenses</a> for %2$s to continue receiving updates.', 'church-theme-content' ),		);		// Have notice message		if ( isset( $notices[$notice_key] ) ) {			// Notice message			$message = $notices[$notice_key];			// Build notice HTML			$content .= '<div class="ctc-license-status-notice error">';			$content .= '	<p>';			$content .= sprintf(							$message,							admin_url( 'options-general.php?page=' . CTC_DIR ) . '#licenses',							$add_on_names, // or single name							// These are available only when the notice is for a single add-on							// Default notices should not use renewal URLs because they may not be available							// An add-on can, however, use the renewal URLs it provides							$expiration_date,							$renewal_url,							$renewal_info_url						);			$content .= '	</p>';			$content .= '</div>';		}	}	return apply_filters( 'ctc_edd_license_notice_content', $content, $notice, $add_ons );}/******************************************* * LICENSE RENEWAL *******************************************//** * Construct license renewal URL * * Replace {license_key} with license key * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return string Renewal URl with license key replaced */function ctc_edd_license_renewal_url( $add_on_dir ) {	// Get raw renewal URL	$renewal_url = ctc_get_add_on( $add_on_dir, 'renewal_url' );	// Have URL	if ( $renewal_url ) {		// Replace {license_key} with license key		$renewal_url = str_replace( '{license_key}', ctc_edd_license_key( $add_on_dir ), $renewal_url );	}	// Return filtered	return apply_filters( 'ctc_edd_license_renewal_url', $renewal_url, $add_on_dir );}/******************************************* * EDD SL API *******************************************//** * Call API with specific action * * https://easydigitaldownloads.com/docs/software-licensing-api/ * activate_license, deactivate_license or check_license * * @since 1.2 * @param string $add_on_dir Add-on to work with * @param string $action EDD API action: activate_license, deactivate_license or check_license * @return object License data from remote server */function ctc_edd_license_action( $add_on_dir, $action ) {	$license_data = array();	// Add-on supports EDD licensing	if ( ctc_edd_license_supported( $add_on_dir ) ) {		// Valid action?		$actions = array( 'activate_license', 'deactivate_license', 'check_license' );		if ( in_array( $action, $actions ) ) {			// Get license			$license = ctc_edd_license_key( $add_on_dir );			// Have license			if ( $license ) {				// Data to send in API request				$api_params = array(					'edd_action'	=> $action,					'license' 		=> $license,					'item_name'		=> urlencode( ctc_get_add_on( $add_on_dir, 'item_name' ) ), // name of download in EDD					'url'			=> urlencode( home_url() ) // URL of this site activated for license				);				// Call the API				$response = wp_remote_get( esc_url_raw( add_query_arg( $api_params, ctc_get_add_on( $add_on_dir, 'store_url' ) ) ), array( 'timeout' => 15, 'sslverify' => false ) );				// Got a valid response?				if ( ! is_wp_error( $response ) ) {					// Decode the license data					$license_data = json_decode( wp_remote_retrieve_body( $response ) );				}			}		}	}	return apply_filters( 'ctc_edd_license_action', $license_data, $add_on_dir, $action );}/** * Get remote license data * * Get status, expiration, etc. from remote * * @since 1.2 * @param string $add_on_dir Add-on to work with * @param string Optional key to get value for * @return array License data array or single value for key */function ctc_edd_license_check_data( $add_on_dir, $key = false ) {	// Get remote license data	$data = ctc_edd_license_action( $add_on_dir, 'check_license' );	// Convert data to array	$data = (array) $data;	// Get value for specific key?	if ( isset( $data[$key] ) ) { // key is given		// Value exists for key in object		if ( ! empty( $data[$key] ) ) {			$data = $data[$key];		}		// If key or value not found, return nothing		// (instead of full license data from above)		else {			$data = '';		}	}	return apply_filters( 'ctc_edd_license_check_data', $data, $add_on_dir, $key );}/** * Check license key status * * Check if license is valid on remote end. * * @since 1.2 * @param string $add_on_dir Add-on to work with * @return string Remote license status */function ctc_edd_license_check( $add_on_dir ) {	$status = ctc_edd_license_check_data( $add_on_dir, 'license' );	return apply_filters( 'ctc_edd_license_check', $status, $add_on_dir );}/** * Sync remote/local status * * It's handy to run this periodically in case license has been remotely activated, renewed or deactivated. * An expired license could have been renewed or a site URL addded remotely. * The license could have been expired, refunded or the URL no longer matches (whole site move). * * This also updates the expiration date locally. * * Otherwise, they may think they are up to date when they are not. * * @since 1.2 * @param string $add_on_dir Add-on to work with */function ctc_edd_license_sync( $add_on_dir ) {	// Plugin supports EDD licensing?	if ( ! ctc_edd_license_supported( $add_on_dir ) ) {		return;	}	// Get remote license data	$license_data = ctc_edd_license_check_data( $add_on_dir );	// Continue only if got a response	if ( ! empty( $license_data ) ) { // don't do anything if times out		// Get remote status		$status = isset( $license_data['license'] ) ? $license_data['license'] : false;		// Active remotely		// This will activate locally if had been inactive or expired locally		if ( 'valid' == $status ) {			// Activate locally			update_option( $add_on_dir . '_license_status', 'active' );		}		// Inactive remotely		elseif ( in_array( $status, array( 'inactive', 'site_inactive', 'disabled' ) ) ) { // status is not valid			// Deactivate locally			delete_option( $add_on_dir . '_license_status' );		}		// Expired remotely		elseif ( 'expired' == $status ) {			// Set status expired locally			update_option( $add_on_dir . '_license_status', 'expired' );		}		// Update expiration data		// This helps the user know when to renew		if ( isset( $license_data['expires'] ) ) {			ctc_edd_license_update_expiration( $add_on_dir, $license_data['expires'] );		}	}}/** * Sync remote/local status automatically * * Check for remote status change periodically on relevant pages: Dashboard, Updates, Plugins, Plugin Settings (CTC) * Check in real-time on Plugin Settings page so if remote change was made, they see it immediately as if in account. * * Once daily is enough to keep notice on dashboard and updates up to date without hammering remote server. * * @since 1.2 * @global object $ctc_settings */function ctc_edd_license_auto_sync() {	global $ctc_settings;	// Admin only	if ( ! is_admin() ) {		return;	}	// Periodically in relevant areas or always on Plugin Settings page (where license key status shown)	$screen = get_current_screen();	if ( ! $ctc_settings->is_settings_page() && ! in_array( $screen->base, array( 'dashboard', 'update-core', 'plugins' ) ) ) {		return;	}	// Get add-ons	$add_ons = ctc_get_add_ons();	// Had add-ons?	// No need to run if no add-ons registered	if ( ! $add_ons ) {		return;	}	// Has this been checked in last day or is it plugin settings page?	// Settings page always runs this	if ( get_transient( 'ctc_edd_license_auto_sync' ) && ! $ctc_settings->is_settings_page() ) {		return;	} else {		// Set transient to prevent check until next day		// Once per day is enough to keep notice on dashboard and updates pages without hammering remote server		set_transient( 'ctc_edd_license_auto_sync', true, DAY_IN_SECONDS );	}	// Loop add-ons	foreach( $add_ons as $add_on_dir => $add_on ) {		// Add-on supports EDD licensing?		if ( ! ctc_edd_license_supported( $add_on_dir ) ) {			return;		}		// Check remote status and sync both ways if necessary		ctc_edd_license_sync( $add_on_dir );	}}add_action( 'current_screen', 'ctc_edd_license_auto_sync' );/******************************************* * ONE-CLICK UPDATES *******************************************//** * Plugin updater * * Enable one-click updates for add-on plugins * * @since 1.2 */function ctc_edd_license_updater() {	// Get add-ons	$add_ons = ctc_get_add_ons();	// Have add-ons?	if ( $add_ons ) {		// Include Easy Digital Downloads Software Licensing plugin updater class		if ( ! class_exists( 'CTC_EDD_SL_Plugin_Updater' ) ) {			require CTC_PATH . '/' . CTC_CLASS_DIR . '/CTC_EDD_SL_Plugin_Updater.php';		}		// Loop add-ons		foreach ( $add_ons as $add_on_dir => $add_on ) {			// Supports licensing and updates?			if ( ! ctc_edd_license_supported( $add_on_dir ) || empty( $add_on['updates'] ) ) {				continue;			}			// License is active?			if ( ! ctc_edd_license_active( $add_on_dir ) ) {				continue;			}			// Get license key			$license_key = ctc_edd_license_key( $add_on_dir );			// Make sure we have the data necessary for using the updater class			if (				$license_key				&& ! empty( $add_on['store_url'] )				&& ! empty( $add_on['item_name'] )				&& ! empty( $add_on['author'] )			) {				// Activate one-click updates				$edd_updater = new CTC_EDD_SL_Plugin_Updater(					$add_on['store_url'],						// Store URL running EDD with Software Licensing extension					$add_on['plugin_file'],						// Full path to main plugin file (ie. __FILE__ in that file)					array(						'version' 	=> $add_on['version'], 		// Current version of the add-on plugin						'license' 	=> $license_key, 			// The license key entered by user in plugin settings						'item_name' => $add_on['item_name'], 	// The name of the add-on plugin (must match title of download in EDD exactly)						'author' 	=> $add_on['author'],  		// The plugin author's name					)				);			}		}	}}add_action( 'admin_init', 'ctc_edd_license_updater', 0 ); // after add-on registration on plugins_loaded, but before other things