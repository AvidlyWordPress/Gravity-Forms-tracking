<?php
/**
 * Plugin Name: Gravity Forms tracking by Avidly
 * Description: Force GF forms to use AJAX and pass data to Google Tag Manager for tracking.
 * Version: 1.1.0
 * Author: Avidly
 * Author URI: http://avidly.fi
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package GF_Track_GTM
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Hook functionality.
 */
add_action( 'init', 'avidly_gmt_track_textdomain' );
add_filter( 'gform_form_args', 'avidly_gmt_track_gform_force_ajax' );
add_filter( 'gform_confirmation', 'avidly_gmt_track_gform_confirmation', 10, 4 );

/**
 * Plugin translations.
 */
function avidly_gmt_track_textdomain() {
	load_plugin_textdomain( 'avidly-gtm-track', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

/**
 * Force all forms to use AJAX.
 *
 * @param array $form_args default form arguments.
 *
 * @return $form_args.
 */
function avidly_gmt_track_gform_force_ajax( $form_args ) {
	$form_args['ajax'] = true;
	return $form_args;
}


/**
 * Modify GF Confirmation for Ajax forms.
 *
 * @param string $confirmation message.
 * @param array $form information.
 * @param array $entry information.
 * @param bool $ajax detect AJAX using.
 *
 * @return $form_args.
 */
function avidly_gmt_track_gform_confirmation( $confirmation, $form, $entry, $ajax ) {

	$redirect_url = isset( $confirmation['redirect'] ) ? esc_url_raw( $confirmation['redirect'] ) : false;

	// Redirect/thank you page becomes as an array so we need to override it to a default string base confirmation (works like confirmation[type]=text).
	if ( $redirect_url ) {
		$confirmation = sprintf(
			'<div id="gf_%1$s" class="gform_anchor" tabindex="-1"></div><div id="gform_confirmation_wrapper_%1$s" class="gform_confirmation_wrapper "><div id="gform_confirmation_message_%1$s" class="gform_confirmation_message_%1$s gform_confirmation_message">%2$s</div></div>',
			(int) $form['id'],
			esc_html_x( 'Redirecting...', 'redirect confirmation', 'avidly-gf-tracking' )
		);
	}

	// Add custom dataLayer script after confirmation message.
	$confirmation .= avidly_gmt_track_gform_inline_script_tag( $form, $redirect_url );

	return $confirmation;
}


/**
 * Set inline script for confimation message.
 *
 * @param [type] $form
 * @param boolean/string $redirect redirect URL, false if not exist.
 * @return void
 */
function avidly_gmt_track_gform_inline_script_tag( $form, $redirect = false ) {
	return GFCommon::get_inline_script_tag( 
		sprintf(
			"window.top.jQuery(document).on( 'gform_confirmation_loaded', function ( event, formID, formName ) {
					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push({
						event    : '%s',
						formID   : '%s',
						formName : '%s',
						eventCallback: (id) =>  { %s },
    					eventTimeout: 2000,
					});
				}
			);",
			'GFormSubmission', // event.
			(int) $form['id'], // formID.
			esc_html( $form['title'] ), // formName.
			( $redirect ) ? "window.open( '$redirect', '_parent' );" : "" // Do the redirect if URL is set.
		)
	);
}
