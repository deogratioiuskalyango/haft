<?php
/**
 * Error template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-builder-provider-connections-error hidden">
	<span class="wpforms-builder-provider-connections-error-message"><?php esc_html_e( 'Something went wrong while performing an AJAX request.', 'wpforms-google-sheets' ); ?></span>
</div>
