<?php
/**
 * Plugin Name: Gravity Forms tracking by Avidly
 * Description: Force GF forms to use AJAX and pass data to Google Tag Manager for tracking.
 * Version: 1.0
 * Author: Avidly
 * Author URI: http://avidly.fi
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package GF_Track_GTM
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

/**
 * Force all forms to use AJAX.
 *
 * @param array $form_args default form arguments.
 *
 * @return $form_args.
 */
add_filter(
	'gform_form_args',
	function( $form_args ) {
		$form_args['ajax'] = true;
		return $form_args;
	}
);

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
add_filter(
	'gform_confirmation',
	function( $confirmation, $form, $entry, $ajax ) {

		if ( $ajax && is_string( $confirmation ) ) {
			// Modify dataLayer for Ajax forms.
			$form_submission = sprintf(
				"<script>
				window.top.jQuery(document).on( 'gform_confirmation_loaded', function ( event, formID, formName ) {
						window.dataLayer = window.dataLayer || [];
						window.dataLayer.push({
							event    : '%s',
							formID   : '%s',
							formName : '%s'
						});
					}
				);

				</script>",
				'GFormSubmission', // event.
				$form['id'], // formID.
				$form['title'] // formName.
			);

			$confirmation .= $form_submission;
			return $confirmation;

		} else {

			//if redirect/thank you page is configured we just want $confirmation because it becomes an array
			return $confirmation;
			
		}
	},
	10,
	4
);
