<?php
/**
 * Advanced auth form template.
 *
 * @since 1.0.0
 *
 * @var array $redirect_uris List of redirect URIs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p class="wpforms-google-sheets-setting-field-credentials">
	<input type="text"
		   name="client_id"
		   class="wpforms-required"
		   placeholder="<?php esc_attr_e( 'Google Client ID *', 'wpforms-google-sheets' ); ?>">

	<input type="text"
		   name="client_secret"
		   class="wpforms-required"
		   placeholder="<?php esc_attr_e( 'Google Client Secret *', 'wpforms-google-sheets' ); ?>">
</p>

<div class="wpforms-google-sheets-setting-field-redirect-uri">
	<label>
		<?php echo esc_html__( 'Callback URLs', 'wpforms-google-sheets' ); ?>
	</label>

	<?php foreach ( $redirect_uris as $key => $redirect_uri ) { ?>
		<div class="wpforms-google-sheets-setting-field-redirect-uri-row">
			<input type="url"
				   name="redirect_uri"
				   class="wpforms-google-sheets-setting-field-redirect-uri-input"
				   value="<?php echo esc_url( $redirect_uri ); ?>"
				   readonly="">

			<button type="button"
					class="wpforms-google-sheets-setting-field-redirect-uri-copy js-wpforms-google-sheets-setting-field-redirect-uri-copy">
				<span class="wpforms-google-sheets-setting-field-redirect-uri-copy-icon wpforms-google-sheets-setting-field-redirect-uri-copy-icon-copy" aria-hidden="true"></span>
				<span class="wpforms-google-sheets-setting-field-redirect-uri-copy-icon wpforms-google-sheets-setting-field-redirect-uri-copy-icon-copied" aria-hidden="true"></span>
			</button>
		</div>
	<?php } ?>
</div>
<p>
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo wpforms_render( WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/sign-in' );
	?>
</p>

<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo wpforms_render( WPFORMS_GOOGLE_SHEETS_PATH . 'templates/auth/errors' );
