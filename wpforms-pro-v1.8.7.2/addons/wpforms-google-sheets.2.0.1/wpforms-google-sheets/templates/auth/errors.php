<?php
/**
 * General authorization error.
 *
 * @since 1.0.0
 *
 * @var array $redirect_uris List of redirect URIs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-google-sheets-auth-required-error error form-error" style="display: none;">
	<?php esc_html_e( 'Please provide valid Google Client ID and Google Client Secret.', 'wpforms-google-sheets' ); ?>
</div>
<div class="wpforms-google-sheets-auth-error error form-error" style="display: none;">
	<?php esc_html_e( 'Something went wrong while performing an AJAX request.', 'wpforms-google-sheets' ); ?>
</div>
