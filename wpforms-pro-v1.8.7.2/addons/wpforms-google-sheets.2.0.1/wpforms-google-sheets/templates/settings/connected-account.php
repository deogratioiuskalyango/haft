<?php
/**
 * Connected account template.
 *
 * @since 1.0.0
 *
 * @var string $reauth_url        Reconnect account URL.
 * @var string $account_name      Account name.
 * @var string $account_connected Connected time.
 * @var string $account_id        Account ID.
 * @var string $slug              Provider slug.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<li class="wpforms-clear">
	<?php if ( $reauth_url ) : ?>
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
				<input type="hidden" value="<?php echo esc_url( $reauth_url ); ?>">
			</div>
		</div>
	<?php endif; ?>

	<span class="label">
		<?php echo wp_kses( $account_name, [ 'em' => [] ] ); ?>
	</span>

	<span class="date">
		<?php
		echo esc_html(
			sprintf( /* translators: %1$s - Connection date. */
				__( 'Connected on: %1$s', 'wpforms-google-sheets' ),
				$account_connected
			)
		);
		?>
	</span>

	<span class="remove">
		<a href="#"
		   data-provider="<?php echo esc_attr( $slug ); ?>"
		   data-key="<?php echo esc_attr( $account_id ); ?>">
			<?php esc_html_e( 'Disconnect', 'wpforms-google-sheets' ); ?>
		</a>
	</span>
</li>
