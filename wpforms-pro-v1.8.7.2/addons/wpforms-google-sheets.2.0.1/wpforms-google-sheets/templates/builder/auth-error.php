<?php
/**
 * Auth notice template.
 *
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpforms-alert wpforms-alert-danger wpforms-alert-dismissible">
	<div class="wpforms-alert-message">
		<p>
			<?php esc_html_e( 'Your Google account connection has expired. Please reconnect your account.', 'wpforms-google-sheets' ); ?>
		</p>
	</div>

	<div class="wpforms-alert-buttons">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo wpforms_render( WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/sign-in' );
		?>
		<input type="hidden" value="{{ data.reauthUrl }}">
	</div>
</div>
